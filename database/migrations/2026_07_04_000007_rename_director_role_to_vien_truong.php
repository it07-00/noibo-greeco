<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')
            ->where('name', 'Giám đốc')
            ->where('guard_name', 'web')
            ->update(['name' => 'Viện Trưởng']);
    }

    public function down(): void
    {
        DB::table('roles')
            ->where('name', 'Viện Trưởng')
            ->where('guard_name', 'web')
            ->update(['name' => 'Giám đốc']);
    }
};
