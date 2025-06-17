<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Mail\ZohoMailTransport;
use Illuminate\Mail\MailManager;

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
        //

        $this->app->make(MailManager::class)->extend('zoho', function ($app) {
            return new ZohoMailTransport();
        });
    }
}
