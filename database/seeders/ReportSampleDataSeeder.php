<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\PaymentConditionType;
use App\Enums\PaymentMethod;
use App\Enums\PaymentScheduleStatus;
use App\Enums\PaymentTermUnit;
use App\Enums\QuotationStatus;
use App\Enums\RoleEnum;
use App\Enums\ServiceType;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class ReportSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $salesUsers = User::role(RoleEnum::Sales->value)->get();
        if ($salesUsers->isEmpty()) {
            $salesUsers = User::query()->limit(2)->get();
        }

        $provinces = [
            'Hà Nội', 'TP. Hồ Chí Minh', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ',
            'Bình Dương', 'Đồng Nai', 'Long An', 'Quảng Ninh', 'Bắc Ninh',
            'Thanh Hóa', 'Nghệ An', 'Khánh Hòa', 'Lâm Đồng', 'Bà Rịa - Vũng Tàu'
        ];

        $industries = [
            'Sản xuất tiêu dùng', 'Công nghệ thông tin', 'Năng lượng tái tạo',
            'Dệt may & Da giày', 'Logistics & Vận tải', 'Xây dựng & Bất động sản',
            'Nông nghiệp công nghệ cao', 'Chế biến thực phẩm', 'Y tế & Dược phẩm'
        ];

        $companyPrefixes = ['Công ty Cổ phần', 'Công ty TNHH', 'Tập đoàn', 'Tổng công ty'];
        $companyNames = [
            'Phát triển Xanh', 'Công nghệ Ánh Sáng', 'Năng lượng Mặt Trời', 'Dịch vụ Môi trường',
            'Đầu tư và Xây dựng', 'Sản xuất Bền vững', 'Logistics Toàn Cầu', 'Thực phẩm Sạch',
            'Dệt may Thăng Long', 'Bách Hóa Việt', 'Cảng biển Phương Nam', 'Giải pháp Carbon',
            'Nông nghiệp Xanh', 'Cơ khí Chính xác', 'Hóa chất An toàn', 'Tái chế Việt Nam'
        ];

        // Create 20 random customers
        $customers = [];
        for ($i = 0; $i < 20; $i++) {
            $name = $companyPrefixes[array_rand($companyPrefixes)] . ' ' . $companyNames[array_rand($companyNames)] . ' ' . rand(10, 99);
            $taxCode = '0' . rand(100000000, 999999999);

            $customers[] = Customer::query()->create([
                'name' => $name,
                'tax_code' => $taxCode,
                'contact_name' => 'Nguyễn Văn ' . chr(65 + $i),
                'email' => 'contact' . $i . '@example.com',
                'phone' => '09' . rand(10000000, 99999999),
                'billing_address' => 'Địa chỉ xuất hóa đơn số ' . $i,
                'work_address' => 'Địa chỉ thực hiện số ' . $i,
                'province' => $provinces[array_rand($provinces)],
                'industry' => $industries[array_rand($industries)],
                'notes' => 'Khách hàng tiềm năng tạo tự động.',
            ]);
        }

        // Generate 50 quotations / contracts
        $serviceTypes = ServiceType::cases();

        DB::transaction(function () use ($customers, $salesUsers, $serviceTypes): void {
            for ($i = 0; $i < 50; $i++) {
                $customer = $customers[array_rand($customers)];
                $owner = $salesUsers->random();

                // Select a random service type and its corresponding contract type
                /** @var ServiceType $serviceType */
                $serviceType = $serviceTypes[array_rand($serviceTypes)];
                $contractType = $serviceType->contractType();

                // Determine year (2025 or 2026) and random date
                $year = rand(2025, 2026);
                $month = rand(1, 12);
                $day = rand(1, 28);
                $issuedAt = Carbon::create($year, $month, $day);
                $validUntil = (clone $issuedAt)->addDays(30);

                // Financial values
                $originalAmount = rand(5, 120) * 10000000; // 50M to 1.2B
                $commissionPercent = rand(0, 1) === 1 ? rand(2, 8) : 0;
                $customerCommission = (int) ($originalAmount * $commissionPercent / 100);
                $commissionTax = (int) ($customerCommission * 0.1);
                $contractValue = $originalAmount - $customerCommission;

                // Status distribution: 15% follow up/lost, 85% won and converted
                $randStatus = rand(1, 100);
                if ($randStatus <= 10) {
                    $status = QuotationStatus::Lost;
                } elseif ($randStatus <= 15) {
                    $status = QuotationStatus::FollowingUp;
                } else {
                    $status = QuotationStatus::Won;
                }

                $quotation = Quotation::query()->create([
                    'customer_id' => $customer->id,
                    'owner_id' => $owner->id,
                    'quotation_number' => sprintf('BG-%d-%04d', $year, $i + 100),
                    'contract_type' => $contractType,
                    'status' => $status,
                    'issued_at' => $issuedAt,
                    'valid_until' => $validUntil,
                    'original_amount' => $originalAmount,
                    'total_amount' => $originalAmount,
                    'customer_commission' => $customerCommission,
                    'commission_tax' => $commissionTax,
                    'contract_value' => $contractValue,
                    'currency' => 'VND',
                    'working_situation' => 'Đã tạo dữ liệu mẫu.',
                    'lost_reason' => $status === QuotationStatus::Lost ? 'Giá cao hơn đối thủ cạnh tranh.' : null,
                ]);

                // Create quotation service
                $quotation->services()->create([
                    'service_type' => $serviceType,
                    'description' => $serviceType->label() . ' - Gói triển khai chuyên nghiệp.',
                    'quantity' => 1,
                    'unit_price' => $originalAmount,
                    'total_amount' => $originalAmount,
                    'sort_order' => 0,
                ]);

                // If won, convert to contract
                if ($status === QuotationStatus::Won) {
                    $signedAt = (clone $issuedAt)->addDays(rand(3, 10));
                    $startsAt = (clone $signedAt)->addDays(5);
                    $endsAt = (clone $startsAt)->addMonths(rand(3, 18));

                    // Random contract status
                    $contractStatuses = [
                        ContractStatus::Active->value,
                        ContractStatus::Completed->value,
                        ContractStatus::Liquidated->value,
                    ];
                    $cStatus = $contractStatuses[array_rand($contractStatuses)];

                    $contract = Contract::query()->create([
                        'quotation_id' => $quotation->id,
                        'customer_id' => $customer->id,
                        'owner_id' => $owner->id,
                        'contract_number' => sprintf('HĐ-%d-%04d', $year, $i + 100),
                        'type' => $contractType,
                        'status' => $cStatus,
                        'title' => 'Hợp đồng ' . $serviceType->label() . ' - ' . $customer->name,
                        'value' => $contractValue,
                        'original_amount' => $originalAmount,
                        'customer_commission' => $customerCommission,
                        'commission_tax' => $commissionTax,
                        'currency' => 'VND',
                        'payment_method' => PaymentMethod::BankTransfer->value,
                        'signed_at' => $signedAt,
                        'starts_at' => $startsAt,
                        'ends_at' => $endsAt,
                        'notes' => 'Hợp đồng mẫu cho báo cáo.',
                    ]);

                    // Create contract service
                    $contract->services()->create([
                        'service_type' => $serviceType,
                        'description' => $serviceType->label() . ' - Nội dung triển khai.',
                        'amount' => $contractValue,
                        'sort_order' => 0,
                    ]);

                    // Create 2 payment installments (50% and 50%)
                    $firstInstallmentVal = (int) ($contractValue * 0.5);
                    $secondInstallmentVal = $contractValue - $firstInstallmentVal;

                    $schedules = [];
                    $schedules[] = $contract->paymentSchedules()->create([
                        'installment_number' => 1,
                        'name' => 'Tạm ứng đợt 1',
                        'percentage' => 50,
                        'amount' => $firstInstallmentVal,
                        'condition_type' => PaymentConditionType::AfterContractSigned->value,
                        'due_date' => (clone $signedAt)->addDays(7),
                        'status' => PaymentScheduleStatus::Paid->value,
                        'triggered_at' => $signedAt,
                    ]);

                    // Second installment status (randomly paid or pending)
                    $isSecondPaid = rand(0, 1) === 1 || $cStatus !== ContractStatus::Active->value;
                    $schedules[] = $contract->paymentSchedules()->create([
                        'installment_number' => 2,
                        'name' => 'Thanh toán đợt 2 (Nghiệm thu)',
                        'percentage' => 50,
                        'amount' => $secondInstallmentVal,
                        'condition_type' => PaymentConditionType::AfterAcceptance->value,
                        'due_date' => (clone $endsAt)->subDays(15),
                        'status' => $isSecondPaid ? PaymentScheduleStatus::Paid->value : PaymentScheduleStatus::Pending->value,
                        'triggered_at' => $isSecondPaid ? (clone $endsAt)->subDays(20) : null,
                    ]);

                    // Create payments for schedules
                    // First payment (always paid)
                    $payment1 = $contract->payments()->create([
                        'paid_at' => (clone $signedAt)->addDays(rand(1, 5)),
                        'amount' => $firstInstallmentVal,
                        'payment_method' => PaymentMethod::BankTransfer->value,
                        'reference_number' => 'FT' . rand(10000000, 99999999),
                        'recorded_by' => $owner->id,
                        'notes' => 'Khách thanh toán đợt 1.',
                    ]);
                    $payment1->allocations()->create([
                        'payment_schedule_id' => $schedules[0]->id,
                        'allocated_amount' => $firstInstallmentVal,
                    ]);

                    // Second payment (if paid)
                    if ($isSecondPaid) {
                        $payment2 = $contract->payments()->create([
                            'paid_at' => (clone $endsAt)->subDays(rand(15, 20)),
                            'amount' => $secondInstallmentVal,
                            'payment_method' => PaymentMethod::BankTransfer->value,
                            'reference_number' => 'FT' . rand(10000000, 99999999),
                            'recorded_by' => $owner->id,
                            'notes' => 'Khách thanh toán đợt 2.',
                        ]);
                        $payment2->allocations()->create([
                            'payment_schedule_id' => $schedules[1]->id,
                            'allocated_amount' => $secondInstallmentVal,
                        ]);
                    }
                }
            }
        });
    }
}
