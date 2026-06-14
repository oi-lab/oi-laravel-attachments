<?php

namespace OiLab\OiLaravelAttachments;

use Illuminate\Support\ServiceProvider;

class OiLaravelAttachmentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/oi-laravel-attachments.php', 'oi-laravel-attachments');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/oi-laravel-attachments.php' => config_path('oi-laravel-attachments.php'),
            ], 'oi-laravel-attachments-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'oi-laravel-attachments-migrations');
        }
    }
}
