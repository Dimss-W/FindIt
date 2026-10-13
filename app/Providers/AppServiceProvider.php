<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $isVercel = isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL');

        if ($this->app->environment('production') || $isVercel) {
            URL::forceScheme('https');
        }

        if ($isVercel) {
            $dbHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? ($_SERVER['DB_HOST'] ?? ''));
            if (empty($dbHost) || $dbHost === '127.0.0.1' || $dbHost === 'localhost') {
                config([
                    'database.default' => 'sqlite',
                    'database.connections.sqlite.database' => '/tmp/database.sqlite',
                ]);
            }
            config([
                'session.driver' => 'cookie',
                'cache.default' => 'array',
            ]);
        }
    }
}
