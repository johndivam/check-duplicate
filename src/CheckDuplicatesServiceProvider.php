<?php

namespace Johndivam\CheckDuplicate;

use Illuminate\Support\ServiceProvider;
use Johndivam\CheckDuplicate\Console\Commands\CheckDuplicatesCommand;

class CheckDuplicatesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CheckDuplicatesCommand::class,
            ]);
        }
        
        $this->publishes([
            __DIR__ . '/config/check-duplicates.php' => config_path('check-duplicates.php'),
        ], 'config');
    }
}
