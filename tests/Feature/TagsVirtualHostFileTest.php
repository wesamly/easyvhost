<?php

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('can write host configs to file with tags specified in setting', function () {
    $configFile = 'httpd-vhosts-tag1.conf';
    $host = createSampleHost(['ErrorLog' => '"/path/to/error.log"']);
    $tag = Tag::factory()->create();
    $host->tags()->attach($tag);

    setting()->set('files', json_encode([
        [
            'file' => $configFile,
            'tags' => [['id' => $tag->id, 'name' => $tag->name]],
        ],
    ]));
    setting()->save();

    Storage::fake('vhosts_dir');
    $disk = Storage::disk('vhosts_dir');
    $disk->put($configFile, '');

    // force rewrite
    $this->artisan('vhosts:rewrite');

    // Get host ServerName directive
    $serverName = $host->configs()->where('directive', 'ServerName')->first()->value;

    $content = Storage::disk('vhosts_dir')->get($configFile);

    expect($content)->toContain($serverName);
});
