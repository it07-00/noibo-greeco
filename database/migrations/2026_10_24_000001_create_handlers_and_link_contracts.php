<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('handlers', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('contracts', function (Blueprint $table): void {
            $table->foreignId('handler_id')->nullable()->constrained('handlers')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('handler_id');
        });
        Schema::dropIfExists('handlers');
    }
};
