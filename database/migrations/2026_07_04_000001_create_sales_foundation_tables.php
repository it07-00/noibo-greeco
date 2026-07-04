<?php

declare(strict_types=1);

use App\Enums\QuotationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('tax_code', 50)->nullable()->unique();
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('billing_address')->nullable();
            $table->text('work_address')->nullable();
            $table->string('province')->nullable();
            $table->string('industry')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('quotations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('quotation_number')->nullable()->unique();
            $table->string('contract_type', 80);
            $table->string('status', 40)->default(QuotationStatus::Draft->value);
            $table->date('issued_at')->nullable();
            $table->date('valid_until')->nullable();
            $table->unsignedBigInteger('total_amount')->default(0);
            $table->char('currency', 3)->default('VND');
            $table->text('notes')->nullable();
            $table->text('lost_reason')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('won_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'owner_id']);
            $table->index(['contract_type', 'status']);
            $table->index('valid_until');
        });

        Schema::create('quotation_services', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->string('service_type', 100);
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->unsignedBigInteger('unit_price')->default(0);
            $table->unsignedBigInteger('total_amount')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['quotation_id', 'sort_order']);
        });

        Schema::create('quotation_follow_ups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('content');
            $table->string('contact_channel', 50)->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('next_follow_up_at')->nullable();
            $table->timestamps();

            $table->index(['quotation_id', 'next_follow_up_at']);
        });

        Schema::create('quotation_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->json('snapshot');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('change_note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['quotation_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_versions');
        Schema::dropIfExists('quotation_follow_ups');
        Schema::dropIfExists('quotation_services');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('customers');
    }
};
