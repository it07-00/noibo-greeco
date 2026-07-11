<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('contract_assignments');
        
        Schema::create('contract_assignments', function (Blueprint $table): void {
            $table->id();
            $table->string('assignable_type');
            $table->unsignedBigInteger('assignable_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('external_assignee')->nullable();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->text('note')->nullable();
            $table->date('deadline')->nullable();
            $table->timestamps();

            $table->index(['assignable_type', 'assignable_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_assignments');
    }
};
