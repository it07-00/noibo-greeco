<?php

declare(strict_types=1);

use App\Enums\CommissionRequestStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('receiver_name');
            $table->string('receiver_phone')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_code', 20)->nullable();
            $table->string('bank_number', 50)->nullable();
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('referrer_info')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default(CommissionRequestStatus::Estimated->value);
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('payment_bill_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['bank_code', 'bank_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_requests');
    }
};
