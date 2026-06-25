<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Livewire\DailyReports\DailyReportIndex;
use App\Livewire\DutySchedules\DutyScheduleIndex;
use App\Livewire\Mail\MailCenterIndex;
use App\Livewire\Profile\ProfileEdit;
use App\Livewire\RolesPermissions\RolesPermissionsIndex;
use App\Livewire\Settings\SettingIndex;
use App\Livewire\Users\UserIndex;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'unlocked'])->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)
        ->middleware('can:dashboard.view')
        ->name('dashboard');

    Route::get('/users', UserIndex::class)
        ->middleware('can:user.view')
        ->name('users.index');

    Route::get('/duty-schedules', DutyScheduleIndex::class)
        ->middleware('can:schedule.view')
        ->name('duty-schedules.index');

    Route::get('/settings', SettingIndex::class)
        ->middleware('can:setting.view')
        ->name('settings.index');

    Route::get('/roles-permissions', RolesPermissionsIndex::class)
        ->middleware('can:role.manage')
        ->name('roles-permissions.index');

    Route::get('/daily-reports', DailyReportIndex::class)
        ->middleware('can:report.view')
        ->name('daily-reports.index');

    Route::get('/mail', MailCenterIndex::class)
        ->middleware('can:mail.view')
        ->name('mail.index');

    Route::get('/profile', ProfileEdit::class)
        ->name('profile.edit');
});

// Custom storage route fallback for hosting that blocks symlinks
Route::get('/storage/{path}', function (string $path) {
    $filePath = 'public/'.$path;

    if (! Storage::exists($filePath)) {
        abort(404);
    }

    $file = Storage::get($filePath);
    $type = Storage::mimeType($filePath);

    return Response::make($file, 200)->header('Content-Type', $type);
})->where('path', '.*');
