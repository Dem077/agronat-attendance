<?php

namespace App\Providers;

use App\Models\TimeSheet;
use App\Observers\TimeSheetObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (config('horizon.enabled')) {
            $this->app->register(HorizonServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        TimeSheet::observe(TimeSheetObserver::class);
    }
}
