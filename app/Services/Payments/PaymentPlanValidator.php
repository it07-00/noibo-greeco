<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Models\Contract;
use DomainException;

final class PaymentPlanValidator
{
    public function validate(Contract $contract): void
    {
        $contract->load('paymentSchedules');
        $activeSchedules = $contract->paymentSchedules
            ->whereNull('cancelled_at');

        if ($activeSchedules->isEmpty()) {
            throw new DomainException('Hợp đồng phải có ít nhất một đợt thanh toán.');
        }

        if ((int) $activeSchedules->sum('amount') !== $contract->value) {
            throw new DomainException('Tổng số tiền các đợt phải bằng giá trị hợp đồng.');
        }

        $percentages = $activeSchedules->pluck('percentage');

        if ($percentages->contains(null)) {
            return;
        }

        $totalPercentage = $percentages
            ->map(static fn (mixed $value): float => (float) $value)
            ->sum();

        if (abs($totalPercentage - 100.0) > 0.001) {
            throw new DomainException('Tổng tỷ lệ các đợt thanh toán phải bằng 100%.');
        }
    }
}
