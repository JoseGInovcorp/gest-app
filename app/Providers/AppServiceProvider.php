<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Models\CustomerOrder;
use App\Observers\CustomerOrderObserver;

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
        Vite::prefetch(concurrency: 3);

        // Forçar HTTPS em ambiente de produção
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Registar observers
        CustomerOrder::observe(CustomerOrderObserver::class);
    }
}
