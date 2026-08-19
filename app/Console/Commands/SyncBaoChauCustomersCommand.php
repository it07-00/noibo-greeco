<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Customer;
use App\Services\BaoChauCustomerSyncService;
use Illuminate\Console\Command;

final class SyncBaoChauCustomersCommand extends Command
{
    protected $signature = 'sync:baochau-customers
                            {--push : Push all local customers to Bao Chau}
                            {--pull : Pull customers from Bao Chau}';

    protected $description = 'Synchronize customers between Greeco and Bao Chau via API';

    public function handle(BaoChauCustomerSyncService $syncService): int
    {
        $push = $this->option('push');
        $pull = $this->option('pull');

        if (! $push && ! $pull) {
            $push = true;
            $pull = true;
        }

        if ($pull) {
            $this->info('Đang tải dữ liệu khách hàng từ Bảo Châu...');
            $result = $syncService->pullFromBaoChau();
            $this->info("Kéo dữ liệu hoàn tất: Đã tạo {$result['created']}, Đã cập nhật {$result['updated']}, Lỗi: {$result['errors']}");
        }

        if ($push) {
            $this->info('Đang đẩy toàn bộ khách hàng từ Greeco sang Bảo Châu...');
            $customers = Customer::all();
            $total = $customers->count();
            $success = 0;
            $failed = 0;

            $bar = $this->output->createProgressBar($total);
            $bar->start();

            foreach ($customers as $customer) {
                if ($syncService->syncCustomerToBaoChau($customer)) {
                    $success++;
                } else {
                    $failed++;
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Đẩy dữ liệu hoàn tất: Thành công {$success}/{$total}, Thất bại: {$failed}");
        }

        return self::SUCCESS;
    }
}