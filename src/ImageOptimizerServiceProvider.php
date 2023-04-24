<?php

namespace Avnsh1111\LaravelImageOptimizer;

use Illuminate\Support\ServiceProvider;

class ImageOptimizerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/image-optimizer.php' => config_path('image-optimizer.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/image-optimizer.php', 'image-optimizer'
        );
    }
}
