<?php

namespace Mgamal92\FilamentDemoGenerator;

use Illuminate\Support\ServiceProvider;

class FilamentDemoGeneratorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/filament-demo-generator.php' => config_path('filament-demo-generator.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/filament-demo-generator.php',
            'filament-demo-generator'
        );
    }

    public function register(): void
    {
        //
    }
}
