<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table): void {
            $table->string('contract_drive_url', 2048)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('info_source')->nullable();
            $table->string('submission_place', 500)->nullable();
            $table->string('report_number', 100)->nullable();
            $table->string('voucher_status', 80)->nullable();
            $table->decimal('payment_percentage', 5, 2)->default(100);
            $table->boolean('is_offset')->default(false);
            $table->boolean('has_room_fund')->default(false);
            $table->boolean('is_overdue')->default(false);
            $table->boolean('is_renewal')->default(false);
            $table->foreignId('parent_contract_id')->nullable()->constrained('contracts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('parent_contract_id');
            $table->dropColumn(['contract_drive_url', 'province', 'info_source', 'submission_place', 'report_number',
                'voucher_status', 'payment_percentage', 'is_offset', 'has_room_fund', 'is_overdue', 'is_renewal']);
        });
    }
};
