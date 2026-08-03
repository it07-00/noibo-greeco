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
            if (! Schema::hasColumn('duty_schedules', 'check_in_at')) {
                $table->dateTime('check_in_at')->nullable()->after('end_at');
            }
            if (! Schema::hasColumn('duty_schedules', 'check_out_at')) {
                $table->dateTime('check_out_at')->nullable()->after('check_in_at');
            }
            if (! Schema::hasColumn('duty_schedules', 'late_minutes')) {
                $table->integer('late_minutes')->nullable()->default(0)->after('check_out_at');
            }
            if (! Schema::hasColumn('duty_schedules', 'early_minutes')) {
                $table->integer('early_minutes')->nullable()->default(0)->after('late_minutes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('duty_schedules', function (Blueprint $table) {
            $columnsToDrop = array_filter(
                ['check_in_at', 'check_out_at', 'late_minutes', 'early_minutes'],
                fn ($col) => Schema::hasColumn('duty_schedules', $col)
            );
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
