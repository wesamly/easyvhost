<?php

use App\Models\Host;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class, WithFaker::class);

it('can create a new host', function () {
    $domain = $this->faker->domainName();
    $configFile = '/httpd-conf-dir/httpd-vhosts.conf';

    Storage::fake('local');
    Storage::fake('local')->put($configFile, '');

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
    // TODO: assert file content match the added virtual host
    Storage::disk('local')->assertExists($configFile);
});
