<?php

declare(strict_types=1);

use App\Enums\DocumentStatus;
use App\Enums\PaymentHandoverStatus;
use App\Enums\PaymentScheduleStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_payment_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('installment_number');
            $table->string('name');
            $table->decimal('percentage', 5, 2)->nullable();
            $table->unsignedBigInteger('amount');
            $table->string('condition_type', 60);
            $table->text('custom_condition')->nullable();
            $table->date('expected_trigger_date')->nullable();
            $table->date('triggered_at')->nullable();
            $table->unsignedSmallInteger('payment_term_days')->nullable();
            $table->string('payment_term_unit', 30)->nullable();
            $table->date('due_date')->nullable();
            $table->string('status', 40)->default(PaymentScheduleStatus::WaitingCondition->value);
            $table->string('handover_status', 50)->default(PaymentHandoverStatus::BusinessPreparing->value);
            $table->foreignId('responsible_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('next_action')->nullable();
            $table->date('next_action_due_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->unique(['contract_id', 'installment_number']);
            $table->index(['status', 'due_date']);
            $table->index(
                ['handover_status', 'responsible_department_id'],
                'cps_handover_department_index',
            );
        });

        Schema::create('contract_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contract_id')->constrained()->restrictOnDelete();
            $table->date('paid_at');
            $table->unsignedBigInteger('amount');
            $table->string('payment_method', 40);
            $table->string('reference_number')->nullable();
            $table->string('proof_file_path')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('void_reason')->nullable();
            $table->timestamps();

            $table->index(['contract_id', 'paid_at']);
            $table->index('reference_number');
        });

        Schema::create('contract_payment_allocations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payment_id')->constrained('contract_payments')->cascadeOnDelete();
            $table->foreignId('payment_schedule_id')->constrained('contract_payment_schedules')->restrictOnDelete();
            $table->unsignedBigInteger('allocated_amount');
            $table->timestamps();

            $table->unique(['payment_id', 'payment_schedule_id'], 'payment_schedule_allocation_unique');
        });

        Schema::create('contract_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_schedule_id')->nullable()->constrained('contract_payment_schedules')->nullOnDelete();
            $table->foreignId('supersedes_id')->nullable()->constrained('contract_documents')->nullOnDelete();
            $table->string('type', 50);
            $table->string('status', 40)->default(DocumentStatus::Draft->value);
            $table->string('title');
            $table->string('file_path');
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_feedback')->nullable();
            $table->date('expires_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['contract_id', 'type', 'status']);
            $table->index(['payment_schedule_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_documents');
        Schema::dropIfExists('contract_payment_allocations');
        Schema::dropIfExists('contract_payments');
        Schema::dropIfExists('contract_payment_schedules');
    }
};
