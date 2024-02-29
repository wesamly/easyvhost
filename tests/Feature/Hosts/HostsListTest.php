<?php

use App\Models\Host;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can list hosts', function () {

    $hosts = Host::factory()->count(3)->create();

    foreach ($hosts as $host) {
        $directives = generateHostBasicDirectives($host->domain);
        $host->configs()->createMany($directives);
    }

    $this->getJson('/api/hosts')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                [
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
            ],
        ]);
});

it('returns hosts with requested configs data', function () {

    /** @var \Tests\TestCase $this */
    $hosts = Host::factory()->count(3)->create();

    foreach ($hosts as $host) {
        $directives = generateHostBasicDirectives($host->domain);
        $host->configs()->createMany($directives);
    }
    $configs = ['ServerName', 'DocumentRoot'];

    /** @var \Illuminate\Testing\TestResponse $response */
    $response = $this->getJson('/api/hosts?configs='.implode(',', $configs));

    $response->assertOk();
    $data = $response->json('data');

    $entry = $data[0];

    foreach ($entry['configs'] as $config) {
        $this->assertContains($config['directive'], $configs);
    }

});

it('returns hosts with associated tags', function () {

    /** @var \Tests\TestCase $this */
    $hosts = Host::factory()->count(3)->create();
    $tags = Tag::factory()->count(3)->create();

    $hosts[0]->tags()->attach($tags);
    $hosts[1]->tags()->attach($tags);
    $hosts[2]->tags()->attach($tags);

    foreach ($hosts as $host) {
        $directives = generateHostBasicDirectives($host->domain);
        $host->configs()->createMany($directives);
    }

    /** @var \Illuminate\Testing\TestResponse $response */
    $response = $this->getJson('/api/hosts?tags=true');

    $response->assertOk();
    $data = $response->json('data');

    $entry = $data[0];

    $this->assertArrayHasKey('tag_ids', $entry);

});
