<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_targets', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('target_amount')->default(0);
            $table->unsignedInteger('target_contract_count')->default(0);
            $table->foreignId('set_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['year', 'month', 'user_id']);
            $table->index(['user_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_targets');
    }
};
