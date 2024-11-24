<?php

use App\Models\Host;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class, WithFaker::class);

it('can create a new host', function () {
    $domain = $this->faker->domainName();

    Storage::fake('vhosts_dir');
    $disk = Storage::disk('vhosts_dir');
    $configFile = $disk->path('/httpd-vhosts-create.conf');
    // File must exist
    $disk->put($configFile, '<VirtualHost *.80></VirtualHost>');

    setting()->forgetAll();
    setting(['default_file' => $configFile])->save();

    $tags = Tag::factory()->count(3)->create();

    $data = [
        'domain' => $domain,
        'config' => array_column(generateHostBasicDirectives($domain), 'value', 'directive'),
        'tags' => $tags->pluck('id')->toArray(),
    ];

    $this->postJson('/api/hosts', $data)
        ->assertCreated()
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
    expect($host->tags->pluck('id')->toArray())->toEqual($tags->pluck('id')->toArray());

    // Assert file content match the added virtual host
    $content = $disk->get($configFile);

    unset($data['config']['_addr_port']);
    foreach ($data['config'] as $directive => $value) {
        expect($content)->toContain($directive.' '.$value);
    }
});
