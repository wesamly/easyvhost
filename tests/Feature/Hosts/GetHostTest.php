<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

it('can get host details', function () {

    $host = createSampleHost();

    $this->getJson('/api/hosts/'.$host->id)
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
                'tags' => [
                    ['id', 'name'],
                ],
            ],
        ]);

});
