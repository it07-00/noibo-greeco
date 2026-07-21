<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_plans', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('category')->default('website');
            $table->longText('content')->nullable();
            $table->dateTime('scheduled_at');
            $table->string('status')->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['scheduled_at', 'status']);
            $table->index('category');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_plans');
    }
};
