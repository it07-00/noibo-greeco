<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ContractDocumentDownloadController;
use App\Http\Controllers\DashboardController;
use App\Livewire\Commissions\CommissionRequestForm;
use App\Livewire\Commissions\CommissionRequestIndex;
use App\Livewire\Contracts\ContractIndex;
use App\Livewire\Contracts\ContractShow;
use App\Livewire\Customers\CustomerIndex;
use App\Livewire\DailyReports\DailyReportIndex;
use App\Livewire\Departments\DepartmentIndex;
use App\Livewire\DocumentRegulations\DocumentRegulationIndex;
use App\Livewire\DutySchedules\DutyScheduleIndex;
use App\Livewire\Mail\MailCenterIndex;
use App\Livewire\Profile\ProfileEdit;
use App\Livewire\Quotations\QuotationIndex;
use App\Livewire\Reports\BusinessReportIndex;
use App\Livewire\Reports\SalesSummaryIndex;
use App\Livewire\Reports\SalesTargetReport;
use App\Livewire\RolesPermissions\RolesPermissionsIndex;
use App\Livewire\SalesTargets\SalesTargetIndex;
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

    Route::get('/departments', DepartmentIndex::class)
        ->middleware('can:role.manage')
        ->name('departments.index');

    Route::get('/daily-reports', DailyReportIndex::class)
        ->middleware('can:report.view')
        ->name('daily-reports.index');

    Route::get('/customers', CustomerIndex::class)
        ->middleware('can:customer.view')
        ->name('customers.index');

    Route::get('/quotations', QuotationIndex::class)
        ->middleware('can:quotation.view')
        ->name('quotations.index');

    Route::get('/contracts', ContractIndex::class)
        ->middleware('can:contract.view')
        ->name('contracts.index');

    Route::get('/contracts/{contract}', ContractShow::class)
        ->middleware('can:contract.view')
        ->name('contracts.show');

    Route::get('/contracts/{contract}/documents/{document}/download', ContractDocumentDownloadController::class)
        ->middleware('can:contract-document.view')
        ->name('contracts.documents.download');

    Route::get('/business-reports', BusinessReportIndex::class)
        ->middleware('can:sales-report.view')
        ->name('business-reports.index');

    Route::get('/sales-summaries', SalesSummaryIndex::class)
        ->middleware('can:sales-report.view')
        ->name('sales-summaries.index');

    Route::get('/sales-target-reports', SalesTargetReport::class)
        ->middleware('can:sales-report.view')
        ->name('sales-targets.report');

    Route::get('/sales-targets', SalesTargetIndex::class)
        ->middleware('can:sales-target.manage')
        ->name('sales-targets.index');

    Route::get('/commissions', CommissionRequestIndex::class)
        ->middleware('can:commission.view')
        ->name('commissions.index');

    Route::get('/commissions/create', CommissionRequestForm::class)
        ->middleware('can:commission.create')
        ->name('commissions.create');

    Route::get('/commissions/{id}/edit', CommissionRequestForm::class)
        ->middleware('can:commission.update')
        ->name('commissions.edit');

    Route::get('/document-regulations', DocumentRegulationIndex::class)
        ->middleware('can:document.view')
        ->name('document-regulations.index');

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
