<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add columns to contracts table
        Schema::table('contracts', function (Blueprint $table): void {
            $table->unsignedBigInteger('revenue')->default(0)->after('value');
            $table->unsignedBigInteger('commission')->default(0)->after('revenue');
            $table->unsignedBigInteger('ncc_payment')->default(0)->after('commission');
            $table->string('ncc_payment_sheet_url', 2048)->nullable()->after('ncc_payment');
            $table->timestamp('ncc_payment_updated_at')->nullable()->after('ncc_payment_sheet_url');
            $table->string('ncc_payment_status', 20)->default('unpaid')->after('ncc_payment_updated_at');
            $table->date('ncc_payment_paid_at')->nullable()->after('ncc_payment_status');
            $table->string('shd_greeco')->nullable()->after('contract_number');
            $table->date('submitted_at')->nullable()->after('ncc_payment_paid_at');
        });

        // 2. Initialize existing contracts: revenue = value, commission = customer_commission
        DB::table('contracts')->update([
            'revenue' => DB::raw('value'),
            'commission' => DB::raw('customer_commission'),
        ]);

        // 3. Register permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'cash-flow.view',
            'cash-flow.export',
        ];

        foreach ($permissions as $pName) {
            Permission::findOrCreate($pName, 'web');
        }

        // 4. Assign permissions to roles
        /** @var \Spatie\Permission\Models\Role|null $superAdmin */
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        /** @var \Spatie\Permission\Models\Role|null $director */
        $director = Role::where('name', 'Viện Trưởng')->first();
        if ($director) {
            $director->givePermissionTo($permissions);
        }

        /** @var \Spatie\Permission\Models\Role|null $accountant */
        $accountant = Role::where('name', 'Kế toán')->first();
        if ($accountant) {
            $accountant->givePermissionTo($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // 1. Remove permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::whereIn('name', ['cash-flow.view', 'cash-flow.export'])->delete();

        // 2. Drop columns
        Schema::table('contracts', function (Blueprint $table): void {
            $table->dropColumn([
                'revenue',
                'commission',
                'ncc_payment',
                'ncc_payment_sheet_url',
                'ncc_payment_updated_at',
                'ncc_payment_status',
                'ncc_payment_paid_at',
                'shd_greeco',
                'submitted_at',
            ]);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
