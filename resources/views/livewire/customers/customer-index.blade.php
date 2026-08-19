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
            <div class="row g-2 align-items-center">
                {{-- Search --}}
                <div class="col-12 col-lg-4">
                    <div class="input-group sales-search">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fi fi-rr-search sales-supporting-text" aria-hidden="true"></i>
                        </span>
                        <input
                            id="customerSearch"
                            type="search"
                            class="form-control border-start-0 ps-0"
                            placeholder="Tìm theo tên, MST, LH, SĐT, NVCS..."
                            wire:model.live.debounce.350ms="search"
                        >
                    </div>
                </div>

                {{-- Source Filter (Bảo Châu / Greeco) --}}
                <div class="col-6 col-md-3 col-lg-2">
                    <select class="form-select" wire:model.live="sourceFilter">
                        <option value="">Tất cả hệ thống</option>
                        <option value="greeco">🌿 Greeco</option>
                        <option value="baochau">🛡️ Bảo Châu</option>
                    </select>
                </div>

                {{-- Type Filter --}}
                <div class="col-6 col-md-3 col-lg-2">
                    <select class="form-select" wire:model.live="typeFilter">
                        <option value="">Tất cả loại KH</option>
                        @foreach ($customerTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Regulatory Filter (KKKNK / KTNL) --}}
                <div class="col-6 col-md-3 col-lg-2">
                    <select class="form-select" wire:model.live="regulatoryFilter">
                        <option value="">Tất cả phân loại</option>
                        <option value="ghg_inventory">☁️ KKKNK</option>
                        <option value="energy_audit">⚡ KTNL</option>
                        <option value="regular">🏢 KH thường</option>
                    </select>
                </div>

                {{-- Caretaker Filter --}}
                <div class="col-6 col-md-3 col-lg-2">
                    <select class="form-select" wire:model.live="caretakerFilter">
                        <option value="">Tất cả người CS</option>
                        <option value="unassigned">Chưa phân công</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 sales-table">
                <thead>
                    <tr>
                        <th style="min-width: 280px;">Khách hàng</th>
                        <th class="d-none d-lg-table-cell" style="min-width: 170px;">Liên hệ</th>
                        <th class="d-none d-xl-table-cell" style="min-width: 150px;">Khu vực / ngành</th>
                        <th style="min-width: 160px;">Người chăm sóc (NVCS)</th>
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
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <span class="fw-semibold text-body">{{ $customer->name }}</span>
                                    <span class="badge rounded-pill {{ $customer->type->badgeClass() }}">{{ $customer->type->label() }}</span>
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-1 mb-1">
                                    @if(($customer->system_source ?? 'greeco') === 'baochau')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 rounded-pill d-inline-flex align-items-center"
                                              style="font-size: 0.72rem;"
                                              title="Nguồn: Hệ thống Bảo Châu">
                                            <i class="fi fi-rr-shield-check me-1"></i>Bảo Châu
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2 py-0.5 rounded-pill d-inline-flex align-items-center"
                                              style="font-size: 0.72rem;"
                                              title="Nguồn: Hệ thống Greeco">
                                            <i class="fi fi-rr-leaf me-1"></i>Greeco
                                        </span>
                                    @endif

                                    @if($customer->is_ghg_inventory)
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-0.5 rounded-pill d-inline-flex align-items-center"
                                              style="font-size: 0.72rem;"
                                              title="Thuộc danh mục Kiểm kê khí nhà kính">
                                            <i class="fi fi-rr-cloud me-1"></i>KKKNK
                                        </span>
                                    @endif
                                    @if($customer->is_energy_audit)
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0.5 rounded-pill d-inline-flex align-items-center"
                                              style="font-size: 0.72rem;"
                                              title="Thuộc danh mục Kiểm toán năng lượng">
                                            <i class="fi fi-rr-bolt me-1"></i>KTNL
                                        </span>
                                    @endif
                                    @if($customer->appendix)
                                        <span class="badge bg-body-secondary text-body border px-2 py-0.5 rounded-pill d-inline-flex align-items-center"
                                              style="font-size: 0.72rem;"
                                              title="Phụ lục: {{ $customer->appendix }}">
                                            {{ $customer->appendix }}
                                        </span>
                                    @endif
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
                            <td>
                                @php
                                    $caretakerName = $customer->caretaker?->name ?: $customer->caretaker_name;
                                @endphp
                                @if($caretakerName)
                                    <div class="d-flex align-items-center gap-1.5">
                                        <div class="rounded-circle bg-primary-subtle text-primary fw-semibold d-inline-flex align-items-center justify-content-center"
                                             style="width: 26px; height: 26px; font-size: 0.75rem;">
                                            {{ mb_substr($caretakerName, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="small fw-semibold text-body">{{ $caretakerName }}</div>
                                            @if($customer->care_status)
                                                <div class="mt-0.5">
                                                    <span class="badge {{ $customer->careStatusBadgeClass() }} px-1.5 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                                                        {{ $customer->careStatusLabel() }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">Chưa phân công</span>
                                @endif
                            </td>
                            <td style="min-width: 150px;">
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
                            <td colspan="8" class="text-center py-5">
                                <i class="fi fi-rr-users-alt d-block fs-2 sales-supporting-text mb-2" aria-hidden="true"></i>
                                <div class="fw-semibold">Chưa có khách hàng phù hợp</div>
                                <div class="small sales-supporting-text">Thử đổi từ khóa hoặc điều kiện lọc.</div>
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
                        <div class="col-12 col-md-3">
                            <label for="customerType" class="form-label">Loại khách hàng <span class="text-danger">*</span></label>
                            <select id="customerType" class="form-select @error('customerType') is-invalid @enderror" wire:model.live="customerType">
                                @foreach ($customerTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('customerType') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="customerSystemSource" class="form-label">Nguồn hệ thống</label>
                            <select id="customerSystemSource" class="form-select" wire:model="systemSource">
                                <option value="greeco">🌿 Greeco</option>
                                <option value="baochau">🛡️ Bảo Châu</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="customerCaretaker" class="form-label">Người chăm sóc (NVCS)</label>
                            <select id="customerCaretaker" class="form-select" wire:model="caretakerId">
                                <option value="">-- Chưa phân công --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="customerCareStatus" class="form-label">Trạng thái chăm sóc</label>
                            <select id="customerCareStatus" class="form-select" wire:model="careStatus">
                                <option value="">-- Chọn trạng thái --</option>
                                @foreach(\App\Enums\CustomerCareStatus::options() as $opt)
                                    <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                @endforeach
                            </select>
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

                        {{-- Classifications --}}
                        <div class="col-12">
                            <label class="form-label d-block fw-semibold mb-2">Phân loại khách hàng danh mục</label>
                            <div class="d-flex flex-wrap gap-4 p-2.5 bg-light-subtle rounded border border-light-subtle">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkGhg" wire:model="isGhgInventory">
                                    <label class="form-check-label" for="checkGhg">
                                        ☁️ Kiểm kê khí nhà kính (KKKNK)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkEnergy" wire:model="isEnergyAudit">
                                    <label class="form-check-label" for="checkEnergy">
                                        ⚡ Kiểm toán năng lượng (KTNL)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="customerProvince" class="form-label">Tỉnh/thành</label>
                            <input id="customerProvince" class="form-control" wire:model="province">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="customerIndustry" class="form-label">{{ $customerType === \App\Enums\CustomerType::Organization->value ? 'Ngành nghề' : 'Nghề nghiệp / đơn vị' }}</label>
                            <input id="customerIndustry" class="form-control" wire:model="industry">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="customerAppendix" class="form-label">Phụ lục áp dụng</label>
                            <input id="customerAppendix" class="form-control" wire:model="appendix" placeholder="Ví dụ: Phụ lục IV">
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