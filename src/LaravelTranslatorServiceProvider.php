<?php

namespace Zhylon\LaravelTranslator;

use Illuminate\Support\ServiceProvider;
use Zhylon\LaravelTranslator\Support\Facades\Translate;

class LaravelTranslatorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // merge services config
        $this->mergeConfigFrom(__DIR__.'/../config/services.php', 'services');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\TranslateCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->app->singleton('translate', function ($app) {
            return new TranslateManager(config('services.zhylon_translate'));
        });

        $this->app->alias('translate', Translate::class);
    }
}
