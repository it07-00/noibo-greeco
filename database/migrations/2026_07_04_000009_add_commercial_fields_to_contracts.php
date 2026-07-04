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
            $table->unsignedBigInteger('original_amount')->default(0)->after('value');
            $table->unsignedBigInteger('customer_commission')->default(0)->after('original_amount');
            $table->unsignedBigInteger('commission_tax')->default(0)->after('customer_commission');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table): void {
            $table->dropColumn([
                'original_amount',
                'customer_commission',
                'commission_tax',
            ]);
        });
    }
};
