<div class="py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <h1 class="h3 mb-0">{{ $contractId ? 'Cập nhật hợp đồng' : 'Thêm hợp đồng' }}</h1>
        <a href="{{ route('contracts.index') }}" class="btn btn-light">Danh sách hợp đồng</a>
    </div>
    <form wire:submit="save">
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
        @endif
        <div class="card mb-3">
            <div class="card-header"><h2 class="h6 mb-0">Khách hàng và phụ trách</h2></div>
            <div class="card-body row g-3">
                <div class="col-12 col-lg-6">
                    <label for="contractCustomer" class="form-label">Khách hàng <span class="text-danger">*</span></label>
                    <select id="contractCustomer" class="form-select" wire:model="form.customer_id">
                        <option value="">Chọn khách hàng</option>
                        @foreach ($customers as $customer)<option value="{{ $customer->id }}">{{ $customer->name }}</option>@endforeach
                    </select>
                    @can('create', \App\Models\Customer::class)
                        <label for="newContractCustomer" class="form-label mt-2">Hoặc nhập khách hàng mới</label>
                        <input id="newContractCustomer" class="form-control" wire:model="newCustomerName" placeholder="Nhập tên nếu chưa có trong danh sách">
                    @endcan
                </div>
                <div class="col-12 col-lg-6">
                    <label for="contractHandler" class="form-label">Nhà thầu phụ</label>
                    <select id="contractHandler" class="form-select" wire:model="form.handler_id">
                        <option value="">Chưa chọn nhà thầu phụ</option>
                        @foreach ($handlers as $handler)<option value="{{ $handler->id }}">{{ $handler->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label for="contractOwner" class="form-label">Nhân viên phụ trách <span class="text-danger">*</span></label>
                    <select id="contractOwner" class="form-select" wire:model="form.owner_id" @disabled(! $canChangeOwner)>
                        <option value="">Chọn nhân viên</option>
                        @foreach ($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label for="contractDepartment" class="form-label">Phòng ban</label>
                    <select id="contractDepartment" class="form-select" wire:model="form.department_id">
                        <option value="">Chưa chọn phòng ban</option>
                        @foreach ($departments as $department)<option value="{{ $department->id }}">{{ $department->name }}</option>@endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header"><h2 class="h6 mb-0">Thông tin hợp đồng</h2></div>
            <div class="card-body row g-3">
                @foreach (['contract_number' => 'Số hợp đồng', 'title' => 'Tên hợp đồng', 'report_number' => 'Số báo cáo', 'province' => 'Tỉnh/thành phố', 'info_source' => 'Nguồn thông tin', 'submission_place' => 'Nơi nộp hồ sơ'] as $field => $label)
                    <div class="col-12 col-md-6">
                        <label for="contract-field-{{ $field }}" class="form-label">{{ $label }} @if ($field === 'title')<span class="text-danger">*</span>@endif</label>
                        <input id="contract-field-{{ $field }}" class="form-control" wire:model="form.{{ $field }}">
                    </div>
                @endforeach
                <div class="col-12 col-md-6">
                    <label for="contractType" class="form-label">Loại hợp đồng</label>
                    <select id="contractType" class="form-select" wire:model.live="form.type">
                        @foreach ($typeOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label for="contractFormStatus" class="form-label">Trạng thái</label>
                    <select id="contractFormStatus" class="form-select" wire:model="form.status">
                        @foreach ($statusOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                    </select>
                </div>
                @foreach (['signed_at' => 'Ngày ký', 'starts_at' => 'Ngày bắt đầu', 'ends_at' => 'Ngày kết thúc', 'submitted_at' => 'Ngày trình'] as $field => $label)
                    <div class="col-6 col-lg-3">
                        <label for="contract-date-{{ $field }}" class="form-label">{{ $label }}</label>
                        <input id="contract-date-{{ $field }}" type="date" class="form-control" wire:model="form.{{ $field }}">
                    </div>
                @endforeach
                <div class="col-12 col-lg-6">
                    <label for="contractRenewal" class="form-label">Tình trạng tái ký</label>
                    <select id="contractRenewal" class="form-select" wire:model="form.renewal_status">
                        @foreach ($renewalOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div class="col-12 col-lg-6">
                    <label for="contractVoucher" class="form-label">Tình trạng chứng từ</label>
                    <select id="contractVoucher" class="form-select" wire:model="form.voucher_status">
                        <option value="">Chưa cập nhật</option>
                        @foreach ($voucherOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label for="contractDrive" class="form-label">Liên kết hồ sơ Google Drive</label>
                    <input id="contractDrive" type="url" class="form-control" wire:model="form.contract_drive_url" placeholder="https://...">
                </div>
                <div class="col-12 d-flex flex-wrap gap-3">
                    @foreach (['is_offset' => 'Bù trừ', 'has_room_fund' => 'Có quỹ phòng', 'is_overdue' => 'Quá hạn', 'is_renewal' => 'Hợp đồng tái ký'] as $field => $label)
                        <div class="form-check">
                            <input id="contract-flag-{{ $field }}" class="form-check-input" type="checkbox" wire:model.live="form.{{ $field }}">
                            <label class="form-check-label" for="contract-flag-{{ $field }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
                @if ($form['is_renewal'])
                    <div class="col-12 col-md-6">
                        <label for="contractParent" class="form-label">Hợp đồng gốc</label>
                        <select id="contractParent" class="form-select" wire:model="form.parent_contract_id">
                            <option value="">Chọn hợp đồng gốc</option>
                            @foreach ($parentOptions as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->contract_number ?: $parent->title }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>
        @if ($showFinancials)
            <div class="card mb-3">
                <div class="card-header"><h2 class="h6 mb-0">Giá trị và thanh toán</h2></div>
                <div class="card-body row g-3">
                    @foreach (['value' => 'Giá trị hợp đồng', 'original_amount' => 'Giá trị gốc', 'customer_commission' => 'Hoa hồng khách hàng', 'commission_tax' => 'Thuế hoa hồng', 'revenue' => 'Doanh số'] as $field => $label)
                        <div class="col-12 col-md-6 col-xl-4">
                            <label for="contract-money-{{ $field }}" class="form-label">{{ $label }} (VNĐ)</label>
                            <input id="contract-money-{{ $field }}" type="number" min="0" step="1" class="form-control" wire:model.blur="form.{{ $field }}">
                        </div>
                    @endforeach
                    <div class="col-12 col-md-6 col-xl-4">
                        <label for="contractPercentage" class="form-label">Tỷ lệ thanh toán (%)</label>
                        <input id="contractPercentage" type="number" min="0" max="100" step="0.01" class="form-control" wire:model.blur="form.payment_percentage">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="contractPayment" class="form-label">Phương thức thanh toán</label>
                        <select id="contractPayment" class="form-select" wire:model="form.payment_method">
                            <option value="">Chưa thỏa thuận</option>
                            @foreach ($paymentOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                        </select>
                    </div>
                    <p class="small text-muted mb-0">Các đợt thanh toán, tiền thực nhận và chi nhà thầu phụ được quản lý tại hồ sơ hợp đồng và dòng tiền.</p>
                </div>
            </div>
        @endif
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center gap-2">
                <h2 class="h6 mb-0">Dịch vụ thực hiện</h2>
                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addService">Thêm dịch vụ</button>
            </div>
            <div class="card-body">
                @foreach ($serviceRows as $index => $row)
                    <div class="row g-2 border rounded p-2 p-md-3 mb-3" wire:key="contract-service-{{ $index }}">
                        <div class="col-12 col-md-5">
                            <label for="contractService-{{ $index }}" class="form-label">Dịch vụ</label>
                            <select id="contractService-{{ $index }}" class="form-select" wire:model="serviceRows.{{ $index }}.service_type">
                                <option value="">Chọn dịch vụ</option>
                                @foreach ($serviceOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="contractServiceDescription-{{ $index }}" class="form-label">Nội dung</label>
                            <input id="contractServiceDescription-{{ $index }}" class="form-control" wire:model="serviceRows.{{ $index }}.description">
                        </div>
                        @if ($showFinancials)
                            <div class="col-8 col-md-2">
                                <label for="contractServiceAmount-{{ $index }}" class="form-label">Giá trị</label>
                                <input id="contractServiceAmount-{{ $index }}" type="number" min="0" class="form-control" wire:model="serviceRows.{{ $index }}.amount">
                            </div>
                        @endif
                        <div class="col-4 col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger" wire:click="removeService({{ $index }})" aria-label="Xóa dòng dịch vụ {{ $index + 1 }}">Xóa</button>
                        </div>
                    </div>
                @endforeach
                <label for="contractFormNotes" class="form-label">Ghi chú</label>
                <textarea id="contractFormNotes" class="form-control" wire:model="form.notes" rows="3"></textarea>
            </div>
        </div>
        @if (! $contractId && $canAssign)
            <div class="card mb-3">
                <div class="card-header"><h2 class="h6 mb-0">Giao việc khi tạo hợp đồng</h2></div>
                <div class="card-body row g-3">
                    <div class="col-12 col-lg-6">
                        <label for="createContractAssignees" class="form-label">Người thực hiện</label>
                        <select id="createContractAssignees" multiple class="form-select" wire:model="createAssignUserIds">
                            @foreach ($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
                        </select>
                        <div class="form-text">Có thể chọn nhiều người thực hiện.</div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label for="createContractExternal" class="form-label">Đơn vị/người thực hiện bên ngoài</label>
                        <input id="createContractExternal" class="form-control" wire:model="createAssignExternal">
                        <label for="createContractDeadline" class="form-label mt-2">Hạn hoàn thành</label>
                        <input id="createContractDeadline" type="date" class="form-control" wire:model="createAssignDeadline">
                    </div>
                </div>
            </div>
        @endif
        <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
            <a href="{{ route('contracts.index') }}" class="btn btn-light">Hủy</a>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Lưu hợp đồng</button>
        </div>
    </form>
</div>
