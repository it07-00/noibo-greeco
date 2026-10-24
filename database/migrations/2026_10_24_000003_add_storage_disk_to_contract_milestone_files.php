<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contract_milestone_files', function (Blueprint $table): void {
            $table->string('storage_disk')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('contract_milestone_files', function (Blueprint $table): void {
            $table->dropColumn('storage_disk');
        });
    }
};
