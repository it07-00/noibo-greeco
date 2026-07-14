<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Contract;
use App\Models\ContractDocument;
use App\Models\ContractPayment;
use App\Models\ContractPaymentSchedule;
use App\Models\Course;
use App\Models\Customer;
use App\Models\DailyReport;
use App\Models\DocumentRegulation;
use App\Models\Quotation;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Policies\ContractDocumentPolicy;
use App\Policies\ContractPaymentPolicy;
use App\Policies\ContractPaymentSchedulePolicy;
use App\Policies\ContractPolicy;
use App\Policies\CoursePolicy;
use App\Policies\CustomerPolicy;
use App\Policies\DailyReportPolicy;
use App\Policies\DocumentRegulationPolicy;
use App\Policies\QuotationPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Support\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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
        Schema::defaultStringLength(191);

        Carbon::setLocale(config('app.locale'));

        Event::listen(
            LocaleUpdated::class,
            static function (LocaleUpdated $event): void {
                Carbon::setLocale($event->locale);
            }
        );

        if (config('app.env') === 'production' || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            URL::forceScheme('https');
        }

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(DailyReport::class, DailyReportPolicy::class);
        Gate::policy(DocumentRegulation::class, DocumentRegulationPolicy::class);
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Quotation::class, QuotationPolicy::class);
        Gate::policy(Contract::class, ContractPolicy::class);
        Gate::policy(ContractPaymentSchedule::class, ContractPaymentSchedulePolicy::class);
        Gate::policy(ContractPayment::class, ContractPaymentPolicy::class);
        Gate::policy(ContractDocument::class, ContractDocumentPolicy::class);

        // Register authentication activity logging
        Event::listen(
            Login::class,
            static function (Login $event): void {
                $user = $event->user instanceof User ? $event->user : null;
                ActivityLogger::log('login', 'Đăng nhập thành công', $user);
            }
        );

        Event::listen(
            Failed::class,
            static function (Failed $event): void {
                $username = $event->credentials['username'] ?? $event->credentials['email'] ?? 'Không rõ';
                ActivityLogger::log('failed_login', "Đăng nhập thất bại (tài khoản: $username)");
            }
        );

        Event::listen(
            Logout::class,
            static function (Logout $event): void {
                $user = $event->user instanceof User ? $event->user : null;
                ActivityLogger::log('logout', 'Đăng xuất', $user);
            }
        );

        if (! $this->app->runningInConsole()) {
            try {
                if ($timezone = Setting::get('timezone')) {
                    date_default_timezone_set($timezone);
                    config(['app.timezone' => $timezone]);
                }
                if ($language = Setting::get('language')) {
                    $this->app->setLocale($language);
                    config(['app.locale' => $language]);
                }
            } catch (\Throwable $e) {
                // Ignore DB/connection errors during early boot or install
            }
        }
    }
}
