<?php

use App\Models\Host;
use App\Models\Tag;

function generateHostBasicDirectives(string $domain, array $extraConfigs = []): array
{
    $directives = [];

    $faker = \Faker\Factory::create();

    $list = [
        'ServerName' => $domain,
        'DocumentRoot' => '"/var/www/html/'.$faker->randomNumber().'"',
        '_addr_post' => '*.80',
    ];

    $list = array_merge($list, $extraConfigs);

    foreach ($list as $key => $value) {
        $directives[] = [
            'directive' => $key,
            'value' => $value,
        ];
    }

    return $directives;
}

function createSampleHost(array $extraConfigs = []): Host
{
    $host = Host::factory()->create();
    $directives = generateHostBasicDirectives($host->domain, $extraConfigs);
    $host->configs()->createMany($directives);

    $tags = Tag::factory()->count(3)->create();
    $host->tags()->attach($tags);

    return $host;
}
