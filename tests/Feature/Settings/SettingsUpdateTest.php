<?php

use App\Events\SettingsUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

it('saves the default config file setting', function () {
    $response = $this->postJson('/api/settings', [
        'configs' => [
            'default' => [
                'file' => 'new_file',
            ],
        ],
    ]);

    $response->assertStatus(204);

    $rawSettings = setting()->all();
    $this->assertEquals('new_file', $rawSettings['default_file']);
});

it('saves the per-tag config files setting', function () {
    $response = $this->postJson('/api/settings', [
        'configs' => [
            'default' => [
                'file' => 'new_file',
            ],
            'files' => [
                'tag1' => 'tag1_file',
                'tag2' => 'tag2_file',
            ],
        ],
    ]);

    $response->assertStatus(204);

    $rawSettings = setting()->all();
    $this->assertEquals(json_encode([
        'tag1' => 'tag1_file',
        'tag2' => 'tag2_file',
    ]), $rawSettings['files']);
});

it('handles invalid or missing settings', function () {
    $response = $this->postJson('/api/settings', []);

    $response->assertStatus(422);
});

it('dispatches the SettingsUpdated event', function () {
    Event::fake();

    $response = $this->postJson('/api/settings', [
        'configs' => [
            'default' => [
                'file' => 'new_file',
            ],
        ],
    ]);

    $response->assertStatus(204);
    Event::assertDispatched(SettingsUpdated::class);
});
