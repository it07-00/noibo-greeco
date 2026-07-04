<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table): void {
            $table->text('working_situation')->nullable()->after('currency');
            $table->unsignedBigInteger('original_amount')->default(0)->after('total_amount');
            $table->unsignedBigInteger('customer_commission')->default(0)->after('original_amount');
            $table->unsignedBigInteger('commission_tax')->default(0)->after('customer_commission');
            $table->unsignedBigInteger('contract_value')->default(0)->after('commission_tax');
        });

        DB::table('quotations')->update([
            'original_amount' => DB::raw('total_amount'),
            'contract_value' => DB::raw('total_amount'),
        ]);

        Schema::table('quotations', function (Blueprint $table): void {
            $table->index(['owner_id', 'issued_at']);
            $table->index(['status', 'contract_value']);
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table): void {
            $table->dropIndex(['owner_id', 'issued_at']);
            $table->dropIndex(['status', 'contract_value']);
            $table->dropColumn([
                'working_situation',
                'original_amount',
                'customer_commission',
                'commission_tax',
                'contract_value',
            ]);
        });
    }
};
