<details wire:ignore.self class="card border-0 shadow-sm mb-3">
    <summary class="card-header">Bộ lọc chi tiết và sắp xếp</summary>
    <div class="card-body row g-3">
        @foreach (['signed_from' => 'Ngày ký từ', 'signed_to' => 'Ngày ký đến', 'submitted_from' => 'Ngày trình từ', 'submitted_to' => 'Ngày trình đến'] as $field => $label)
            <div class="col-6 col-lg-3">
                <label for="filter-{{ $field }}" class="form-label">{{ $label }}</label>
                <input id="filter-{{ $field }}" type="date" class="form-control" wire:model.live="filter.{{ $field }}">
            </div>
        @endforeach
        @foreach (['owner_id' => ['Nhân viên phụ trách', $staff], 'department_id' => ['Phòng ban', $departments], 'handler_id' => ['Nhà thầu phụ', $handlers]] as $field => [$label, $options])
            <div class="col-12 col-md-4">
                <label for="filter-{{ $field }}" class="form-label">{{ $label }}</label>
                <select id="filter-{{ $field }}" class="form-select" wire:model.live="filter.{{ $field }}">
                    <option value="">Tất cả</option>
                    @foreach ($options as $option)<option value="{{ $option->id }}">{{ $option->name }}</option>@endforeach
                </select>
            </div>
        @endforeach
        @foreach (['renewal_status' => ['Tái ký', $renewalOptions], 'voucher_status' => ['Chứng từ', $voucherOptions], 'payment_method' => ['Thanh toán', $paymentOptions], 'service_type' => ['Dịch vụ', $serviceOptions]] as $field => [$label, $options])
            <div class="col-12 col-md-4">
                <label for="filter-{{ $field }}" class="form-label">{{ $label }}</label>
                <select id="filter-{{ $field }}" class="form-select" wire:model.live="filter.{{ $field }}">
                    <option value="">Tất cả</option>
                    @foreach ($options as $value => $text)<option value="{{ $value }}">{{ $text }}</option>@endforeach
                </select>
            </div>
        @endforeach
        @foreach (['province' => 'Tỉnh/thành phố', 'info_source' => 'Nguồn thông tin'] as $field => $label)
            <div class="col-12 col-md-6">
                <label for="filter-{{ $field }}" class="form-label">{{ $label }}</label>
                <input id="filter-{{ $field }}" class="form-control" wire:model.live.debounce.400ms="filter.{{ $field }}">
            </div>
        @endforeach
        <div class="col-12 d-flex flex-wrap gap-3">
            @foreach (['is_offset' => 'Bù trừ', 'has_room_fund' => 'Có quỹ phòng', 'is_overdue' => 'Quá hạn', 'missing_submission_place' => 'Chưa có nơi nộp', 'hide_completed_workflow' => 'Ẩn hợp đồng đã hoàn thành tiến độ'] as $field => $label)
                <div class="form-check">
                    <input id="filter-{{ $field }}" type="checkbox" class="form-check-input" wire:model.live="filter.{{ $field }}">
                    <label for="filter-{{ $field }}" class="form-check-label">{{ $label }}</label>
                </div>
            @endforeach
        </div>
        <div class="col-12 col-md-4">
            <label for="contractSort" class="form-label">Sắp xếp theo</label>
            <select id="contractSort" class="form-select" wire:model.live="sortField">
                @foreach (['created_at' => 'Ngày tạo', 'signed_at' => 'Ngày ký', 'submitted_at' => 'Ngày trình', 'contract_number' => 'Số hợp đồng', 'report_number' => 'Số báo cáo'] as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-4">
            <label for="contractSortDirection" class="form-label">Thứ tự</label>
            <select id="contractSortDirection" class="form-select" wire:model.live="sortDirection"><option value="desc">Giảm dần</option><option value="asc">Tăng dần</option></select>
        </div>
        <div class="col-6 col-md-4">
            <label for="contractPerPage" class="form-label">Số dòng</label>
            <select id="contractPerPage" class="form-select" wire:model.live="perPage">@foreach ([10, 15, 25, 50] as $count)<option value="{{ $count }}">{{ $count }} dòng/trang</option>@endforeach</select>
        </div>
        <div class="col-12"><button type="button" class="btn btn-light" wire:click="resetFilters">Đặt lại bộ lọc</button></div>
    </div>
</details>
