<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table): void {
            $table->string('duration', 100)->nullable()->after('location');
            $table->decimal('fee', 15, 2)->nullable()->after('duration');
            $table->string('instructor', 191)->nullable()->after('fee');
            $table->text('audience')->nullable()->after('instructor');
            $table->text('objectives')->nullable()->after('audience');
            $table->text('content_summary')->nullable()->after('objectives');
            $table->longText('content_detail')->nullable()->after('content_summary');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table): void {
            $table->dropColumn([
                'duration',
                'fee',
                'instructor',
                'audience',
                'objectives',
                'content_summary',
                'content_detail',
            ]);
        });
    }
};
