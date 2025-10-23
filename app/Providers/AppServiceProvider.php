<?php

namespace App\Providers;

use App\Models\Karyawan;
use App\Policies\KaryawanPolicy;
use Illuminate\Support\Facades\Gate;
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
        // Register model policies
        Gate::policy(Karyawan::class, KaryawanPolicy::class);
    }
}
