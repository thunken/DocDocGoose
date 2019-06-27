<?php

namespace Thunken\DocDocGoose;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider as BaseProvider;
use Thunken\DocDocGoose\Tools\Extractor;

class DocDocGooseProvider extends BaseProvider
{

    public function boot()
    {
        AliasLoader::getInstance([
            'Extractor' => \Thunken\DocDocGoose\Facades\Extractor::class
        ]);

        // Config
        $this->publishes([
            __DIR__.'/config/docdocgoose.php' => $this->app->configPath('docdocgoose.php'),
        ]);

        // Views
        $this->loadViewsFrom(__DIR__.'/views', 'docdocgoose');
        $this->publishes([
            __DIR__.'/views' => $this->app->resourcePath('views/vendor/docdocgoose'),
        ]);
    }

    public function register()
    {
        // Config
        $this->mergeConfigFrom(
            __DIR__.'/config/docdocgoose.php', 'docdocgoose'
        );

        $this->app->bind(Extractor::class, function() {
            return new Extractor(config('docdocgoose'));
        });
    }

}