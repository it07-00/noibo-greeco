<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\ContractType;
use App\Enums\CustomerType;
use App\Enums\ServiceType;
use Tests\TestCase;

final class BusinessEnumTest extends TestCase
{
    public function test_customer_types_are_stable_and_complete(): void
    {
        self::assertSame(['organization', 'individual'], CustomerType::values());
    }

    public function test_contract_types_are_stable_and_complete(): void
    {
        self::assertSame([
            'training',
            'consulting',
            'project',
            'research_technology_transfer',
            'scientific_event_communication',
        ], ContractType::values());
    }

    public function test_every_service_belongs_to_exactly_one_contract_type(): void
    {
        self::assertCount(27, ServiceType::cases());

        $groupedCount = 0;

        foreach (ContractType::cases() as $contractType) {
            $options = ServiceType::optionsFor($contractType);
            self::assertNotEmpty($options);

            foreach (array_keys($options) as $serviceValue) {
                self::assertSame(
                    $contractType,
                    ServiceType::from($serviceValue)->contractType(),
                );
            }

            $groupedCount += count($options);
        }

        self::assertSame(count(ServiceType::cases()), $groupedCount);
    }
}
