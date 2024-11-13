<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Classes\HelperFunction\FileProcess;
use App\Classes\HelperFunction\ModelFinder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ModelFinder::class, function ($app) {
            return new ModelFinder();
        });

        $this->app->singleton(FileProcess::class, function ($app) {
            return new FileProcess();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
