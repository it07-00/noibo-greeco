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
        // Defensive drop of existing foreign keys (names may vary on production)
        if (DB::getDriverName() === 'mysql') {
            Schema::table('duty_schedule_user', function (Blueprint $table) {
                $table->dropForeignIfExists(['duty_schedule_id']);
                $table->dropForeignIfExists(['user_id']);
            });

            // Also try explicit constraint names in case the above doesn't match
            $this->dropForeignByNameIfExists('duty_schedule_user', 'duty_schedule_user_duty_schedule_id_foreign');
            $this->dropForeignByNameIfExists('duty_schedule_user', 'duty_schedule_user_user_id_foreign');
        }

        Schema::table('duty_schedule_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();

            if (! $this->columnExists('duty_schedule_user', 'baochau_user_id')) {
                $table->unsignedBigInteger('baochau_user_id')->nullable()->after('user_id');
            }

            if (! $this->columnExists('duty_schedule_user', 'baochau_user_name')) {
                $table->string('baochau_user_name')->nullable()->after('baochau_user_id');
            }
        });

        Schema::table('duty_schedule_user', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                // Drop old unique if still exists
                $this->dropUniqueByNameIfExists('duty_schedule_user', 'duty_schedule_user_duty_schedule_id_user_id_unique');

                $table->foreign('duty_schedule_id')->references('id')->on('duty_schedules')->cascadeOnDelete();
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            }

            if (! $this->uniqueExists('duty_schedule_user', 'dsu_schedule_user_baochau_unique')) {
                $table->unique(['duty_schedule_id', 'user_id', 'baochau_user_id'], 'dsu_schedule_user_baochau_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('duty_schedule_user', function (Blueprint $table) {
            $table->dropUniqueIfExists('dsu_schedule_user_baochau_unique');

            if (DB::getDriverName() === 'mysql') {
                $table->dropForeignIfExists(['duty_schedule_id']);
                $table->dropForeignIfExists(['user_id']);
            }
        });

        Schema::table('duty_schedule_user', function (Blueprint $table) {
            $table->dropColumnIfExists(['baochau_user_id', 'baochau_user_name']);
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

    private function columnExists(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column);
    }

    private function uniqueExists(string $table, string $name): bool
    {
        if (DB::getDriverName() !== 'mysql') {
            // SQLite in-memory (test) — assume not yet created
            return false;
        }

        $count = DB::select(
            "SELECT COUNT(*) as cnt FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = ?
               AND CONSTRAINT_TYPE = 'UNIQUE'",
            [$table, $name]
        );

        return ($count[0]->cnt ?? 0) > 0;
    }

    private function dropForeignByNameIfExists(string $table, string $name): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $count = DB::select(
            "SELECT COUNT(*) as cnt FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = ?
               AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
            [$table, $name]
        );

        if (($count[0]->cnt ?? 0) > 0) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$name}`");
        }
    }

    private function dropUniqueByNameIfExists(string $table, string $name): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $count = DB::select(
            "SELECT COUNT(*) as cnt FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = ?
               AND CONSTRAINT_TYPE = 'UNIQUE'",
            [$table, $name]
        );

        if (($count[0]->cnt ?? 0) > 0) {
            DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$name}`");
        }
    }
};
