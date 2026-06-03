<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
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
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(\Spatie\Permission\Models\Role::class, \App\Policies\RolePolicy::class);
        Gate::policy(\App\Models\DailyReport::class, \App\Policies\DailyReportPolicy::class);


        if (!$this->app->runningInConsole()) {
            try {
                if ($timezone = \App\Models\Setting::get('timezone')) {
                    date_default_timezone_set($timezone);
                    config(['app.timezone' => $timezone]);
                }
                if ($language = \App\Models\Setting::get('language')) {
                    $this->app->setLocale($language);
                    config(['app.locale' => $language]);
                }
            } catch (\Throwable $e) {
                // Ignore DB/connection errors during early boot or install
            }
        }
    }
}
