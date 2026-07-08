<div class="sales-page py-4">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/sales.css') }}?v=1.1.0">
    @endpush

    <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Yêu cầu chi hoa hồng</h1>
            <p class="sales-supporting-text mb-0">Theo dõi yêu cầu hoa hồng của Kinh doanh và trạng thái xử lý của Kế toán.</p>
        </div>
        @if ($canCreate)
            <a href="{{ route('commissions.create') }}" class="btn btn-primary sales-primary-action">
                <i class="fi fi-rr-plus me-1" aria-hidden="true"></i>
                Tạo yêu cầu
            </a>
        @endif
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-2">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Tổng yêu cầu</div>
                    <div class="sales-kpi-value">{{ number_format($summary['total']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Dự chi</div>
                    <div class="sales-kpi-value text-secondary">{{ number_format($summary['estimated']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Đã duyệt</div>
                    <div class="sales-kpi-value text-warning">{{ number_format($summary['approved']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Đã chi</div>
                    <div class="sales-kpi-value text-success">{{ number_format($summary['paid']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Tổng tiền lọc</div>
                    <div class="sales-kpi-value sales-money">{{ number_format($summary['amount'], 0, ',', '.') }}₫</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Tổng đã chi</div>
                    <div class="sales-kpi-value sales-money text-success">{{ number_format($summary['paid_amount'], 0, ',', '.') }}₫</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h2 class="h6 mb-0">Bộ lọc</h2>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-lg-3">
                    <label for="contractTypeFilter" class="form-label">Loại hợp đồng</label>
                    <select id="contractTypeFilter" class="form-select" wire:model.live="contractTypeFilter">
                        <option value="">Tất cả loại hợp đồng</option>
                        @foreach ($contractTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-lg-2">
                    <label for="statusFilter" class="form-label">Tình trạng</label>
                    <select id="statusFilter" class="form-select" wire:model.live="statusFilter">
                        <option value="">Tất cả tình trạng</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-lg-2">
                    <label for="requestMonthFilter" class="form-label">Tháng yêu cầu</label>
                    <input id="requestMonthFilter" type="month" class="form-control" wire:model.live="requestMonthFilter">
                </div>
                <div class="col-12 col-lg-2">
                    <label for="requesterFilter" class="form-label">Người yêu cầu</label>
                    <select id="requesterFilter" class="form-select" wire:model.live="requesterFilter">
                        <option value="">Tất cả</option>
                        @foreach ($requesters as $requester)
                            <option value="{{ $requester->id }}">{{ $requester->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-lg-3">
                    <label for="commissionSearch" class="form-label">Tìm kiếm</label>
                    <input id="commissionSearch" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Số HĐ, khách hàng, người nhận...">
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between gap-3 py-3">
            <h2 class="h6 mb-0">Danh sách yêu cầu</h2>
            <span class="badge bg-primary-subtle text-primary">{{ number_format($requests->total()) }} kết quả</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 sales-table">
                <thead>
                    <tr>
                        <th>Hợp đồng / Khách hàng</th>
                        <th>Người nhận</th>
                        <th class="text-end">Số tiền</th>
                        <th class="text-center">Tình trạng</th>
                        <th>Người yêu cầu</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $request)
                        @php
                            $isOwner = auth()->id() === $request->user_id;
                            $canEditRow = $isOwner && ! in_array($request->status, [\App\Enums\CommissionRequestStatus::Approved, \App\Enums\CommissionRequestStatus::Paid], true);
                        @endphp
                        <tr wire:key="commission-request-{{ $request->id }}">
                            <td>
                                <div class="fw-semibold text-primary">{{ $request->contract?->contract_number ?: '#'.$request->contract_id }}</div>
                                <div>{{ $request->contract?->customer?->name ?: $request->contract?->title }}</div>
                                <div class="small sales-supporting-text">{{ $request->contract?->type?->label() }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $request->receiver_name }}</div>
                                <div class="small sales-supporting-text">{{ $request->receiver_phone ?: 'Chưa có SĐT' }}</div>
                                <div class="small sales-supporting-text">
                                    {{ $request->bank_code && $request->bank_number ? $request->bank_code.' - '.$request->bank_number : ($request->bank_account ?: 'Chưa có tài khoản') }}
                                </div>
                                @if ($request->qr_url)
                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" wire:click="viewRequest({{ $request->id }})">Xem QR</button>
                                @endif
                            </td>
                            <td class="text-end sales-number fw-semibold">{{ number_format($request->amount, 0, ',', '.') }}₫</td>
                            <td class="text-center">
                                <span class="badge rounded-pill {{ $request->status->badgeClass() }}">{{ $request->status->label() }}</span>
                                @if ($request->status === \App\Enums\CommissionRequestStatus::Rejected && $this->rejectionReason($request->notes))
                                    <div class="small text-danger mt-1">{{ \Illuminate\Support\Str::limit($this->rejectionReason($request->notes), 60) }}</div>
                                @endif
                            </td>
                            <td>
                                <div>{{ $request->requester?->name ?: 'Hệ thống' }}</div>
                                <div class="small sales-supporting-text">{{ $request->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-info sales-action-button" wire:click="viewRequest({{ $request->id }})">Xem</button>
                                    @if ($canApprove && $request->status === \App\Enums\CommissionRequestStatus::Estimated)
                                        <button type="button" class="btn btn-sm btn-outline-success sales-action-button" wire:click="approve({{ $request->id }})" wire:confirm="Duyệt chi yêu cầu này?">Duyệt</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger sales-action-button" wire:click="startReject({{ $request->id }})">Từ chối</button>
                                    @endif
                                    @if ($canPay && $request->status === \App\Enums\CommissionRequestStatus::Approved)
                                        <button type="button" class="btn btn-sm btn-success sales-action-button" wire:click="startPay({{ $request->id }})">Đã chi</button>
                                    @endif
                                    @if ($canEditRow)
                                        <a href="{{ route('commissions.edit', $request->id) }}" class="btn btn-sm btn-outline-primary sales-action-button">Sửa</a>
                                        <button type="button" class="btn btn-sm btn-outline-danger sales-action-button" wire:click="delete({{ $request->id }})" wire:confirm="Xóa yêu cầu này?">Xóa</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fi fi-rr-receipt d-block fs-2 sales-supporting-text mb-2" aria-hidden="true"></i>
                                <div class="fw-semibold">Chưa có yêu cầu phù hợp</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($requests->hasPages())
            <div class="card-footer bg-white">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    <div wire:ignore.self class="modal fade" id="commissionViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="h5 modal-title">Chi tiết yêu cầu hoa hồng</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeView" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    @if ($viewingRequest)
                        <div class="row g-4">
                            <div class="col-12 col-md-6">
                                <dl class="row mb-0 contract-facts">
                                    <dt class="col-sm-5">Hợp đồng</dt>
                                    <dd class="col-sm-7">{{ $viewingRequest->contract?->contract_number ?: '#'.$viewingRequest->contract_id }}</dd>
                                    <dt class="col-sm-5">Khách hàng</dt>
                                    <dd class="col-sm-7">{{ $viewingRequest->contract?->customer?->name ?: 'Chưa có' }}</dd>
                                    <dt class="col-sm-5">Người nhận</dt>
                                    <dd class="col-sm-7">{{ $viewingRequest->receiver_name }}</dd>
                                    <dt class="col-sm-5">Ngân hàng</dt>
                                    <dd class="col-sm-7">{{ $viewingRequest->bank_code ?: $viewingRequest->bank_account ?: 'Chưa có' }}</dd>
                                    <dt class="col-sm-5">Số tài khoản</dt>
                                    <dd class="col-sm-7">{{ $viewingRequest->bank_number ?: 'Chưa có' }}</dd>
                                    <dt class="col-sm-5">Số tiền</dt>
                                    <dd class="col-sm-7 fw-semibold text-danger">{{ number_format($viewingRequest->amount, 0, ',', '.') }}₫</dd>
                                    <dt class="col-sm-5">Ghi chú</dt>
                                    <dd class="col-sm-7">{{ $viewingRequest->notes ?: 'Không có' }}</dd>
                                    @if ($viewingRequest->status === \App\Enums\CommissionRequestStatus::Paid && $viewingRequest->payment_bill_path)
                                        <dt class="col-sm-5">Hóa đơn chi</dt>
                                        <dd class="col-sm-7">
                                            <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2 text-decoration-none" wire:click="downloadBill({{ $viewingRequest->id }})">
                                                <i class="fi fi-rr-download me-1"></i>Tải hóa đơn
                                            </button>
                                        </dd>
                                    @endif
                                </dl>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="bg-light border rounded d-flex align-items-center justify-content-center text-center p-3" style="min-height: 320px;">
                                    @if ($viewingRequest->qr_url)
                                        <img src="{{ $viewingRequest->qr_url }}" class="img-fluid rounded border bg-white" style="max-width: 300px;" alt="QR Code">
                                    @else
                                        <div class="sales-supporting-text">Không có thông tin VietQR.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    @if ($viewingRequest && $canApprove && $viewingRequest->status === \App\Enums\CommissionRequestStatus::Estimated)
                        <button type="button" class="btn btn-success" wire:click="approve({{ $viewingRequest->id }})">Duyệt chi</button>
                    @endif
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" wire:click="closeView">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="commissionRejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" wire:submit.prevent="reject">
                <div class="modal-header">
                    <h2 class="h5 modal-title">Từ chối yêu cầu</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <label for="rejectReason" class="form-label">Lý do từ chối <span class="text-danger">*</span></label>
                    <textarea id="rejectReason" rows="4" class="form-control @error('rejectReason') is-invalid @enderror" wire:model="rejectReason"></textarea>
                    @error('rejectReason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Từ chối</button>
                </div>
            </form>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="commissionPayModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" wire:submit.prevent="confirmPay">
                <div class="modal-header">
                    <h2 class="h5 modal-title">Xác nhận chi hoa hồng</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng" wire:click="closePayModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="paymentBillFile" class="form-label">Hóa đơn/Ủy nhiệm chi <span class="text-danger">*</span></label>
                        <input type="file" id="paymentBillFile" class="form-control @error('paymentBillFile') is-invalid @enderror" wire:model="paymentBillFile">
                        @error('paymentBillFile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Định dạng hỗ trợ: PDF, JPG, JPEG, PNG. Dung lượng tối đa: 10 MB.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" wire:click="closePayModal">Hủy</button>
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Xác nhận chi
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('commission-view:show', () => window.GreecoModal?.show('commissionViewModal'));
                Livewire.on('commission-view:hide', () => window.GreecoModal?.hide('commissionViewModal'));
                Livewire.on('commission-reject:show', () => window.GreecoModal?.show('commissionRejectModal'));
                Livewire.on('commission-reject:hide', () => window.GreecoModal?.hide('commissionRejectModal'));
                Livewire.on('commission-pay:show', () => window.GreecoModal?.show('commissionPayModal'));
                Livewire.on('commission-pay:hide', () => window.GreecoModal?.hide('commissionPayModal'));
            });
        </script>
    @endpush
</div>
