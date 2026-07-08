<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BusinessPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_permissions_are_separated_by_department(): void
    {
        $this->seed(PermissionSeeder::class);

        $sales = User::factory()->create();
        $sales->assignRole(RoleEnum::Sales->value);

        $accountant = User::factory()->create();
        $accountant->assignRole(RoleEnum::Accountant->value);

        $director = User::factory()->create();
        $director->assignRole(RoleEnum::Director->value);

        self::assertTrue($sales->can(PermissionEnum::QuotationSend->value));
        self::assertTrue($sales->can(PermissionEnum::PaymentScheduleManage->value));
        self::assertTrue($sales->can(PermissionEnum::CommissionCreate->value));
        self::assertFalse($sales->can(PermissionEnum::PaymentRecord->value));

        self::assertTrue($accountant->can(PermissionEnum::PaymentScheduleConfirm->value));
        self::assertTrue($accountant->can(PermissionEnum::PaymentRecord->value));
        self::assertTrue($accountant->can(PermissionEnum::CommissionApprove->value));
        self::assertTrue($accountant->can(PermissionEnum::CommissionPay->value));
        self::assertFalse($accountant->can(PermissionEnum::QuotationCreate->value));

        self::assertTrue($director->can(PermissionEnum::ContractApprove->value));
        self::assertTrue($director->can(PermissionEnum::ManagementDashboardView->value));
        self::assertFalse($director->can(PermissionEnum::PaymentRecord->value));
    }
}
