<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('duty_schedules', function (Blueprint $table) {
            $table->integer('early_minutes')->nullable()->default(0)->after('late_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('duty_schedules', function (Blueprint $table) {
            $table->dropColumn('early_minutes');
        });
    }
};
