<?php

namespace OiLab\OiLaravelAttachments;

use Illuminate\Support\ServiceProvider;
use OiLab\OiLaravelAttachments\Console\Commands\InstallAiSkillCommand;

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
            $this->commands([
                InstallAiSkillCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/oi-laravel-attachments.php' => config_path('oi-laravel-attachments.php'),
            ], 'oi-laravel-attachments-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'oi-laravel-attachments-migrations');

            $this->publishes([
                __DIR__.'/../resources/stubs/ai-skill.md' => base_path('.claude/skills/oilab-laravel-attachments/SKILL.md'),
            ], 'oi-laravel-attachments-skill');
        }
    }
}
