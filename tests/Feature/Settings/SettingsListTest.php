<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('has settings list', function () {
    $response = $this->getJson('/api/settings');

    $response->assertStatus(200);
});

it('returns settings data', function () {
    $response = $this->getJson('/api/settings');

    $response->assertJsonStructure([
        'data' => [
            'configs',
        ],
    ]);
});

it('returns the default config file setting', function () {

    setting()->set('default_file', 'some_value');
    setting()->save();

    $response = $this->getJson('/api/settings');

    $response->assertJson([
        'data' => [
            'configs' => [
                'default' => [
                    'file' => 'some_value',
                ],
            ],
        ],
    ]);
});

it('returns the per-tag config files setting', function () {

    setting()->set('files', json_encode(['some_tag' => 'some_value']));
    setting()->save();

    $response = $this->getJson('/api/settings');

    $response->assertJson([
        'data' => [
            'configs' => [
                'files' => [
                    'some_tag' => 'some_value',
                ],
            ],
        ],
    ]);
});

it('handles invalid or missing settings', function () {
    setting()->forget('default_file');
    setting()->forget('files');

    $response = $this->getJson('/api/settings');

    $response->assertJson([
        'data' => [
            'configs' => [],
        ],
    ]);
});

it('handles JSON decoding errors', function () {
    setting()->set('files', ' invalid_json');
    setting()->save();

    $response = $this->getJson('/api/settings');

    $response->assertJson([
        'data' => [
            'configs' => [
                'files' => [],
            ],
        ],
    ]);
});
