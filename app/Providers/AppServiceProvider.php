<?php

namespace App\Providers;

use App\Contracts\AiProviderInterface;
use App\Models\PlatformSetting;
use App\Models\Setting;
use App\Services\ClaudeService;
use App\Services\OpenAiService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AiProviderInterface::class, function ($app) {
            $provider = Setting::get('active_ai_provider', 'claude');

            return $provider === 'openai'
                ? $app->make(OpenAiService::class)
                : $app->make(ClaudeService::class);
        });
    }

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
