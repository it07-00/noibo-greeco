<?php

declare(strict_types=1);

use App\Enums\ContractRenewalStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table): void {
            $table->string('renewal_status', 40)->default(ContractRenewalStatus::NotApplicable->value)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table): void {
            $table->dropColumn('renewal_status');
        });
    }
};
