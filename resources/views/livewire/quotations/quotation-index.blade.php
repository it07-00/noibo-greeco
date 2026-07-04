<div class="sales-page py-4">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/sales.css') }}?v=1.0.0">
    @endpush

    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <div class="text-uppercase text-success fw-semibold sales-eyebrow">Pipeline kinh doanh</div>
            <h1 class="h3 mb-1">Theo dõi báo giá</h1>
            <p class="sales-supporting-text mb-0">Theo dõi từ bản nháp đến khi chuyển thành hợp đồng.</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary sales-primary-action">
                <i class="fi fi-rr-users-alt me-2" aria-hidden="true"></i>
                Khách hàng
            </a>
            @can('create', \App\Models\Quotation::class)
                <button type="button" class="btn btn-success sales-primary-action" wire:click="openCreate">
                    <i class="fi fi-rr-plus me-2" aria-hidden="true"></i>
                    Tạo báo giá
                </button>
            @endcan
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Tổng báo giá</div>
                    <div class="sales-kpi-value">{{ number_format($summary['total']) }}</div>
                    <div class="small sales-supporting-text">Tất cả trạng thái</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Đang theo dõi</div>
                    <div class="sales-kpi-value text-warning">{{ number_format($summary['following_up']) }}</div>
                    <div class="small sales-supporting-text">Đã gửi hoặc chăm sóc</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Thành công</div>
                    <div class="sales-kpi-value text-success">{{ number_format($summary['won']) }}</div>
                    <div class="small sales-supporting-text">Sẵn sàng tạo hợp đồng</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Giá trị pipeline</div>
                    <div class="sales-kpi-value sales-money">{{ number_format($summary['pipeline_value'], 0, ',', '.') }}₫</div>
                    <div class="small sales-supporting-text">Chưa kết thúc</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <label for="quotationSearch" class="form-label">Tìm kiếm</label>
                    <div class="input-group sales-search">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fi fi-rr-search sales-supporting-text" aria-hidden="true"></i>
                        </span>
                        <input
                            id="quotationSearch"
                            type="search"
                            class="form-control border-start-0 ps-0"
                            placeholder="Số báo giá, khách hàng hoặc mã số thuế..."
                            wire:model.live.debounce.350ms="search"
                        >
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <label for="quotationStatusFilter" class="form-label">Trạng thái</label>
                    <select id="quotationStatusFilter" class="form-select" wire:model.live="filterStatus">
                        <option value="">Tất cả trạng thái</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-lg-3">
                    <label for="quotationTypeFilter" class="form-label">Loại hợp đồng</label>
                    <select id="quotationTypeFilter" class="form-select" wire:model.live="filterContractType">
                        <option value="">Tất cả loại</option>
                        @foreach ($contractTypeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0 sales-table">
                 <thead>
                    <tr>
                        <th>STT</th>
                        <th>Báo giá / Sale</th>
                        <th>Công ty / Khách hàng</th>
                        <th style="min-width: 200px">Dịch vụ</th>
                        <th style="min-width: 220px">Tình hình làm việc</th>
                        <th>Tình hình</th>
                        <th class="text-end" style="min-width: 140px">Giá trị HĐ</th>
                        <th class="text-end">#</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quotations as $index => $quotation)
                        <tr wire:key="quotation-{{ $quotation->id }}">
                            <td>{{ ($quotations->firstItem() ?? 1) + $index }}</td>
                            <td>
                                <div class="fw-semibold">{{ $quotation->quotation_number }}</div>
                                <div class="small sales-supporting-text">{{ $quotation->issued_at?->format('d/m/Y') ?: 'Chưa có ngày' }}</div>
                                <div class="small text-primary text-nowrap mt-1" style="font-size: 0.76rem;">
                                    <i class="fi fi-rr-user me-1" style="font-size: 0.72rem;"></i>{{ $quotation->owner?->name ?: 'Chưa phân công' }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $quotation->customer->name }}</div>
                                <div class="small sales-supporting-text">{{ $quotation->customer->tax_code ?: 'Chưa có MST' }}</div>
                            </td>
                            <td>
                                @foreach ($quotation->services->take(2) as $service)
                                    <div class="small {{ ! $loop->first ? 'mt-1' : '' }}">{{ $service->service_type->label() }}</div>
                                @endforeach
                                @if ($quotation->services_count > 2)
                                    <div class="small sales-supporting-text mt-1">+{{ $quotation->services_count - 2 }} dịch vụ</div>
                                @endif
                            </td>
                            <td>
                                <div class="sales-working-situation">{{ $quotation->working_situation ?: 'Chưa cập nhật' }}</div>
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $quotation->status->badgeClass() }}">
                                    {{ $quotation->status->label() }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="text-nowrap">
                                    <span class="text-muted small" style="font-size: 0.78rem;">HĐ:</span>
                                    <span class="fw-semibold sales-number">{{ number_format($quotation->contract_value, 0, ',', '.') }}₫</span>
                                </div>
                                @if ($quotation->customer_commission > 0)
                                    <div class="small text-muted text-nowrap" style="font-size: 0.76rem; opacity: 0.85;">Gốc: {{ number_format($quotation->original_amount, 0, ',', '.') }}₫</div>
                                    <div class="small text-danger text-nowrap" style="font-size: 0.76rem;">HH: {{ number_format($quotation->customer_commission, 0, ',', '.') }}₫</div>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    @can('update', $quotation)
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary sales-icon-button"
                                            wire:click="openEdit({{ $quotation->id }})"
                                            aria-label="Chỉnh sửa {{ $quotation->quotation_number }}"
                                            title="Chỉnh sửa"
                                        >
                                            <i class="fi fi-rr-edit" aria-hidden="true"></i>
                                        </button>
                                    @endcan

                                    @if ($quotation->status === \App\Enums\QuotationStatus::Draft)
                                        @can('send', $quotation)
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary sales-action-button"
                                                wire:click="transitionStatus({{ $quotation->id }}, 'sent')"
                                                wire:confirm="Xác nhận báo giá đã được gửi cho khách hàng?"
                                            >
                                                Gửi khách
                                            </button>
                                        @endcan
                                    @elseif ($quotation->status === \App\Enums\QuotationStatus::Sent)
                                        @can('update', $quotation)
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-warning sales-action-button"
                                                wire:click="transitionStatus({{ $quotation->id }}, 'following_up')"
                                            >
                                                Theo dõi
                                            </button>
                                        @endcan
                                    @elseif ($quotation->status === \App\Enums\QuotationStatus::FollowingUp)
                                        @can('update', $quotation)
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-success sales-action-button"
                                                wire:click="transitionStatus({{ $quotation->id }}, 'won')"
                                                wire:confirm="Khách hàng đã duyệt báo giá này?"
                                            >
                                                Khách duyệt
                                            </button>
                                        @endcan
                                    @elseif ($quotation->status === \App\Enums\QuotationStatus::Won)
                                        @if ($quotation->contract)
                                            <a
                                                href="{{ route('contracts.show', $quotation->contract) }}"
                                                class="btn btn-sm btn-outline-primary sales-action-button"
                                            >
                                                Mở hợp đồng
                                            </a>
                                        @else
                                            @can('convert', $quotation)
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-success sales-action-button"
                                                    wire:click="openConvertModal({{ $quotation->id }})"
                                                >
                                                    Tạo hợp đồng
                                                </button>
                                            @endcan
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fi fi-rr-document-signed d-block fs-2 mb-2" aria-hidden="true"></i>
                                <div class="fw-semibold">Chưa có báo giá phù hợp</div>
                                <div class="small sales-supporting-text">Tạo báo giá đầu tiên hoặc thay đổi bộ lọc.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($quotations->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $quotations->links() }}
            </div>
        @endif
    </div>

    <div wire:ignore.self class="modal fade" id="quotationFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <form class="modal-content" wire:submit.prevent="save">
                <div class="modal-header">
                    <div>
                        <h2 class="h5 modal-title mb-1">
                            {{ $editingId > 0 ? 'Cập nhật báo giá' : 'Tạo báo giá' }}
                        </h2>
                        <div class="small sales-supporting-text">Báo giá được lưu nháp và có thể gửi thẳng cho khách hàng.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3 mb-4">
                        @if ($canChooseOwner)
                            <div class="col-12 col-lg-4">
                                <label for="quotationOwner" class="form-label">Sale phụ trách <span class="text-danger">*</span></label>
                                <select id="quotationOwner" class="form-select @error('formOwnerId') is-invalid @enderror" wire:model="formOwnerId">
                                    @foreach ($salesUsers as $salesUser)
                                        <option value="{{ $salesUser->id }}">{{ $salesUser->name }}</option>
                                    @endforeach
                                </select>
                                @error('formOwnerId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endif
                        <div class="col-12 col-lg-6">
                            <label for="quotationCustomer" class="form-label">Khách hàng <span class="text-danger">*</span></label>
                            <select id="quotationCustomer" class="form-select @error('formCustomerId') is-invalid @enderror" wire:model="formCustomerId">
                                <option value="0">Chọn khách hàng</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">
                                        {{ $customer->name }}{{ $customer->tax_code ? ' — '.$customer->tax_code : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('formCustomerId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-lg-6">
                            <label for="quotationContractType" class="form-label">Loại hợp đồng <span class="text-danger">*</span></label>
                            <select id="quotationContractType" class="form-select" wire:model.live="formContractType">
                                @foreach ($contractTypeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="quotationIssuedAt" class="form-label">Ngày báo giá</label>
                            <input id="quotationIssuedAt" type="date" class="form-control" wire:model="formIssuedAt">
                        </div>
                        <div class="col-6">
                            <label for="quotationValidUntil" class="form-label">Hiệu lực đến</label>
                            <input id="quotationValidUntil" type="date" class="form-control @error('formValidUntil') is-invalid @enderror" wire:model="formValidUntil">
                            @error('formValidUntil') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="quotationWorkingSituation" class="form-label">Tình hình làm việc</label>
                        <textarea id="quotationWorkingSituation" rows="3" class="form-control" wire:model="formWorkingSituation" placeholder="Lần liên hệ gần nhất, phản hồi của khách hàng, bước tiếp theo..."></textarea>
                        <div class="mt-2 small d-flex flex-wrap gap-2 align-items-center">
                            <span class="text-muted">Gợi ý nhanh:</span>
                            <button type="button" class="btn btn-sm btn-light text-secondary border" style="font-size: 0.78rem;" 
                                wire:click="$set('formWorkingSituation', 'Khách hàng đã thống nhất phạm vi và đang chuẩn bị ký hợp đồng.')">
                                Đồng ý ký HĐ
                            </button>
                            <button type="button" class="btn btn-sm btn-light text-secondary border" style="font-size: 0.78rem;" 
                                wire:click="$set('formWorkingSituation', 'Khách hàng đang xem xét phạm vi công việc.')">
                                Đang xem xét phạm vi
                            </button>
                            <button type="button" class="btn btn-sm btn-light text-secondary border" style="font-size: 0.78rem;" 
                                wire:click="$set('formWorkingSituation', 'Đang chờ phản hồi từ khách hàng.')">
                                Chờ phản hồi
                            </button>
                            <button type="button" class="btn btn-sm btn-light text-secondary border" style="font-size: 0.78rem;" 
                                wire:click="$set('formWorkingSituation', 'Khách hàng đề xuất đàm phán giá.')">
                                Đàm phán giá
                            </button>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between gap-3 mb-2">
                        <div>
                            <h3 class="h6 mb-1">Dịch vụ báo giá</h3>
                            <div class="small sales-supporting-text">Danh sách tự lọc theo loại hợp đồng đã chọn.</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-success sales-action-button" wire:click="addServiceRow">
                            <i class="fi fi-rr-plus me-1" aria-hidden="true"></i>
                            Thêm dịch vụ
                        </button>
                    </div>

                    @error('serviceRows')
                        <div class="alert alert-danger py-2" role="alert">{{ $message }}</div>
                    @enderror

                    <div class="sales-service-list">
                        @foreach ($serviceRows as $index => $row)
                            <div class="sales-service-row" wire:key="service-row-{{ $index }}">
                                <div class="row g-3 align-items-end">
                                    <div class="col-12 col-lg-5">
                                        <label class="form-label" for="serviceType{{ $index }}">Dịch vụ <span class="text-danger">*</span></label>
                                        <select id="serviceType{{ $index }}" class="form-select @error('serviceRows.'.$index.'.service_type') is-invalid @enderror" wire:model="serviceRows.{{ $index }}.service_type">
                                            @foreach ($serviceOptions as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('serviceRows.'.$index.'.service_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-5 col-lg-2">
                                        <label class="form-label" for="serviceQuantity{{ $index }}">Số lượng</label>
                                        <input id="serviceQuantity{{ $index }}" type="number" min="1" step="1" class="form-control" wire:model.live.debounce.300ms="serviceRows.{{ $index }}.quantity">
                                    </div>
                                    <x-currency-input class="col-7 col-lg-3" id="servicePrice{{ $index }}" wire="serviceRows.{{ $index }}.unit_price" label="Đơn giá (VND)" :suffix="false" />
                                    <div class="col-10 col-lg-1">
                                        <div class="small sales-supporting-text">Thành tiền</div>
                                        <div class="fw-semibold sales-number text-nowrap">
                                            {{ number_format((float) ($row['quantity'] ?: 0) * (int) ($row['unit_price'] ?: 0), 0, ',', '.') }}₫
                                        </div>
                                    </div>
                                    <div class="col-2 col-lg-1 text-end">
                                        <button
                                            type="button"
                                            class="btn btn-outline-danger sales-icon-button"
                                            wire:click="removeServiceRow({{ $index }})"
                                            @disabled(count($serviceRows) <= 1)
                                            aria-label="Xóa dịch vụ"
                                        >
                                            <i class="fi fi-rr-trash" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="serviceDescription{{ $index }}">Nội dung chi tiết</label>
                                        <textarea id="serviceDescription{{ $index }}" rows="2" class="form-control" wire:model="serviceRows.{{ $index }}.description"></textarea>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="sales-total-panel mt-3">
                        <span>Tổng dịch vụ</span>
                        <strong class="sales-number">
                            {{ number_format(collect($serviceRows)->sum(fn ($row) => (float) ($row['quantity'] ?: 0) * (int) ($row['unit_price'] ?: 0)), 0, ',', '.') }}₫
                        </strong>
                    </div>

                    <div class="row g-3 mt-1">
                        <x-currency-input class="col-6 col-lg-3" id="quotationOriginalAmount" wire="formOriginalAmount" label="Giá trị gốc" :suffix="false" placeholder="Theo tổng dịch vụ" />
                        <x-currency-input class="col-6 col-lg-3" id="quotationCommission" wire="formCustomerCommission" label="Hoa hồng KH" :suffix="false" />
                        <x-currency-input class="col-6 col-lg-3" id="quotationCommissionTax" wire="formCommissionTax" label="Thuế hoa hồng" :suffix="false" />
                        <x-currency-input class="col-6 col-lg-3" id="quotationContractValue" wire="formContractValue" label="Giá trị HĐ" :suffix="false" placeholder="Theo tổng dịch vụ" />
                    </div>

                    <div class="mt-3">
                        <label for="quotationNotes" class="form-label">Ghi chú nội bộ</label>
                        <textarea id="quotationNotes" rows="3" class="form-control" wire:model="formNotes"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save">Lưu báo giá</span>
                        <span wire:loading wire:target="save">Đang lưu...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Convert to Contract Modal -->
    <div wire:ignore.self class="modal fade" id="convertModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <form class="modal-content shadow" wire:submit.prevent="saveConversion">
                <div class="modal-header py-3">
                    <div>
                        <h2 class="h5 modal-title mb-1">Tạo hợp đồng từ báo giá</h2>
                        <div class="small sales-supporting-text">Kiểm tra thông tin, khai báo lịch thanh toán và hồ sơ ban đầu trước khi tạo.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>

                <div class="modal-body py-4">
                    <div class="sales-form-section mb-4">
                        <div class="sales-form-section-title">
                            <i class="fi fi-rr-document-signed" aria-hidden="true"></i>
                            Thông tin hợp đồng
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-md-8">
                                <label for="convertTitle" class="form-label">Tên hợp đồng <span class="text-danger">*</span></label>
                                <input id="convertTitle" class="form-control @error('convertTitle') is-invalid @enderror" wire:model="convertTitle">
                                @error('convertTitle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="convertContractNumber" class="form-label">Số hợp đồng</label>
                                <input id="convertContractNumber" class="form-control @error('convertContractNumber') is-invalid @enderror" wire:model="convertContractNumber" placeholder="Ví dụ: HD-2026-001">
                                @error('convertContractNumber') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="sales-form-section mb-4">
                        <div class="sales-form-section-title">
                            <i class="fi fi-rr-money-bill-wave" aria-hidden="true"></i>
                            Giá trị & tài chính
                        </div>
                        <div class="row g-3">
                            <x-currency-input class="col-6 col-lg-3" id="convertOriginalAmount" wire="convertOriginalAmount" label="Giá trị gốc" error="convertOriginalAmount" />
                            <x-currency-input class="col-6 col-lg-3" id="convertCustomerCommission" wire="convertCustomerCommission" label="Hoa hồng KH" error="convertCustomerCommission" />
                            <x-currency-input class="col-6 col-lg-3" id="convertCommissionTax" wire="convertCommissionTax" label="Thuế hoa hồng" error="convertCommissionTax" />
                            <div class="col-6 col-lg-3">
                                <label class="form-label">Giá trị báo giá gốc</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light" readonly value="{{ $conversionSource ? number_format($conversionSource->contract_value, 0, ',', '.') : '0' }}đ">
                                </div>
                            </div>
                            <x-currency-input class="col-12 col-lg-4" id="convertValue" wire="convertValue" label="Giá trị hợp đồng (VND)" :required="true" error="convertValue" />
                            <div class="col-12 col-lg-3">
                                <label for="convertPaymentMethod" class="form-label">Phương thức thanh toán</label>
                                <select id="convertPaymentMethod" class="form-select" wire:model="convertPaymentMethod">
                                    <option value="">Chưa thỏa thuận</option>
                                    @foreach ($convertPaymentMethodOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-lg-5">
                                <label class="form-label">Dịch vụ kế thừa từ báo giá</label>
                                <div class="sales-readonly-panel">
                                    @if ($conversionSource)
                                        {{ $conversionSource->services->pluck('service_type')->map->label()->join(', ') }}
                                    @else
                                        Dịch vụ sẽ được kế thừa tự động.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sales-form-section mb-4">
                        <div class="sales-form-section-title">
                            <i class="fi fi-rr-calendar" aria-hidden="true"></i>
                            Thời gian thực hiện
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="convertSignedAt" class="form-label">Ngày ký hợp đồng</label>
                                <input id="convertSignedAt" type="date" class="form-control" wire:model="convertSignedAt">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="convertStartsAt" class="form-label">Ngày bắt đầu</label>
                                <input id="convertStartsAt" type="date" class="form-control" wire:model="convertStartsAt">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="convertEndsAt" class="form-label">Ngày kết thúc</label>
                                <input id="convertEndsAt" type="date" class="form-control @error('convertEndsAt') is-invalid @enderror" wire:model="convertEndsAt">
                                @error('convertEndsAt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="sales-form-section mb-4">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
                            <div>
                                <div class="sales-form-section-title mb-1">
                                    <i class="fi fi-rr-calendar-clock" aria-hidden="true"></i>
                                    Lịch thanh toán
                                </div>
                                <div class="small sales-supporting-text">Có thể chia bất kỳ số đợt nào; tổng tiền phải bằng giá trị hợp đồng. Nếu chưa chốt, có thể xóa hết và bổ sung sau.</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-light sales-action-button" wire:click="addConvertPaymentRow">
                                <i class="fi fi-rr-plus me-1" aria-hidden="true"></i> Thêm đợt
                            </button>
                        </div>

                        <div class="sales-service-list">
                            @forelse ($convertPaymentRows as $index => $paymentRow)
                                <div class="sales-service-row" wire:key="convert-payment-{{ $index }}">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                        <strong>Đợt {{ $index + 1 }}</strong>
                                        <button type="button" class="btn btn-sm btn-light sales-icon-button" wire:click="removeConvertPaymentRow({{ $index }})" aria-label="Xóa đợt {{ $index + 1 }}">
                                            <i class="fi fi-rr-trash" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12 col-lg-3">
                                            <label class="form-label" for="convertPaymentName{{ $index }}">Tên đợt <span class="text-danger">*</span></label>
                                            <input id="convertPaymentName{{ $index }}" class="form-control @error('convertPaymentRows.'.$index.'.name') is-invalid @enderror" wire:model="convertPaymentRows.{{ $index }}.name">
                                            @error('convertPaymentRows.'.$index.'.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <x-currency-input class="col-7 col-lg-3" id="convertPaymentAmount{{ $index }}" wire="convertPaymentRows.{{ $index }}.amount" label="Số tiền" :required="true" error="convertPaymentRows.{{ $index }}.amount" :suffix="false" />
                                        <div class="col-5 col-lg-2">
                                            <label class="form-label" for="convertPaymentPercent{{ $index }}">Tỷ lệ (%)</label>
                                            <input id="convertPaymentPercent{{ $index }}" type="number" min="0.01" max="100" step="0.01" class="form-control" wire:model="convertPaymentRows.{{ $index }}.percentage">
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <label class="form-label" for="convertPaymentCondition{{ $index }}">Điều kiện thanh toán</label>
                                            <select id="convertPaymentCondition{{ $index }}" class="form-select" wire:model.live="convertPaymentRows.{{ $index }}.condition_type">
                                                @foreach ($paymentConditionOptions as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if (($paymentRow['condition_type'] ?? '') === \App\Enums\PaymentConditionType::Custom->value)
                                            <div class="col-12">
                                                <label class="form-label" for="convertPaymentCustom{{ $index }}">Điều kiện tùy chỉnh</label>
                                                <textarea id="convertPaymentCustom{{ $index }}" rows="2" class="form-control" wire:model="convertPaymentRows.{{ $index }}.custom_condition"></textarea>
                                            </div>
                                        @endif
                                        <div class="col-6 col-lg-3">
                                            <label class="form-label" for="convertExpectedDate{{ $index }}">Ngày dự kiến đạt điều kiện</label>
                                            <input id="convertExpectedDate{{ $index }}" type="date" class="form-control" wire:model="convertPaymentRows.{{ $index }}.expected_trigger_date">
                                        </div>
                                        <div class="col-6 col-lg-2">
                                            <label class="form-label" for="convertTermDays{{ $index }}">Thời hạn</label>
                                            <input id="convertTermDays{{ $index }}" type="number" min="0" class="form-control" wire:model="convertPaymentRows.{{ $index }}.payment_term_days" placeholder="Số ngày">
                                        </div>
                                        <div class="col-6 col-lg-3">
                                            <label class="form-label" for="convertTermUnit{{ $index }}">Cách tính ngày</label>
                                            <select id="convertTermUnit{{ $index }}" class="form-select" wire:model="convertPaymentRows.{{ $index }}.payment_term_unit">
                                                @foreach ($paymentTermUnitOptions as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6 col-lg-4">
                                            <label class="form-label" for="convertDueDate{{ $index }}">Hạn thanh toán cố định</label>
                                            <input id="convertDueDate{{ $index }}" type="date" class="form-control" wire:model="convertPaymentRows.{{ $index }}.due_date">
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="sales-empty-state py-3">Chưa khai báo lịch thanh toán. Có thể bổ sung tại hồ sơ hợp đồng sau khi tạo.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="sales-form-section mb-4">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
                            <div>
                                <div class="sales-form-section-title mb-1">
                                    <i class="fi fi-rr-folder-open" aria-hidden="true"></i>
                                    Hồ sơ chứng từ ban đầu
                                </div>
                                <div class="small sales-supporting-text">Đính kèm hợp đồng, đề nghị thanh toán hoặc hồ sơ liên quan; mỗi file được lưu thành bản nháp.</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-light sales-action-button" wire:click="addConvertDocumentRow">
                                <i class="fi fi-rr-plus me-1" aria-hidden="true"></i> Thêm chứng từ
                            </button>
                        </div>

                        <div class="sales-service-list">
                            @forelse ($convertDocumentRows as $index => $documentRow)
                                <div class="sales-service-row" wire:key="convert-document-{{ $index }}">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                        <strong>Chứng từ {{ $index + 1 }}</strong>
                                        <button type="button" class="btn btn-sm btn-light sales-icon-button" wire:click="removeConvertDocumentRow({{ $index }})" aria-label="Xóa chứng từ {{ $index + 1 }}">
                                            <i class="fi fi-rr-trash" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12 col-lg-3">
                                            <label class="form-label" for="convertDocumentType{{ $index }}">Loại chứng từ</label>
                                            <select id="convertDocumentType{{ $index }}" class="form-select" wire:model="convertDocumentRows.{{ $index }}.type">
                                                @foreach ($documentTypeOptions as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-lg-5">
                                            <label class="form-label" for="convertDocumentTitle{{ $index }}">Tên chứng từ <span class="text-danger">*</span></label>
                                            <input id="convertDocumentTitle{{ $index }}" class="form-control @error('convertDocumentRows.'.$index.'.title') is-invalid @enderror" wire:model="convertDocumentRows.{{ $index }}.title">
                                            @error('convertDocumentRows.'.$index.'.title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-6 col-lg-2">
                                            <label class="form-label" for="convertDocumentSchedule{{ $index }}">Gắn với đợt</label>
                                            <select id="convertDocumentSchedule{{ $index }}" class="form-select" wire:model="convertDocumentRows.{{ $index }}.payment_schedule_index">
                                                <option value="">Toàn hợp đồng</option>
                                                @foreach ($convertPaymentRows as $paymentIndex => $scheduleRow)
                                                    <option value="{{ $paymentIndex }}">{{ $scheduleRow['name'] ?: 'Đợt '.($paymentIndex + 1) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6 col-lg-2">
                                            <label class="form-label" for="convertDocumentExpiry{{ $index }}">Hết hiệu lực</label>
                                            <input id="convertDocumentExpiry{{ $index }}" type="date" class="form-control" wire:model="convertDocumentRows.{{ $index }}.expires_at">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label" for="convertDocumentFile{{ $index }}">File <span class="text-danger">*</span></label>
                                            <input id="convertDocumentFile{{ $index }}" type="file" class="form-control @error('convertDocumentRows.'.$index.'.file') is-invalid @enderror" wire:model="convertDocumentRows.{{ $index }}.file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                            @error('convertDocumentRows.'.$index.'.file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="sales-empty-state py-3">Chưa có chứng từ đính kèm. Có thể tải lên sau tại hồ sơ hợp đồng.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="sales-form-section">
                        <div class="sales-form-section-title">
                            <i class="fi fi-rr-edit" aria-hidden="true"></i>
                            Ghi chú nội bộ
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="convertNotes" class="form-label">Ghi chú</label>
                                <textarea id="convertNotes" rows="3" class="form-control @error('convertNotes') is-invalid @enderror" wire:model="convertNotes" placeholder="Điều khoản đặc biệt, lưu ý khi triển khai hoặc lý do điều chỉnh giá trị..."></textarea>
                                @error('convertNotes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="saveConversion">
                        <span wire:loading.remove wire:target="saveConversion">Tạo hợp đồng</span>
                        <span wire:loading wire:target="saveConversion" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.addEventListener('quotation-form:show', () => window.GreecoModal?.show('quotationFormModal'));
        window.addEventListener('quotation-form:hide', () => window.GreecoModal?.hide('quotationFormModal'));
        window.addEventListener('convert-modal:show', () => window.GreecoModal?.show('convertModal'));
        window.addEventListener('convert-modal:hide', () => window.GreecoModal?.hide('convertModal'));
    </script>
</div>
