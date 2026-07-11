<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('contract_workflow_steps');
        
        Schema::create('contract_workflow_steps', function (Blueprint $table): void {
            $table->id();
            $table->string('contract_type');
            $table->unsignedBigInteger('contract_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('step_name'); // receiving, survey, processing, waiting_client, client_confirmed, finished
            $table->string('action')->default('complete');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index(['contract_type', 'contract_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_workflow_steps');
    }
};
