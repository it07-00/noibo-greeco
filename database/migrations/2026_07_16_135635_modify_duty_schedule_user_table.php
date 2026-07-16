<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('duty_schedule_user', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                $table->dropForeign(['duty_schedule_id']);
                $table->dropForeign(['user_id']);
            }
        });

        Schema::table('duty_schedule_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->unsignedBigInteger('baochau_user_id')->nullable()->after('user_id');
            $table->string('baochau_user_name')->nullable()->after('baochau_user_id');
        });

        Schema::table('duty_schedule_user', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                $table->dropUnique('duty_schedule_user_duty_schedule_id_user_id_unique');
                $table->foreign('duty_schedule_id')->references('id')->on('duty_schedules')->cascadeOnDelete();
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            }
            $table->unique(['duty_schedule_id', 'user_id', 'baochau_user_id'], 'dsu_schedule_user_baochau_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('duty_schedule_user', function (Blueprint $table) {
            $table->dropUnique('dsu_schedule_user_baochau_unique');
            if (DB::getDriverName() === 'mysql') {
                $table->dropForeign(['duty_schedule_id']);
                $table->dropForeign(['user_id']);
            }
        });

        Schema::table('duty_schedule_user', function (Blueprint $table) {
            $table->dropColumn(['baochau_user_id', 'baochau_user_name']);
            $table->unsignedBigInteger('user_id')->change();
        });

        Schema::table('duty_schedule_user', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                $table->foreign('duty_schedule_id')->references('id')->on('duty_schedules')->cascadeOnDelete();
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                $table->unique(['duty_schedule_id', 'user_id']);
            }
        });
    }
};
