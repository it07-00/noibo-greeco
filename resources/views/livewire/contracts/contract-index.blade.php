<div class="sales-page py-4">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/sales.css') }}?v=1.1.0">
    @endpush

    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <div class="text-uppercase text-success fw-semibold sales-eyebrow">Vận hành hợp đồng</div>
            <h1 class="h3 mb-1">Hợp đồng</h1>
            <p class="sales-supporting-text mb-0">Theo dõi thực hiện, chứng từ và dòng tiền trên cùng một hồ sơ.</p>
        </div>
        <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary sales-primary-action">
            <i class="fi fi-rr-document-signed me-2" aria-hidden="true"></i>
            Theo dõi báo giá
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Tổng hợp đồng</div>
                    <div class="sales-kpi-value">{{ number_format($summary['total']) }}</div>
                    <div class="small sales-supporting-text">Tất cả trạng thái</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Đang thực hiện</div>
                    <div class="sales-kpi-value text-primary">{{ number_format($summary['active']) }}</div>
                    <div class="small sales-supporting-text">Hợp đồng đang hoạt động</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Tổng giá trị</div>
                    <div class="sales-kpi-value sales-money">{{ number_format($summary['total_value'], 0, ',', '.') }}₫</div>
                    <div class="small sales-supporting-text">Giá trị đã ký và dự kiến</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Tiền đã nhận</div>
                    <div class="sales-kpi-value sales-money text-success">{{ number_format($summary['received'], 0, ',', '.') }}₫</div>
                    <div class="small sales-supporting-text">Giao dịch chưa hủy</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <label for="contractSearch" class="form-label">Tìm kiếm</label>
                    <div class="input-group sales-search">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fi fi-rr-search sales-supporting-text" aria-hidden="true"></i>
                        </span>
                        <input
                            id="contractSearch"
                            type="search"
                            class="form-control border-start-0 ps-0"
                            placeholder="Số hợp đồng, tiêu đề, khách hàng hoặc mã số thuế..."
                            wire:model.live.debounce.350ms="search"
                        >
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <label for="contractStatusFilter" class="form-label">Trạng thái</label>
                    <select id="contractStatusFilter" class="form-select" wire:model.live="filterStatus">
                        <option value="">Tất cả trạng thái</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-lg-3">
                    <label for="contractTypeFilter" class="form-label">Loại hợp đồng</label>
                    <select id="contractTypeFilter" class="form-select" wire:model.live="filterType">
                        <option value="">Tất cả loại</option>
                        @foreach ($typeOptions as $value => $label)
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
                        <th>Hợp đồng</th>
                        <th>Khách hàng</th>
                        <th class="d-none d-xl-table-cell">Loại</th>
                        <th class="text-end">Giá trị</th>
                        <th class="text-end d-none d-lg-table-cell">Đã nhận</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contracts as $contract)
                        @php
                            $received = (int) ($contract->received_amount ?? 0);
                        @endphp
                        <tr wire:key="contract-{{ $contract->id }}">
                            <td>
                                <div class="fw-semibold">{{ $contract->contract_number ?: 'Chưa có số HĐ' }}</div>
                                <div class="small sales-supporting-text">{{ $contract->title }}</div>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $contract->customer->name }}</div>
                                <div class="small sales-supporting-text">{{ $contract->owner?->name ?: 'Chưa phân công' }}</div>
                            </td>
                            <td class="d-none d-xl-table-cell">{{ $contract->type->label() }}</td>
                            <td class="text-end sales-number">{{ number_format($contract->value, 0, ',', '.') }}₫</td>
                            <td class="text-end d-none d-lg-table-cell sales-number">
                                <span class="{{ $received > 0 ? 'text-success' : 'sales-supporting-text' }}">
                                    {{ number_format($received, 0, ',', '.') }}₫
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $contract->status->badgeClass() }}">
                                    {{ $contract->status->label() }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a
                                    href="{{ route('contracts.show', $contract) }}"
                                    class="btn btn-sm btn-outline-primary sales-action-button"
                                >
                                    Mở hồ sơ
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fi fi-rr-briefcase d-block fs-2 sales-supporting-text mb-2" aria-hidden="true"></i>
                                <div class="fw-semibold">Chưa có hợp đồng phù hợp</div>
                                <div class="small sales-supporting-text">Hợp đồng được tạo từ báo giá thành công.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($contracts->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>
</div>
