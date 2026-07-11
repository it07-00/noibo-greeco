<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('contract_milestone_files');
        
        Schema::create('contract_milestone_files', function (Blueprint $table): void {
            $table->id();
            $table->string('contract_type');
            $table->unsignedBigInteger('contract_id');
            $table->string('milestone');
            $table->string('file_path');
            $table->string('original_name');
            $table->foreignId('uploader_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['contract_type', 'contract_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_milestone_files');
    }
};
