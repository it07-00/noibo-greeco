<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Enums\PaymentConditionType;
use App\Enums\PaymentHandoverStatus;
use App\Models\Contract;
use App\Models\ContractPaymentSchedule;
use Carbon\CarbonInterface;
use DomainException;
use Illuminate\Support\Facades\DB;

final class PaymentScheduleService
{
    public function __construct(
        private readonly PaymentDueDateService $dueDateService,
        private readonly PaymentScheduleStatusResolver $statusResolver,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function save(
        Contract $contract,
        array $data,
        ?ContractPaymentSchedule $schedule = null,
    ): ContractPaymentSchedule {
        if ($schedule !== null && $schedule->contract_id !== $contract->id) {
            throw new DomainException('Đợt thanh toán không thuộc hợp đồng này.');
        }

        if ($schedule !== null && $schedule->allocations()->exists()) {
            throw new DomainException('Đợt đã phát sinh thanh toán, không thể sửa trực tiếp.');
        }

        if ((float) $data['amount'] <= 0) {
            throw new DomainException('Số tiền của đợt phải lớn hơn 0.');
        }

        $conditionType = PaymentConditionType::from((string) $data['condition_type']);

        if ($conditionType === PaymentConditionType::Custom && blank($data['custom_condition'] ?? null)) {
            throw new DomainException('Vui lòng nhập điều kiện thanh toán tùy chỉnh.');
        }

        $savedSchedule = DB::transaction(function () use ($contract, $data, $schedule): ContractPaymentSchedule {
            $payload = array_merge($data, [
                'contract_id' => $contract->id,
                'handover_status' => PaymentHandoverStatus::WaitingForCustomerPayment,
                'confirmed_at' => null,
                'confirmed_by' => null,
            ]);

            if ($schedule === null) {
                $payload['installment_number'] = ((int) $contract->paymentSchedules()->max('installment_number')) + 1;

                return ContractPaymentSchedule::query()->create($payload);
            }

            $schedule->update($payload);

            return $schedule->refresh();
        });

        return $this->statusResolver->refresh($savedSchedule);
    }

    public function delete(ContractPaymentSchedule $schedule): void
    {
        if ($schedule->allocations()->exists()) {
            throw new DomainException('Không thể xóa đợt đã phát sinh thanh toán.');
        }

        $schedule->delete();
    }

    public function trigger(
        ContractPaymentSchedule $schedule,
        CarbonInterface $triggeredAt,
    ): ContractPaymentSchedule {
        $schedule = $this->dueDateService->trigger($schedule, $triggeredAt);

        return $this->statusResolver->refresh($schedule);
    }
}
