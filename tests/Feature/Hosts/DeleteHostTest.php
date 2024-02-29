<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

it('can delete a host', function () {

    $host = createSampleHost();

    $this->deleteJson('/api/hosts/'.$host->id)
        ->assertNoContent();

    $this->assertDatabaseMissing('hosts', [
        'id' => $host->id,
    ]);
});
