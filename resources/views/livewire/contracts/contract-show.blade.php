<div class="sales-page contract-page py-4">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/sales.css') }}?v=1.1.0">
    @endpush

    @php
        $activeSchedules = $contract->paymentSchedules->whereNull('cancelled_at');
        $planState = $activeSchedules->first()?->handover_status;
        $paymentProgress = $contract->value > 0
            ? min(100, round(($financial['allocated'] / $contract->value) * 100, 1))
            : 0;
    @endphp

    <div class="d-flex flex-column flex-xl-row align-items-xl-start justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('contracts.index') }}" class="small sales-supporting-text text-decoration-none">
                <i class="fi fi-rr-arrow-left me-1" aria-hidden="true"></i>
                Danh sách hợp đồng
            </a>
            <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                <h1 class="h3 mb-0">{{ $contract->contract_number ?: 'Hợp đồng chưa đánh số' }}</h1>
                <span class="badge rounded-pill {{ $contract->status->badgeClass() }}">
                    {{ $contract->status->label() }}
                </span>
            </div>
            <p class="sales-supporting-text mb-0 mt-1">{{ $contract->title }}</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            @can('update', $contract)
                <button type="button" class="btn btn-outline-secondary sales-primary-action" wire:click="openContractInfo">
                    <i class="fi fi-rr-edit me-2" aria-hidden="true"></i>
                    Thông tin HĐ
                </button>
            @endcan

            @if ($contract->status === \App\Enums\ContractStatus::Draft)
                @can('update', $contract)
                    <button
                        type="button"
                        class="btn btn-primary sales-primary-action"
                        wire:click="transitionContract('internal_review')"
                        wire:confirm="Gửi hợp đồng sang bước kiểm tra nội bộ?"
                    >
                        Gửi kiểm tra
                    </button>
                @endcan
            @elseif ($contract->status === \App\Enums\ContractStatus::InternalReview)
                @can('approve', $contract)
                    <button
                        type="button"
                        class="btn btn-primary sales-primary-action"
                        wire:click="transitionContract('waiting_customer_signature')"
                    >
                        Duyệt, chờ khách ký
                    </button>
                @endcan
            @elseif ($contract->status === \App\Enums\ContractStatus::WaitingCustomerSignature)
                @can('activate', $contract)
                    <button
                        type="button"
                        class="btn btn-success sales-primary-action"
                        wire:click="transitionContract('active')"
                    >
                        Kích hoạt hợp đồng
                    </button>
                @endcan
            @elseif ($contract->status === \App\Enums\ContractStatus::Active)
                @can('complete', $contract)
                    <button
                        type="button"
                        class="btn btn-success sales-primary-action"
                        wire:click="transitionContract('completed')"
                        wire:confirm="Xác nhận công việc trong hợp đồng đã hoàn thành?"
                    >
                        Hoàn thành công việc
                    </button>
                @endcan
            @endif
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Giá trị hợp đồng</div>
                    <div class="sales-kpi-value sales-money">{{ number_format($contract->value, 0, ',', '.') }}₫</div>
                    <div class="small sales-supporting-text">{{ $contract->type->label() }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Tiền đã nhận</div>
                    <div class="sales-kpi-value sales-money text-success">{{ number_format($financial['received'], 0, ',', '.') }}₫</div>
                    <div class="small sales-supporting-text">{{ number_format($financial['unallocated'], 0, ',', '.') }}₫ chưa phân bổ</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Còn phải thu</div>
                    <div class="sales-kpi-value sales-money text-warning">{{ number_format($financial['outstanding'], 0, ',', '.') }}₫</div>
                    <div class="small sales-supporting-text">{{ $paymentProgress }}% đã phân bổ</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Kế hoạch thanh toán</div>
                    <div class="sales-kpi-value">{{ $activeSchedules->count() }} đợt</div>
                    <div class="small">
                        @if ($planState)
                            <span class="badge rounded-pill {{ $planState->badgeClass() }}">{{ $planState->label() }}</span>
                        @else
                            <span class="sales-supporting-text">Chưa lập kế hoạch</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex align-items-center justify-content-between gap-3 py-3">
                    <div>
                        <h2 class="h6 mb-1">Thông tin hợp đồng</h2>
                        <div class="small sales-supporting-text">Nguồn báo giá và thời gian thực hiện.</div>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row contract-facts mb-0">
                        <dt class="col-sm-4">Khách hàng</dt>
                        <dd class="col-sm-8">{{ $contract->customer->name }}</dd>
                        <dt class="col-sm-4">Người phụ trách</dt>
                        <dd class="col-sm-8">{{ $contract->owner?->name ?: 'Chưa phân công' }}</dd>
                        <dt class="col-sm-4">Ngày ký</dt>
                        <dd class="col-sm-8">{{ $contract->signed_at?->format('d/m/Y') ?: 'Chưa ký' }}</dd>
                        <dt class="col-sm-4">Thời gian thực hiện</dt>
                        <dd class="col-sm-8">
                            {{ $contract->starts_at?->format('d/m/Y') ?: 'Chưa xác định' }}
                            —
                            {{ $contract->ends_at?->format('d/m/Y') ?: 'Chưa xác định' }}
                        </dd>
                        <dt class="col-sm-4">Báo giá nguồn</dt>
                        <dd class="col-sm-8">
                            @if ($contract->quotation_id)
                                <a href="{{ route('quotations.index') }}">#{{ $contract->quotation?->quotation_number }}</a>
                            @else
                                Tạo trực tiếp
                            @endif
                        </dd>
                        <dt class="col-sm-4">Phương thức thanh toán</dt>
                        <dd class="col-sm-8">{{ $contract->payment_method?->label() ?: 'Chưa thỏa thuận' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-1">Dịch vụ</h2>
                    <div class="small sales-supporting-text">{{ $contract->services->count() }} dịch vụ trong hợp đồng.</div>
                </div>
                <div class="list-group list-group-flush">
                    @foreach ($contract->services as $service)
                        <div class="list-group-item py-3">
                            <div class="fw-medium">{{ $service->service_type->label() }}</div>
                            @if ($service->description)
                                <div class="small sales-supporting-text mt-1">{{ $service->description }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 py-3">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h2 class="h6 mb-0">Lịch thanh toán</h2>
                    @if ($planState)
                        <span class="badge rounded-pill {{ $planState->badgeClass() }}">{{ $planState->label() }}</span>
                    @endif
                </div>
                <div class="small sales-supporting-text mt-1">Kế hoạch phải thu; ngày đến hạn có thể được tính sau khi đạt điều kiện.</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if ($canManagePlan)
                    <button type="button" class="btn btn-sm btn-outline-success sales-action-button" wire:click="openScheduleCreate">
                        <i class="fi fi-rr-plus me-1" aria-hidden="true"></i>
                        Thêm đợt
                    </button>
                    @if ($activeSchedules->isNotEmpty() && ! $activeSchedules->contains(fn ($item) => $item->confirmed_at !== null))
                        <button type="button" class="btn btn-sm btn-primary sales-action-button" wire:click="submitPaymentPlan">
                            Gửi Kế toán
                        </button>
                    @endif
                @endif
                @if ($canConfirmPlan && $planState === \App\Enums\PaymentHandoverStatus::SubmittedToAccounting)
                    <button type="button" class="btn btn-sm btn-outline-danger sales-action-button" wire:click="$dispatch('payment-return:show')">
                        Trả lại
                    </button>
                    <button type="button" class="btn btn-sm btn-success sales-action-button" wire:click="confirmPaymentPlan">
                        Xác nhận kế hoạch
                    </button>
                @endif
                @if ($canRecordPayment)
                    <button type="button" class="btn btn-sm btn-success sales-action-button" wire:click="openPayment">
                        <i class="fi fi-rr-coins me-1" aria-hidden="true"></i>
                        Ghi nhận tiền về
                    </button>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 sales-table">
                <thead>
                    <tr>
                        <th>Đợt</th>
                        <th>Điều kiện</th>
                        <th class="text-end">Phải thu</th>
                        <th class="text-end">Đã phân bổ</th>
                        <th>Hạn thanh toán</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activeSchedules as $schedule)
                        @php
                            $allocated = (int) $schedule->allocations
                                ->filter(fn ($allocation) => $allocation->payment?->voided_at === null)
                                ->sum('allocated_amount');
                            $condition = $schedule->condition_type === \App\Enums\PaymentConditionType::Custom
                                ? $schedule->custom_condition
                                : $schedule->condition_type->label();
                        @endphp
                        <tr wire:key="payment-schedule-{{ $schedule->id }}">
                            <td>
                                <div class="fw-semibold">{{ $schedule->name }}</div>
                                <div class="small sales-supporting-text">
                                    {{ $schedule->percentage !== null ? $schedule->percentage.'%' : 'Theo số tiền' }}
                                </div>
                            </td>
                            <td>
                                <div>{{ $condition }}</div>
                                @if ($schedule->next_action)
                                    <div class="small text-danger mt-1">{{ $schedule->next_action }}</div>
                                @endif
                            </td>
                            <td class="text-end sales-number">{{ number_format($schedule->amount, 0, ',', '.') }}₫</td>
                            <td class="text-end sales-number">
                                <span class="{{ $allocated > 0 ? 'text-success' : 'sales-supporting-text' }}">
                                    {{ number_format($allocated, 0, ',', '.') }}₫
                                </span>
                            </td>
                            <td>
                                @if ($schedule->due_date)
                                    {{ $schedule->due_date->format('d/m/Y') }}
                                @elseif ($schedule->triggered_at)
                                    <span class="sales-supporting-text">Chưa quy định hạn</span>
                                @else
                                    <span class="sales-supporting-text">Chờ điều kiện</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $schedule->status->badgeClass() }}">
                                    {{ $schedule->status->label() }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    @if ($canManagePlan && $schedule->triggered_at === null)
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary sales-action-button"
                                            wire:click="triggerSchedule({{ $schedule->id }})"
                                            wire:confirm="Xác nhận điều kiện thanh toán của đợt này đã xảy ra hôm nay?"
                                        >
                                            Đạt điều kiện
                                        </button>
                                    @endif
                                    @if ($canManagePlan && $schedule->confirmed_at === null)
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary sales-icon-button"
                                            wire:click="openScheduleEdit({{ $schedule->id }})"
                                            aria-label="Sửa {{ $schedule->name }}"
                                        >
                                            <i class="fi fi-rr-edit" aria-hidden="true"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger sales-icon-button"
                                            wire:click="deleteSchedule({{ $schedule->id }})"
                                            wire:confirm="Xóa đợt thanh toán này?"
                                            aria-label="Xóa {{ $schedule->name }}"
                                        >
                                            <i class="fi fi-rr-trash" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fi fi-rr-calendar-clock d-block fs-2 sales-supporting-text mb-2" aria-hidden="true"></i>
                                <div class="fw-semibold">Chưa có lịch thanh toán</div>
                                <div class="small sales-supporting-text">Kinh doanh thêm các đợt theo nội dung hợp đồng.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h2 class="h6 mb-1">Giao dịch thực nhận</h2>
            <div class="small sales-supporting-text">Tiền về được giữ nguyên; việc phân bổ vào đợt được theo dõi riêng.</div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 sales-table">
                <thead>
                    <tr>
                        <th>Ngày nhận</th>
                        <th>Phương thức / tham chiếu</th>
                        <th class="text-end">Số tiền</th>
                        <th class="text-end">Đã phân bổ</th>
                        <th>Người ghi nhận</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contract->payments->whereNull('voided_at') as $payment)
                        @php
                            $allocatedPayment = (int) $payment->allocations->sum('allocated_amount');
                        @endphp
                        <tr wire:key="contract-payment-{{ $payment->id }}">
                            <td>{{ $payment->paid_at->format('d/m/Y') }}</td>
                            <td>
                                <div>{{ $payment->payment_method->label() }}</div>
                                <div class="small sales-supporting-text">{{ $payment->reference_number ?: 'Không có mã tham chiếu' }}</div>
                            </td>
                            <td class="text-end sales-number fw-semibold">{{ number_format($payment->amount, 0, ',', '.') }}₫</td>
                            <td class="text-end sales-number">
                                {{ number_format($allocatedPayment, 0, ',', '.') }}₫
                                @if ($payment->amount > $allocatedPayment)
                                    <div class="small text-warning">
                                        {{ number_format($payment->amount - $allocatedPayment, 0, ',', '.') }}₫ chờ phân bổ
                                    </div>
                                @endif
                            </td>
                            <td>{{ $payment->recorder?->name ?: 'Hệ thống' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 sales-supporting-text">Chưa ghi nhận giao dịch nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 py-3">
            <div>
                <h2 class="h6 mb-1">Chứng từ & phiên bản</h2>
                <div class="small sales-supporting-text">Mỗi lần bổ sung tạo một phiên bản mới; file cũ vẫn được giữ để đối chiếu.</div>
            </div>
            @if ($canSubmitDocument)
                <button type="button" class="btn btn-primary sales-action-button" wire:click="openDocumentCreate">
                    <i class="fi fi-rr-file-upload me-2" aria-hidden="true"></i>
                    Thêm chứng từ
                </button>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 sales-table">
                <thead>
                    <tr>
                        <th>Chứng từ</th>
                        <th>Loại / phiên bản</th>
                        <th>Liên kết thanh toán</th>
                        <th>Người gửi</th>
                        <th>Trạng thái</th>
                        <th>Phản hồi</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contract->documents as $document)
                        <tr wire:key="contract-document-{{ $document->id }}">
                            <td>
                                <div class="fw-semibold">{{ $document->title }}</div>
                                <div class="small sales-supporting-text">{{ $document->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td>
                                <div>{{ $document->type->label() }}</div>
                                <div class="small sales-supporting-text">Phiên bản {{ $document->version }}</div>
                            </td>
                            <td>{{ $document->paymentSchedule?->name ?: 'Toàn hợp đồng' }}</td>
                            <td>
                                <div>{{ $document->submitter?->name ?: 'Chưa xác định' }}</div>
                                @if ($document->reviewer)
                                    <div class="small sales-supporting-text">KT: {{ $document->reviewer->name }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $document->status->badgeClass() }}">{{ $document->status->label() }}</span>
                            </td>
                            <td>
                                <div class="document-feedback">{{ $document->review_feedback ?: 'Không có' }}</div>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                    <a href="{{ route('contracts.documents.download', [$contract, $document]) }}" class="btn btn-sm btn-light sales-icon-button" aria-label="Tải {{ $document->title }}" title="Tải file">
                                        <i class="fi fi-rr-download" aria-hidden="true"></i>
                                    </a>
                                    @if ($canSubmitDocument && $document->status === \App\Enums\DocumentStatus::Draft)
                                        <button type="button" class="btn btn-sm btn-primary sales-action-button" wire:click="submitDocument({{ $document->id }})">Gửi kiểm tra</button>
                                        <button type="button" class="btn btn-sm btn-light sales-icon-button" wire:click="deleteDocument({{ $document->id }})" wire:confirm="Xóa bản nháp chứng từ này?" aria-label="Xóa {{ $document->title }}">
                                            <i class="fi fi-rr-trash" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                    @if ($canSubmitDocument && in_array($document->status, [\App\Enums\DocumentStatus::RevisionRequired, \App\Enums\DocumentStatus::Rejected], true))
                                        <button type="button" class="btn btn-sm btn-primary sales-action-button" wire:click="openDocumentRevision({{ $document->id }})">Tạo bản sửa</button>
                                    @endif
                                    @if ($canReviewDocument && $document->status === \App\Enums\DocumentStatus::Submitted)
                                        <button type="button" class="btn btn-sm btn-primary sales-action-button" wire:click="startDocumentReview({{ $document->id }})">Nhận kiểm tra</button>
                                    @endif
                                    @if ($canReviewDocument && in_array($document->status, [\App\Enums\DocumentStatus::Submitted, \App\Enums\DocumentStatus::UnderReview], true))
                                        <button type="button" class="btn btn-sm btn-success sales-action-button" wire:click="openDocumentReview({{ $document->id }})">Đánh giá</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fi fi-rr-folder-open d-block fs-2 mb-2" aria-hidden="true"></i>
                                <div class="fw-semibold">Chưa có chứng từ hợp đồng</div>
                                <div class="small sales-supporting-text">Kinh doanh có thể tải hợp đồng, đề nghị thanh toán hoặc biên bản lên đây.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Contract document modal --}}
    <div wire:ignore.self class="modal fade" id="contractDocumentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form class="modal-content" wire:submit.prevent="saveDocument">
                <div class="modal-header">
                    <div>
                        <h2 class="h5 modal-title mb-1">{{ $documentRevisionSourceId ? 'Tạo phiên bản sửa đổi' : 'Thêm chứng từ' }}</h2>
                        <div class="small sales-supporting-text">File tối đa 20 MB; hỗ trợ PDF, Word, Excel và hình ảnh.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="documentType" class="form-label">Loại chứng từ <span class="text-danger">*</span></label>
                            <select id="documentType" class="form-select" wire:model="documentType" @disabled($documentRevisionSourceId > 0)>
                                @foreach ($documentTypeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="documentSchedule" class="form-label">Đợt thanh toán liên quan</label>
                            <select id="documentSchedule" class="form-select" wire:model="documentPaymentScheduleId">
                                <option value="">Toàn hợp đồng / Không gắn đợt</option>
                                @foreach ($activeSchedules as $schedule)
                                    <option value="{{ $schedule->id }}">{{ $schedule->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="documentTitle" class="form-label">Tên chứng từ <span class="text-danger">*</span></label>
                            <input id="documentTitle" class="form-control @error('documentTitle') is-invalid @enderror" wire:model="documentTitle">
                            @error('documentTitle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="documentExpiresAt" class="form-label">Ngày hết hiệu lực</label>
                            <input id="documentExpiresAt" type="date" class="form-control" wire:model="documentExpiresAt">
                        </div>
                        <div class="col-12">
                            <label for="documentFile" class="form-label">File chứng từ <span class="text-danger">*</span></label>
                            <input id="documentFile" type="file" class="form-control @error('documentFile') is-invalid @enderror" wire:model="documentFile" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                            @error('documentFile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div wire:loading wire:target="documentFile" class="small sales-supporting-text mt-2">Đang tải file lên...</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="saveDocument,documentFile">Lưu bản nháp</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Document review modal --}}
    <div wire:ignore.self class="modal fade" id="documentReviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h2 class="h5 modal-title mb-1">Đánh giá chứng từ</h2>
                        <div class="small sales-supporting-text">Duyệt hoặc phản hồi rõ nội dung cần bổ sung.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <label for="documentReviewFeedback" class="form-label">Nội dung phản hồi</label>
                    <textarea id="documentReviewFeedback" rows="4" class="form-control @error('documentReviewFeedback') is-invalid @enderror" wire:model="documentReviewFeedback"></textarea>
                    @error('documentReviewFeedback') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="modal-footer justify-content-between">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light" wire:click="reviewDocument('rejected')">Từ chối</button>
                        <button type="button" class="btn btn-warning" wire:click="reviewDocument('revision_required')">Yêu cầu sửa</button>
                    </div>
                    <button type="button" class="btn btn-success" wire:click="reviewDocument('approved')">Duyệt chứng từ</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Contract information modal --}}
    <div wire:ignore.self class="modal fade" id="contractInfoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form class="modal-content" wire:submit.prevent="saveContractInfo">
                <div class="modal-header">
                    <div>
                        <h2 class="h5 modal-title mb-1">Thông tin hợp đồng</h2>
                        <div class="small sales-supporting-text">Bổ sung số hợp đồng và thời gian thực hiện.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-5">
                            <label for="contractNumber" class="form-label">Số hợp đồng</label>
                            <input id="contractNumber" class="form-control @error('contractNumber') is-invalid @enderror" wire:model="contractNumber">
                            @error('contractNumber') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-7">
                            <label for="contractTitle" class="form-label">Tên hợp đồng <span class="text-danger">*</span></label>
                            <input id="contractTitle" class="form-control @error('contractTitle') is-invalid @enderror" wire:model="contractTitle">
                            @error('contractTitle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="contractSignedAt" class="form-label">Ngày ký</label>
                            <input id="contractSignedAt" type="date" class="form-control" wire:model="contractSignedAt">
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="contractStartsAt" class="form-label">Ngày bắt đầu</label>
                            <input id="contractStartsAt" type="date" class="form-control" wire:model="contractStartsAt">
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="contractEndsAt" class="form-label">Ngày kết thúc</label>
                            <input id="contractEndsAt" type="date" class="form-control @error('contractEndsAt') is-invalid @enderror" wire:model="contractEndsAt">
                            @error('contractEndsAt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="contractPaymentMethod" class="form-label">Phương thức thanh toán dự kiến</label>
                            <select id="contractPaymentMethod" class="form-select" wire:model="contractPaymentMethod">
                                <option value="">Chưa thỏa thuận</option>
                                @foreach ($paymentMethodOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="contractNotes" class="form-label">Ghi chú</label>
                            <textarea id="contractNotes" rows="3" class="form-control" wire:model="contractNotes"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Lưu thông tin</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Payment schedule modal --}}
    <div wire:ignore.self class="modal fade" id="paymentScheduleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <form class="modal-content" wire:submit.prevent="saveSchedule">
                <div class="modal-header">
                    <div>
                        <h2 class="h5 modal-title mb-1">{{ $editingScheduleId ? 'Cập nhật đợt thanh toán' : 'Thêm đợt thanh toán' }}</h2>
                        <div class="small sales-supporting-text">Ngày đến hạn có thể để trống nếu còn phụ thuộc mốc công việc.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-5">
                            <label for="scheduleName" class="form-label">Tên đợt <span class="text-danger">*</span></label>
                            <input id="scheduleName" class="form-control @error('scheduleName') is-invalid @enderror" wire:model="scheduleName">
                            @error('scheduleName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-5 col-md-3">
                            <label for="schedulePercentage" class="form-label">Tỷ lệ (%)</label>
                            <input id="schedulePercentage" type="number" min="0.01" max="100" step="0.01" class="form-control" wire:model="schedulePercentage">
                        </div>
                        <div class="col-7 col-md-4">
                            <label for="scheduleAmount" class="form-label">Số tiền (VND) <span class="text-danger">*</span></label>
                            <input id="scheduleAmount" type="number" min="1" class="form-control @error('scheduleAmount') is-invalid @enderror sales-number" wire:model="scheduleAmount">
                            @error('scheduleAmount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="scheduleConditionType" class="form-label">Điều kiện thanh toán</label>
                            <select id="scheduleConditionType" class="form-select" wire:model.live="scheduleConditionType">
                                @foreach ($conditionOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($scheduleConditionType === \App\Enums\PaymentConditionType::Custom->value)
                            <div class="col-12 col-md-6">
                                <label for="scheduleCustomCondition" class="form-label">Nội dung điều kiện</label>
                                <input id="scheduleCustomCondition" class="form-control" wire:model="scheduleCustomCondition">
                            </div>
                        @endif
                        <div class="col-12 col-md-4">
                            <label for="scheduleExpectedTriggerDate" class="form-label">Ngày mốc dự kiến</label>
                            <input id="scheduleExpectedTriggerDate" type="date" class="form-control" wire:model="scheduleExpectedTriggerDate">
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="schedulePaymentTermDays" class="form-label">Thanh toán sau</label>
                            <input id="schedulePaymentTermDays" type="number" min="0" class="form-control" wire:model="schedulePaymentTermDays" placeholder="Số ngày">
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="schedulePaymentTermUnit" class="form-label">Đơn vị ngày</label>
                            <select id="schedulePaymentTermUnit" class="form-select" wire:model="schedulePaymentTermUnit">
                                <option value="">Không xác định</option>
                                @foreach ($termUnitOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-5">
                            <label for="scheduleDueDate" class="form-label">Hạn thanh toán cố định</label>
                            <input id="scheduleDueDate" type="date" class="form-control" wire:model="scheduleDueDate">
                            <div class="form-text">Chỉ nhập nếu hợp đồng có ngày cụ thể.</div>
                        </div>
                        <div class="col-12 col-md-7">
                            <label for="scheduleNotes" class="form-label">Ghi chú</label>
                            <textarea id="scheduleNotes" rows="2" class="form-control" wire:model="scheduleNotes"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Lưu đợt thanh toán</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Record payment modal --}}
    <div wire:ignore.self class="modal fade" id="paymentRecordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <form class="modal-content" wire:submit.prevent="recordPayment">
                <div class="modal-header">
                    <div>
                        <h2 class="h5 modal-title mb-1">Ghi nhận tiền về</h2>
                        <div class="small sales-supporting-text">Có thể để lại một phần chưa phân bổ như tiền ứng trước.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <label for="paymentPaidAt" class="form-label">Ngày nhận</label>
                            <input id="paymentPaidAt" type="date" class="form-control" wire:model="paymentPaidAt">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="paymentMethod" class="form-label">Phương thức</label>
                            <select id="paymentMethod" class="form-select" wire:model="paymentMethod">
                                @foreach ($paymentMethodOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="paymentAmount" class="form-label">Số tiền thực nhận (VND) <span class="text-danger">*</span></label>
                            <input id="paymentAmount" type="number" min="1" class="form-control @error('paymentAmount') is-invalid @enderror sales-number" wire:model.live="paymentAmount">
                            @error('paymentAmount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="paymentReference" class="form-label">Mã giao dịch / tham chiếu</label>
                            <input id="paymentReference" class="form-control" wire:model="paymentReference">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="paymentNotes" class="form-label">Ghi chú</label>
                            <input id="paymentNotes" class="form-control" wire:model="paymentNotes">
                        </div>
                    </div>

                    <h3 class="h6 mb-2">Phân bổ vào các đợt</h3>
                    <div class="small sales-supporting-text mb-3">Tổng phân bổ không được vượt số tiền thực nhận.</div>
                    <div class="list-group">
                        @foreach ($allocationRows as $index => $row)
                            @php
                                $allocationSchedule = $contract->paymentSchedules->firstWhere('id', $row['payment_schedule_id']);
                            @endphp
                            @if ($allocationSchedule)
                                <label class="list-group-item d-flex align-items-center justify-content-between gap-3">
                                    <span>
                                        <span class="d-block fw-medium">{{ $allocationSchedule->name }}</span>
                                        <span class="small sales-supporting-text">
                                            Phải thu {{ number_format($allocationSchedule->amount, 0, ',', '.') }}₫
                                        </span>
                                    </span>
                                    <input
                                        type="number"
                                        min="0"
                                        class="form-control sales-number payment-allocation-input"
                                        wire:model.live="allocationRows.{{ $index }}.amount"
                                        aria-label="Phân bổ vào {{ $allocationSchedule->name }}"
                                    >
                                </label>
                            @endif
                        @endforeach
                    </div>
                    @php
                        $allocationTotal = collect($allocationRows)->sum(fn ($row) => (int) ($row['amount'] ?: 0));
                        $unallocatedPreview = max(0, (int) ($paymentAmount ?: 0) - $allocationTotal);
                    @endphp
                    <div class="sales-total-panel mt-3">
                        <span>Đã phân bổ: {{ number_format($allocationTotal, 0, ',', '.') }}₫</span>
                        <strong>Còn lại: {{ number_format($unallocatedPreview, 0, ',', '.') }}₫</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Ghi nhận giao dịch</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Return plan modal --}}
    <div wire:ignore.self class="modal fade" id="paymentReturnModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" wire:submit.prevent="returnPaymentPlan">
                <div class="modal-header">
                    <h2 class="h5 modal-title">Trả lại Kinh doanh</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <label for="returnReason" class="form-label">Nội dung cần bổ sung <span class="text-danger">*</span></label>
                    <textarea id="returnReason" rows="4" class="form-control @error('returnReason') is-invalid @enderror" wire:model="returnReason"></textarea>
                    @error('returnReason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Trả lại kế hoạch</button>
                </div>
            </form>
        </div>
    </div>
</div>
