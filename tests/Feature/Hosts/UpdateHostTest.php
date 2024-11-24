<?php

use App\Models\Host;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('can update host', function () {

    // Create Host
    $host = createSampleHost(['ErrorLog' => '"/path/to/error.log"']);

    $domain = $host->domain;
    $configFile = 'httpd-vhosts.conf';

    Storage::fake('vhosts_dir');
    $disk = Storage::disk('vhosts_dir');
    $disk->put($configFile, '');

    setting()->forgetAll();
    setting(['default_file' => $configFile])->save();

    // Update Host
    $newDocRoot = '"/var/www/html/updated-document-root"';
    $newTags = Tag::factory()->count(3)->create();
    $configs = array_column(generateHostBasicDirectives($domain), 'value', 'directive');
    $configs['DocumentRoot'] = $newDocRoot;
    $configs['ServerAdmin'] = 'admin@example.com'; // New Config
    // ErrorLog entry is removed

    $data = [
        'domain' => $domain,
        'config' => $configs,
        'tags' => $newTags->pluck('id')->toArray(),
    ];

    $this->patchJson('/api/hosts/'.$host->id, $data)
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'domain',
                'configs' => [
                    [
                        'id',
                        'host_id',
                        'directive',
                        'value',
                    ],
                ],
                'doc_root_exists',
            ],
        ]);

    // Assert tags assigned
    $host = Host::where('domain', $domain)->first();
    expect($host->tags->pluck('id')->toArray())->toEqual($newTags->pluck('id')->toArray());
    // Assert DocumentRoot directive updated
    expect($host->configs()->where('directive', 'DocumentRoot')->first()->value)->toEqual($newDocRoot);

    $content = Storage::disk('vhosts_dir')->get($configFile);

    expect($content)->toContain($newDocRoot);
});
