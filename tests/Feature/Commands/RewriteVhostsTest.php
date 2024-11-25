<?php

use App\Models\Host;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    createSampleHost();
});

it('should rewrite virtual hosts configs', function () {

    Storage::fake('vhosts_dir');
    $disk = Storage::disk('vhosts_dir');
    $configFile = 'httpd-vhosts-rewrite.conf';
    $disk->put($configFile, '<VirtualHost *.80></VirtualHost>');

    setting()->forgetAll();
    setting(['default_file' => $configFile])->save();

    // Get first host
    $host = Host::first();

    $this->artisan('vhosts:rewrite');

    // Get host ServerName directive
    $serverName = $host->configs()->where('directive', 'ServerName')->first()->value;

    $content = $disk->get($configFile);

    expect($content)->toContain($serverName);
});

it('should return 0', function () {
    $this->artisan('vhosts:rewrite')
        ->assertExitCode(0);
});
