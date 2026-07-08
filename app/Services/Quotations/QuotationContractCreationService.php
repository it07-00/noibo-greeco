<?php

declare(strict_types=1);

namespace App\Services\Quotations;

use App\Models\Contract;
use App\Models\Quotation;
use App\Models\User;
use App\Services\Contracts\ContractDocumentService;
use App\Services\Payments\PaymentScheduleService;
use DomainException;
use Illuminate\Support\Facades\DB;

final class QuotationContractCreationService
{
    public function __construct(
        private readonly QuotationToContractService $converter,
        private readonly PaymentScheduleService $paymentSchedules,
        private readonly ContractDocumentService $documents,
    ) {}

    /**
     * @param  array<string, mixed>  $contractData
     * @param  list<array<string, mixed>>  $scheduleRows
     * @param  list<array<string, mixed>>  $documentRows
     */
    public function create(
        Quotation $quotation,
        array $contractData,
        array $scheduleRows,
        array $documentRows,
        User $actor,
    ): Contract {
        $contractValue = (float) ($contractData['value'] ?? 0);

        if ($scheduleRows !== []) {
            $scheduledAmount = array_sum(array_map(
                static fn (array $row): float => (float) $row['amount'],
                $scheduleRows,
            ));

            if (abs($scheduledAmount - $contractValue) > 0.01) {
                throw new DomainException('Tổng các đợt thanh toán phải bằng giá trị hợp đồng.');
            }

            $percentages = array_column($scheduleRows, 'percentage');
            $allHavePercentage = ! in_array(null, $percentages, true);

            if ($allHavePercentage && abs(array_sum($percentages) - 100.0) > 0.001) {
                throw new DomainException('Tổng tỷ lệ các đợt thanh toán phải bằng 100%.');
            }
        }

        return DB::transaction(function () use (
            $quotation,
            $contractData,
            $scheduleRows,
            $documentRows,
            $actor,
        ): Contract {
            $contract = $this->converter->convert(
                $quotation,
                $contractData['contract_number'] ?? null,
                $contractData,
            );

            $savedSchedules = [];

            foreach ($scheduleRows as $index => $row) {
                $savedSchedules[$index] = $this->paymentSchedules->save($contract, $row);
            }

            foreach ($documentRows as $row) {
                $scheduleIndex = $row['payment_schedule_index'] ?? null;
                unset($row['payment_schedule_index']);

                if ($scheduleIndex !== null && $scheduleIndex !== '') {
                    $schedule = $savedSchedules[(int) $scheduleIndex] ?? null;

                    if ($schedule === null) {
                        throw new DomainException('Đợt thanh toán liên kết với chứng từ không hợp lệ.');
                    }

                    $row['payment_schedule_id'] = $schedule->id;
                }

                $this->documents->create($contract, $row, $actor);
            }

            return $contract->refresh()->load([
                'customer',
                'services',
                'paymentSchedules',
                'documents',
            ]);
        });
    }
}
