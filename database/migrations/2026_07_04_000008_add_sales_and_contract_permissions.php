<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'customer.view',
            'customer.manage',
            'quotation.view',
            'quotation.create',
            'quotation.update',
            'quotation.send',
            'quotation.convert',
            'contract.view',
            'contract.create',
            'contract.update',
            'contract.approve',
            'contract.activate',
            'contract.complete',
            'contract.cancel',
            'payment-schedule.view',
            'payment-schedule.manage',
            'payment-schedule.confirm',
            'payment.record',
            'payment.adjust',
            'contract-document.view',
            'contract-document.submit',
            'contract-document.review',
            'business-dashboard.view',
            'accounting-dashboard.view',
            'management-dashboard.view',
            'sales-report.view',
            'sales-target.manage',
        ];

        foreach ($permissions as $pName) {
            Permission::findOrCreate($pName, 'web');
        }

        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        $director = Role::where('name', 'Viện Trưởng')->first();
        if ($director) {
            $director->givePermissionTo([
                'customer.view',
                'quotation.view',
                'contract.view',
                'contract.approve',
                'contract.activate',
                'contract.complete',
                'contract.cancel',
                'payment-schedule.view',
                'contract-document.view',
                'management-dashboard.view',
                'sales-report.view',
                'sales-target.manage',
            ]);
        }

        $sales = Role::where('name', 'Phòng Kinh doanh')->first();
        if ($sales) {
            $sales->givePermissionTo([
                'customer.view',
                'customer.manage',
                'quotation.view',
                'quotation.create',
                'quotation.update',
                'quotation.send',
                'quotation.convert',
                'contract.view',
                'contract.create',
                'contract.update',
                'contract.activate',
                'payment-schedule.view',
                'payment-schedule.manage',
                'contract-document.view',
                'contract-document.submit',
                'business-dashboard.view',
                'sales-report.view',
                'sales-target.manage',
            ]);
        }

        $consultant = Role::where('name', 'Tư vấn')->first();
        if ($consultant) {
            $consultant->givePermissionTo([
                'customer.view',
                'quotation.view',
                'contract.view',
                'contract.update',
                'payment-schedule.view',
                'contract-document.view',
                'contract-document.submit',
            ]);
        }

        $accountant = Role::where('name', 'Kế toán')->first();
        if ($accountant) {
            $accountant->givePermissionTo([
                'customer.view',
                'quotation.view',
                'contract.view',
                'payment-schedule.view',
                'payment-schedule.manage',
                'payment-schedule.confirm',
                'payment.record',
                'payment.adjust',
                'contract-document.view',
                'contract-document.submit',
                'contract-document.review',
                'accounting-dashboard.view',
                'sales-report.view',
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Keep empty for safe rollback on production
    }
};
