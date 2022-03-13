<?php

namespace App\Providers;

use App\VirtualHost\VirtualHostModelParser;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // TODO: replace with a module
        VirtualHostModelParser::addModifier(function($host, $entries){
            if ($host->tags()->exists()) {
                $tags = $host->tags;
                foreach ($tags as $tag) {
                    // Per Vhost php version
                    if (strlen($tag->name) == 5 && substr($tag->name, 0, 3) == 'php') {
                        $version = substr($tag->name, 3, 2);
                        if (is_numeric($version)) {
                            $entries[] = "\t" .'<Proxy "fcgi://127.0.0.1:90' . $version . '">
                            ProxySet timeout=3600
                          </Proxy>
                          <FilesMatch "\.php$">
                          SetHandler proxy:fcgi://127.0.0.1:90' . $version . '
                        </FilesMatch>' . PHP_EOL;
                        }
                    }
                }
            }
            
            return $entries;
        }, 5);
    }
}
