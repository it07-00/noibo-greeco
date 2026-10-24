<!doctype html>
<html lang="vi"><head><meta charset="utf-8"><title>Danh sách hợp đồng</title></head><body>
<table border="1">
    <thead><tr><th>Số hợp đồng</th><th>Tên hợp đồng</th><th>Khách hàng</th><th>Nhà thầu phụ</th><th>Phụ trách</th><th>Phòng ban</th><th>Loại hợp đồng</th><th>Ngày ký</th><th>Ngày trình</th><th>Trạng thái</th><th>Tái ký</th><th>Chứng từ</th><th>Tỉnh</th><th>Nơi nộp</th><th>Số báo cáo</th>
        @if ($showFinancials)<th>Giá trị</th><th>Doanh số</th><th>Hoa hồng</th><th>Chi NCC</th>@endif
    </tr></thead>
    <tbody>
        @foreach ($contracts as $contract)
            <tr>
                @foreach ([$contract->contract_number, $contract->title, $contract->customer?->name, $contract->handler?->name, $contract->owner?->name, $contract->department?->name,
                    $contract->type->label(), $contract->signed_at?->format('d/m/Y'), $contract->submitted_at?->format('d/m/Y'), $contract->status->label(),
                    $contract->renewal_status?->label(), $contract->voucher_status?->label(), $contract->province, $contract->submission_place, $contract->report_number] as $value)
                    <td>{{ preg_match('/^[=+@-]/u', ltrim((string) $value)) ? "'".$value : $value }}</td>
                @endforeach
                @if ($showFinancials)<td>{{ $contract->value }}</td><td>{{ $contract->revenue }}</td><td>{{ $contract->commission }}</td><td>{{ $contract->ncc_payment }}</td>@endif
            </tr>
        @endforeach
    </tbody>
</table></body></html>
