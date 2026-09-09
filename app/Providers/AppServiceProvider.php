<?php

namespace App\Providers;

use App\Models\BarangKeluar;
use App\Observers\BarangKeluarObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;

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
            Paginator::useBootstrapFive();

        BarangKeluar::observe(BarangKeluarObserver::class);

    if (request()->header('x-forwarded-proto') === 'https') {
        URL::forceScheme('https');
    }
}
}
