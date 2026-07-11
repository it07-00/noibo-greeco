<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table): void {
            if (!Schema::hasColumn('contracts', 'workflow_status')) {
                $table->string('workflow_status')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table): void {
            if (Schema::hasColumn('contracts', 'workflow_status')) {
                $table->dropColumn('workflow_status');
            }
        });
    }
};
