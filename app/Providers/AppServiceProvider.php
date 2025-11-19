<?php

namespace App\Providers;

use App\Models\TipeAC;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
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
        View::composer('components.header', function ($view) {
            $tipeAC = Cache::remember('header_tipe_ac', 3600, function () {
                return TipeAC::has('unitAC')->limit(3)->get();
            });

            $view->with('tipeAC', $tipeAC);
        });
    }
}
