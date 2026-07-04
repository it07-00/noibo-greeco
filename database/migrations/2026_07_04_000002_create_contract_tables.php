<?php

declare(strict_types=1);

use App\Enums\ContractStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('quotation_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contract_number')->nullable()->unique();
            $table->string('type', 80);
            $table->string('status', 40)->default(ContractStatus::Draft->value);
            $table->string('title');
            $table->unsignedBigInteger('value')->default(0);
            $table->char('currency', 3)->default('VND');
            $table->date('signed_at')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('liquidated_at')->nullable();
            $table->text('suspension_reason')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'owner_id']);
            $table->index(['type', 'status']);
            $table->index('ends_at');
        });

        Schema::create('contract_services', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->string('service_type', 100);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('amount')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['contract_id', 'sort_order']);
        });

        Schema::create('contract_progress_notes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('content');
            $table->unsignedTinyInteger('progress_percentage')->nullable();
            $table->date('reported_at')->nullable();
            $table->timestamps();

            $table->index(['contract_id', 'reported_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_progress_notes');
        Schema::dropIfExists('contract_services');
        Schema::dropIfExists('contracts');
    }
};
