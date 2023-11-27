<?php

namespace App\Providers;

use App\VirtualHost\VirtualHostModelParser;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;

class ModifierServiceProvider extends ServiceProvider
{
    /**
     * Register any modifier services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any modifier services.
     *
     * @return void
     */
    public function boot()
    {
        $finder = new Finder();
        $modifiers = $finder->in(base_path('modifiers'))->depth(0)->files()->name('*.php');

        foreach ($modifiers as $file) {
            $modifier = require $file->getRealPath();
            if (is_callable($modifier['callable'])) {
                VirtualHostModelParser::addModifier($modifier['callable'], $modifier['priority'] ?? 10);
            }
        }

    }
}
