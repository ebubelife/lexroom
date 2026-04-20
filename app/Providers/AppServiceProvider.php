<?php

namespace App\Providers;

use App\Models\PlatformSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::defaultView('partials.pagination');

        // Share currency symbol and code with all views
        View::composer('*', function ($view) {
            try {
                $view->with([
                    'currencySymbol' => PlatformSetting::currencySymbol(),
                    'currencyCode'   => PlatformSetting::currencyCode(),
                ]);
            } catch (\Exception $e) {
                $view->with(['currencySymbol' => '£', 'currencyCode' => 'gbp']);
            }
        });
    }
}
