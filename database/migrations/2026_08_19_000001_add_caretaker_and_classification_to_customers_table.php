<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'caretaker_id')) {
                $table->foreignId('caretaker_id')->nullable()->after('contact_name')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('customers', 'care_status')) {
                $table->string('care_status', 50)->nullable()->after('caretaker_id');
            }
            if (!Schema::hasColumn('customers', 'is_ghg_inventory')) {
                $table->boolean('is_ghg_inventory')->default(false)->after('care_status');
            }
            if (!Schema::hasColumn('customers', 'is_energy_audit')) {
                $table->boolean('is_energy_audit')->default(false)->after('is_ghg_inventory');
            }
            if (!Schema::hasColumn('customers', 'appendix')) {
                $table->string('appendix', 255)->nullable()->after('is_energy_audit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['caretaker_id']);
            $table->dropColumn([
                'caretaker_id',
                'care_status',
                'is_ghg_inventory',
                'is_energy_audit',
                'appendix',
            ]);
        });
    }
};