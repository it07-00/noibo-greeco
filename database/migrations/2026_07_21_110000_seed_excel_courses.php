<?php

declare(strict_types=1);

use Database\Seeders\CourseSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        app(CourseSeeder::class)->run();
    }

    public function down(): void
    {
        // Keep course data intact on rollback
    }
};
