<div class="sales-page py-4">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/sales.css') }}?v=1.0.0">
    @endpush

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="text-uppercase text-success fw-semibold sales-eyebrow">Kinh doanh</div>
            <h1 class="h3 mb-1">Khách hàng</h1>
            <p class="sales-supporting-text mb-0">Quản lý khách hàng tổ chức và khách hàng cá nhân đăng ký khóa học.</p>
        </div>

        @can('create', \App\Models\Customer::class)
            <button type="button" class="btn btn-success sales-primary-action" wire:click="openCreate">
                <i class="fi fi-rr-plus me-2" aria-hidden="true"></i>
                Thêm khách hàng
            </button>
        @endcan
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body border-bottom">
            <label class="form-label visually-hidden" for="customerSearch">Tìm kiếm khách hàng</label>
            <div class="row g-2">
                <div class="col-12 col-md">
                    <div class="input-group sales-search">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fi fi-rr-search sales-supporting-text" aria-hidden="true"></i>
                        </span>
                        <input
                            id="customerSearch"
                            type="search"
                            class="form-control border-start-0 ps-0"
                            placeholder="Tìm theo tên, mã số thuế, liên hệ hoặc số điện thoại..."
                            wire:model.live.debounce.350ms="search"
                        >
                    </div>
                </div>
                <div class="col-12 col-md-auto">
                    <label class="visually-hidden" for="customerTypeFilter">Loại khách hàng</label>
                    <select id="customerTypeFilter" class="form-select" wire:model.live="typeFilter">
                        <option value="">Tất cả khách hàng</option>
                        @foreach ($customerTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 sales-table">
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th class="d-none d-lg-table-cell">Liên hệ</th>
                        <th class="d-none d-xl-table-cell">Khu vực / ngành</th>
                        <th>Khóa học</th>
                        <th class="text-center">Báo giá</th>
                        <th class="text-center">Hợp đồng</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr wire:key="customer-{{ $customer->id }}">
                            <td>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fw-semibold text-body">{{ $customer->name }}</span>
                                    <span class="badge rounded-pill {{ $customer->type->badgeClass() }}">{{ $customer->type->label() }}</span>
                                </div>
                                <div class="small sales-supporting-text">
                                    @if ($customer->type === \App\Enums\CustomerType::Organization)
                                        MST: {{ $customer->tax_code ?: 'Chưa cập nhật' }}
                                    @else
                                        Học viên đăng ký khóa học
                                    @endif
                                </div>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <div>{{ $customer->type === \App\Enums\CustomerType::Organization ? ($customer->contact_name ?: '—') : ($customer->phone ?: '—') }}</div>
                                <div class="small sales-supporting-text">{{ $customer->phone ?: $customer->email ?: 'Chưa có thông tin' }}</div>
                            </td>
                            <td class="d-none d-xl-table-cell">
                                <div>{{ $customer->province ?: '—' }}</div>
                                <div class="small sales-supporting-text">{{ $customer->industry ?: 'Chưa phân ngành' }}</div>
                            </td>
                            <td style="min-width: 170px;">
                                @if ($customer->type === \App\Enums\CustomerType::Individual)
                                    @forelse ($customer->courses as $course)
                                        <span class="badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle me-1 mb-1">
                                            {{ $course->name }}
                                        </span>
                                    @empty
                                        <span class="small sales-supporting-text">Chưa đăng ký</span>
                                    @endforelse
                                @else
                                    <span class="sales-supporting-text">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="sales-count-pill">{{ $customer->quotations_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="sales-count-pill">{{ $customer->contracts_count }}</span>
                            </td>
                            <td class="text-end">
                                @can('update', $customer)
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary sales-icon-button"
                                        wire:click="openEdit({{ $customer->id }})"
                                        aria-label="Chỉnh sửa {{ $customer->name }}"
                                        title="Chỉnh sửa"
                                    >
                                        <i class="fi fi-rr-edit" aria-hidden="true"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fi fi-rr-users-alt d-block fs-2 sales-supporting-text mb-2" aria-hidden="true"></i>
                                <div class="fw-semibold">Chưa có khách hàng phù hợp</div>
                                <div class="small sales-supporting-text">Thử đổi từ khóa hoặc thêm khách hàng mới.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <div wire:ignore.self class="modal fade" id="customerFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <form class="modal-content" wire:submit.prevent="save">
                <div class="modal-header">
                    <div>
                        <h2 class="h5 modal-title mb-1">
                            {{ $editingId > 0 ? 'Cập nhật khách hàng' : 'Thêm khách hàng' }}
                        </h2>
                        <div class="small sales-supporting-text">Hồ sơ dùng chung cho báo giá, hợp đồng hoặc đăng ký khóa học.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="customerType" class="form-label">Loại khách hàng <span class="text-danger">*</span></label>
                            <select id="customerType" class="form-select @error('customerType') is-invalid @enderror" wire:model.live="customerType">
                                @foreach ($customerTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('customerType') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @if ($customerType === \App\Enums\CustomerType::Individual->value)
                                <div class="form-text">Dùng cho học viên đăng ký khóa học riêng lẻ, không cần mã số thuế hoặc người liên hệ.</div>
                            @endif
                        </div>
                        <div class="col-12 {{ $customerType === \App\Enums\CustomerType::Organization->value ? 'col-md-8' : '' }}">
                            <label for="customerName" class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                            <input id="customerName" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @if ($customerType === \App\Enums\CustomerType::Organization->value)
                            <div class="col-12 col-md-4">
                                <label for="customerTaxCode" class="form-label">Mã số thuế</label>
                                <div class="input-group">
                                    <input id="customerTaxCode" class="form-control @error('taxCode') is-invalid @enderror" wire:model="taxCode" placeholder="Nhập MST...">
                                    <button type="button" class="btn btn-outline-success px-3" wire:click="lookupTaxCode" wire:loading.attr="disabled" wire:target="lookupTaxCode">
                                        <span wire:loading.remove wire:target="lookupTaxCode">
                                            <i class="fi fi-rr-search align-middle me-1"></i> Tra cứu
                                        </span>
                                        <span wire:loading wire:target="lookupTaxCode" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    </button>
                                </div>
                                @error('taxCode') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="customerContact" class="form-label">Người liên hệ</label>
                                <input id="customerContact" class="form-control" wire:model="contactName">
                            </div>
                        @endif
                        <div class="col-12 {{ $customerType === \App\Enums\CustomerType::Organization->value ? 'col-md-3' : 'col-md-6' }}">
                            <label for="customerPhone" class="form-label">Điện thoại</label>
                            <input id="customerPhone" type="tel" class="form-control" wire:model="phone">
                        </div>
                        <div class="col-12 {{ $customerType === \App\Enums\CustomerType::Organization->value ? 'col-md-3' : 'col-md-6' }}">
                            <label for="customerEmail" class="form-label">Email</label>
                            <input id="customerEmail" type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="customerProvince" class="form-label">Tỉnh/thành</label>
                            <input id="customerProvince" class="form-control" wire:model="province">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="customerIndustry" class="form-label">{{ $customerType === \App\Enums\CustomerType::Organization->value ? 'Ngành nghề' : 'Nghề nghiệp / đơn vị công tác' }}</label>
                            <input id="customerIndustry" class="form-control" wire:model="industry">
                        </div>
                        <div class="col-12">
                            <label for="customerBillingAddress" class="form-label">Địa chỉ xuất hóa đơn</label>
                            <textarea id="customerBillingAddress" rows="2" class="form-control" wire:model="billingAddress"></textarea>
                        </div>
                        <div class="col-12">
                            <label for="customerWorkAddress" class="form-label">{{ $customerType === \App\Enums\CustomerType::Organization->value ? 'Địa chỉ thực hiện' : 'Địa chỉ liên hệ' }}</label>
                            <textarea id="customerWorkAddress" rows="2" class="form-control" wire:model="workAddress"></textarea>
                        </div>
                        <div class="col-12">
                            <label for="customerNotes" class="form-label">Ghi chú</label>
                            <textarea id="customerNotes" rows="3" class="form-control" wire:model="notes"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save">Lưu khách hàng</span>
                        <span wire:loading wire:target="save">Đang lưu...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.addEventListener('customer-form:show', () => window.GreecoModal?.show('customerFormModal'));
        window.addEventListener('customer-form:hide', () => window.GreecoModal?.hide('customerFormModal'));
    </script>
</div>
