<?php

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

it('should return a list of tags', function () {
    $tag = Tag::factory()->create();

    $response = $this->get(URL::route('tags.index'));

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonFragment([
        'id' => $tag->id,
        'name' => $tag->name,
    ]);
});

// should return a list of tags with counts if request has hosts_count

it('should return a list of tags with counts if request has hosts_count', function () {
    $tag = Tag::factory()->create();

    $response = $this->get(URL::route('tags.index', ['include' => 'hosts_count']));

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $tag->id,
        'name' => $tag->name,
        'hosts_count' => 0,
    ]);
});

it('should create a new tag', function () {
    $data = [
        'name' => 'New Tag',
    ];

    $response = $this->post(URL::route('tags.store'), $data);

    $response->assertCreated();
    $response->assertJsonFragment([
        'id' => Tag::latest()->first()->id,
        'name' => 'New Tag',
    ]);
});

it('should return a single tag', function () {
    $tag = Tag::factory()->create();

    $response = $this->get(URL::route('tags.show', $tag));

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $tag->id,
        'name' => $tag->name,
    ]);
});

it('should update a tag', function () {
    $tag = Tag::factory()->create();

    $data = [
        'name' => 'Updated Tag',
    ];

    $response = $this->put(URL::route('tags.update', $tag), $data);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $tag->id,
        'name' => 'Updated Tag',
    ]);
});

it('should delete a tag', function () {
    $tag = Tag::factory()->create();

    $response = $this->delete(URL::route('tags.destroy', $tag));

    $response->assertNoContent();
    $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
});
