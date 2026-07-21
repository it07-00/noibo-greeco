<div>
    <div class="app-page-head d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="clearfix">
            <h1 class="app-page-title">Kế hoạch Marketing & Nội dung</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Kế hoạch Marketing
                    </li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="btn-group" role="group" aria-label="View mode">
                <button type="button" class="btn btn-outline-primary btn-sm {{ $viewMode === 'calendar' ? 'active' : '' }}" wire:click="setViewMode('calendar')">
                    <i class="fi fi-rr-calendar me-1"></i> Dạng Lịch
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm {{ $viewMode === 'list' ? 'active' : '' }}" wire:click="setViewMode('list')">
                    <i class="fi fi-rr-list me-1"></i> Dạng Danh sách
                </button>
            </div>
            @can('create', App\Models\MarketingPlan::class)
                <button type="button" class="btn btn-primary waves-effect waves-light"
                    wire:click="openCreate('{{ date('Y-m-d') }}')" wire:loading.attr="disabled">
                    <i class="fi fi-rr-plus me-1"></i> Tạo kế hoạch bài viết
                </button>
            @endcan
        </div>
    </div>

    {{-- ── Filter Bar ───────────────────────────────────────────────────────── --}}
    <div class="card mb-3 shadow-sm border-0">
        <div class="card-body py-2">
            <div class="row g-2 align-items-center">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label mb-0 text-muted small fw-semibold">Danh mục bài viết</label>
                    <select class="form-select form-select-sm" wire:model.live="filterCategory">
                        <option value="all">Tất cả danh mục</option>
                        @foreach ($categoriesEnum as $cat)
                            <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label mb-0 text-muted small fw-semibold">Trạng thái phê duyệt</label>
                    <select class="form-select form-select-sm" wire:model.live="filterStatus">
                        <option value="all">Tất cả trạng thái</option>
                        @foreach ($statusesEnum as $st)
                            <option value="{{ $st->value }}">{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label mb-0 text-muted small fw-semibold">Người soạn bài</label>
                    <select class="form-select form-select-sm" wire:model.live="filterCreatorId">
                        <option value="0">Tất cả thành viên</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label mb-0 text-muted small fw-semibold">Tìm kiếm</label>
                    <input type="text" class="form-control form-control-sm" placeholder="Tìm tiêu đề, nội dung..." wire:model.live.debounce.300ms="search">
                </div>
            </div>
        </div>
    </div>

    @if ($viewMode === 'calendar')
        {{-- ── Calendar View ───────────────────────────────────────────────────── --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="p-4 calendar-scroll-wrapper" wire:ignore>
                            <div id="marketingCalendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- ── List / Table View ────────────────────────────────────────────────── --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 70px;">Ảnh</th>
                                <th>Tiêu đề & Nội dung</th>
                                <th>Danh mục</th>
                                <th>Thời gian xuất bản</th>
                                <th>Người soạn</th>
                                <th>Trạng thái</th>
                                <th class="text-end" style="width: 150px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($listPlans as $plan)
                                <tr>
                                    <td>
                                        @if ($plan->images->count() > 0)
                                            <img src="{{ Storage::url($plan->images->first()->file_path) }}" class="rounded object-fit-cover shadow-sm" style="width: 50px; height: 50px;" alt="Thumbnail">
                                        @else
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted border" style="width: 50px; height: 50px;">
                                                <i class="fi fi-rr-picture"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)" class="fw-bold text-dark text-decoration-none d-block mb-1" wire:click="openDetail({{ $plan->id }})">
                                            {{ $plan->title }}
                                        </a>
                                        <div class="text-muted small text-truncate" style="max-width: 380px;">
                                            {{ Str::limit(strip_tags($plan->content), 90) }}
                                        </div>
                                    </td>
                                    <td>
                                        @php $catEnum = $plan->category; @endphp
                                        @if ($catEnum)
                                            <span class="badge {{ $catEnum->badgeClass() }} text-xs">
                                                {{ $catEnum->label() }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $plan->scheduled_at->format('H:i d/m/Y') }}</div>
                                        <div class="text-muted small">{{ $plan->scheduled_at->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $plan->creator?->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $plan->status->badgeClass() }} px-2 py-1">
                                            {{ $plan->status->label() }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-info" title="Xem chi tiết" wire:click="openDetail({{ $plan->id }})">
                                                <i class="fi fi-rr-eye"></i>
                                            </button>
                                            @can('update', $plan)
                                                <button type="button" class="btn btn-outline-warning" title="Chỉnh sửa" wire:click="openEdit({{ $plan->id }})">
                                                    <i class="fi fi-rr-edit"></i>
                                                </button>
                                            @endcan
                                            @can('delete', $plan)
                                                <button type="button" class="btn btn-outline-danger" title="Xóa" wire:click="deletePlan({{ $plan->id }})" wire:confirm="Bạn có chắc chắn muốn xóa bài viết này?">
                                                    <i class="fi fi-rr-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fi fi-rr-folder-open display-6 text-muted d-block mb-2"></i>
                                        <span class="text-muted">Chưa có kế hoạch bài viết nào phù hợp.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($listPlans->hasPages())
                    <div class="p-3 border-top">
                        {{ $listPlans->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ── Modal Create / Edit Plan ─────────────────────────────────────────── --}}
    <div class="modal fade" id="modalPlanForm" tabindex="-1" aria-labelledby="modalPlanFormLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark" id="modalPlanFormLabel">
                        {{ $planId ? 'Cập nhật kế hoạch bài viết' : 'Tạo mới kế hoạch bài viết Marketing' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="resetForm"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="save">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Tiêu đề bài viết / Kế hoạch truyền thông <span class="text-danger">*</span></label>
                                <input type="text" wire:model="title" class="form-control @error('title') is-invalid @enderror" placeholder="Nhập tiêu đề bài viết truyền thông..." />
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Danh mục kế hoạch <span class="text-danger">*</span></label>
                                <select wire:model="category" class="form-select @error('category') is-invalid @enderror">
                                    @foreach ($categoriesEnum as $cat)
                                        <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                                    @endforeach
                                </select>
                                @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Thời gian dự kiến xuất bản <span class="text-danger">*</span></label>
                                <input type="datetime-local" wire:model="scheduled_at" class="form-control @error('scheduled_at') is-invalid @enderror" />
                                @error('scheduled_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Trạng thái gửi duyệt</label>
                                <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="draft">Bản nháp (Lưu tạm)</option>
                                    <option value="pending_approval">Gửi duyệt (Ban quản lý)</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- ── Rich Text Editor (Quill.js) ───────────────────────────────── --}}
                            <div class="col-12" wire:ignore>
                                <label class="form-label fw-semibold">Nội dung bài viết (Rich Text Editor)</label>
                                <div id="quillEditor" style="height: 260px; background-color: #fff;" class="rounded-bottom border"></div>
                                <input type="hidden" id="contentHiddenInput" wire:model="content">
                            </div>
                            @error('content') <div class="text-danger small mt-1 d-block">{{ $message }}</div> @enderror

                            <div class="col-12">
                                <label class="form-label fw-semibold">Hình ảnh minh họa / Đính kèm (Upload ảnh)</label>
                                <input type="file" wire:model="newImages" multiple accept="image/*" class="form-control @error('newImages.*') is-invalid @enderror">
                                <div class="form-text">Có thể chọn nhiều hình ảnh (JPG, PNG, WEBP, tối đa 10MB/ảnh).</div>
                                @error('newImages.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                                {{-- Image Upload Progress --}}
                                <div wire:loading wire:target="newImages" class="text-primary mt-2 small">
                                    <div class="spinner-border spinner-border-sm me-1" role="status"></div> Đang tải ảnh lên...
                                </div>

                                {{-- Existing Images Preview --}}
                                @if (count($existingImages) > 0)
                                    <div class="mt-3">
                                        <label class="form-label small fw-semibold text-muted">Hình ảnh hiện tại:</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($existingImages as $img)
                                                <div class="position-relative border rounded p-1 bg-white" style="width: 90px; height: 90px;">
                                                    <img src="{{ $img['url'] }}" class="w-100 h-100 object-fit-cover rounded" alt="{{ $img['name'] }}">
                                                    <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 m-1 rounded-circle p-1 d-flex align-items-center justify-content-center" title="Xóa ảnh này" wire:click="markForImageDeletion({{ $img['id'] }})">
                                                        <i class="fi fi-rr-cross" style="font-size: 10px;"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- New Images Preview --}}
                                @if (count($newImages) > 0)
                                    <div class="mt-3">
                                        <label class="form-label small fw-semibold text-muted">Hình ảnh vừa chọn:</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($newImages as $index => $imgFile)
                                                @if ($imgFile && method_exists($imgFile, 'temporaryUrl'))
                                                    <div class="position-relative border rounded p-1 bg-white" style="width: 90px; height: 90px;">
                                                        <img src="{{ $imgFile->temporaryUrl() }}" class="w-100 h-100 object-fit-cover rounded" alt="Preview">
                                                        <button type="button" class="btn btn-secondary btn-xs position-absolute top-0 end-0 m-1 rounded-circle p-1 d-flex align-items-center justify-content-center" title="Hủy ảnh này" wire:click="removeNewImage({{ $index }})">
                                                            <i class="fi fi-rr-cross" style="font-size: 10px;"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Ghi chú nội bộ</label>
                                <textarea wire:model="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" placeholder="Ghi chú thêm cho người duyệt hoặc đội nhóm..."></textarea>
                                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 text-end border-top pt-3">
                                <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal" wire:click="resetForm">Hủy</button>
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <i class="fi fi-rr-disk me-1"></i> Lưu kế hoạch
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Modal View Detail & Approval ─────────────────────────────────────── --}}
    <div class="modal fade" id="modalPlanDetail" tabindex="-1" aria-labelledby="modalPlanDetailLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                @if ($selectedPlan)
                    <div class="modal-header align-items-start bg-light">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge {{ $selectedPlan->status->badgeClass() }} px-2 py-1 fs-6">
                                    {{ $selectedPlan->status->label() }}
                                </span>
                                <span class="text-muted small">
                                    <i class="fi fi-rr-clock me-1"></i>Dự kiến xuất bản: <strong>{{ $selectedPlan->scheduled_at->format('H:i - d/m/Y') }}</strong>
                                </span>
                            </div>
                            <h4 class="modal-title fw-bold text-dark mb-0">{{ $selectedPlan->title }}</h4>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        {{-- Rejection reason alert --}}
                        @if ($selectedPlan->status === App\Enums\MarketingPlanStatus::Rejected && $selectedPlan->rejection_reason)
                            <div class="alert alert-danger border-danger shadow-sm mb-4">
                                <div class="fw-bold text-danger mb-1"><i class="fi fi-rr-exclamation me-1"></i>Lý do từ chối phê duyệt:</div>
                                <div>{{ $selectedPlan->rejection_reason }}</div>
                            </div>
                        @endif

                        {{-- Metadata --}}
                        <div class="row g-3 mb-4 pb-3 border-bottom">
                            <div class="col-sm-6">
                                <div class="text-muted small fw-semibold">Danh mục bài viết:</div>
                                <div class="mt-1">
                                    @php $catEnum = $selectedPlan->category; @endphp
                                    @if ($catEnum)
                                        <span class="badge {{ $catEnum->badgeClass() }} px-2 py-1 fs-6">
                                            {{ $catEnum->label() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small fw-semibold">Người soạn bài:</div>
                                <div class="fw-bold text-dark mt-1">{{ $selectedPlan->creator?->name ?? 'N/A' }}</div>
                            </div>
                            @if ($selectedPlan->approved_by)
                                <div class="col-sm-6">
                                    <div class="text-muted small fw-semibold">Người xử lý duyệt:</div>
                                    <div class="fw-bold text-dark mt-1">
                                        {{ $selectedPlan->approver?->name ?? 'N/A' }} ({{ $selectedPlan->approved_at?->format('H:i d/m/Y') }})
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Rich Text Content Display --}}
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-2"><i class="fi fi-rr-document-signed me-1"></i>Nội dung truyền thông:</h6>
                            <div class="p-3 bg-light rounded text-dark ql-editor-preview" style="line-height: 1.6;">
                                {!! $selectedPlan->content ?: '<em class="text-muted">Chưa nhập nội dung chi tiết.</em>' !!}
                            </div>
                        </div>

                        {{-- Images Gallery --}}
                        @if ($selectedPlan->images->count() > 0)
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark mb-2"><i class="fi fi-rr-picture me-1"></i>Hình ảnh đính kèm ({{ $selectedPlan->images->count() }} ảnh):</h6>
                                <div class="row g-2">
                                    @foreach ($selectedPlan->images as $img)
                                        <div class="col-6 col-md-4">
                                            <a href="{{ Storage::url($img->file_path) }}" target="_blank" class="d-block border rounded overflow-hidden shadow-sm" style="height: 160px;">
                                                <img src="{{ Storage::url($img->file_path) }}" class="w-100 h-100 object-fit-cover hover-zoom" alt="{{ $img->file_name }}">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($selectedPlan->notes)
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><i class="fi fi-rr-notebook me-1"></i>Ghi chú nội bộ:</h6>
                                <div class="text-muted italic bg-light p-2 rounded">{{ $selectedPlan->notes }}</div>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer justify-content-between bg-light">
                        <div>
                            @can('delete', $selectedPlan)
                                <button type="button" class="btn btn-outline-danger btn-sm" wire:click="deletePlan({{ $selectedPlan->id }})" wire:confirm="Xóa kế hoạch này?">
                                    <i class="fi fi-rr-trash me-1"></i> Xóa
                                </button>
                            @endcan
                        </div>
                        <div class="d-flex gap-2">
                            @if (($selectedPlan->status === App\Enums\MarketingPlanStatus::Draft || $selectedPlan->status === App\Enums\MarketingPlanStatus::Rejected) && (int)$selectedPlan->created_by === (int)auth()->id())
                                <button type="button" class="btn btn-warning waves-effect waves-light" wire:click="submitForReview({{ $selectedPlan->id }})">
                                    <i class="fi fi-rr-paper-plane me-1"></i> Gửi phê duyệt
                                </button>
                            @endif

                            @can('approve', App\Models\MarketingPlan::class)
                                @if ($selectedPlan->status !== App\Enums\MarketingPlanStatus::Approved)
                                    <button type="button" class="btn btn-success waves-effect waves-light" wire:click="approvePlan({{ $selectedPlan->id }})">
                                        <i class="fi fi-rr-check-circle me-1"></i> Phê duyệt bài viết
                                    </button>
                                @endif
                                @if ($selectedPlan->status !== App\Enums\MarketingPlanStatus::Rejected)
                                    <button type="button" class="btn btn-outline-danger waves-effect waves-light" wire:click="openRejectModal({{ $selectedPlan->id }})">
                                        <i class="fi fi-rr-cross-circle me-1"></i> Từ chối duyệt
                                    </button>
                                @endif
                            @endcan

                            @can('update', $selectedPlan)
                                <button type="button" class="btn btn-primary waves-effect waves-light" wire:click="openEdit({{ $selectedPlan->id }})">
                                    <i class="fi fi-rr-edit me-1"></i> Chỉnh sửa
                                </button>
                            @endcan

                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Modal Reject Reason ─────────────────────────────────────────────── --}}
    <div class="modal fade" id="modalRejectReason" tabindex="-1" aria-labelledby="modalRejectReasonLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger" id="modalRejectReasonLabel">Từ chối phê duyệt kế hoạch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nhập lý do từ chối & Yêu cầu chỉnh sửa <span class="text-danger">*</span></label>
                        <textarea wire:model="rejection_reason" class="form-control @error('rejection_reason') is-invalid @enderror" rows="4" placeholder="Nhập chi tiết lý do từ chối để bộ phận Marketing nắm thông tin và sửa lại..."></textarea>
                        @error('rejection_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger waves-effect waves-light" wire:click="confirmReject">
                        Xác nhận từ chối
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/duty-schedule.css') }}?v=1.2.0">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <style>
        .hover-zoom { transition: transform .2s ease; }
        .hover-zoom:hover { transform: scale(1.04); }
        .ql-editor-preview p { margin-bottom: 0.5rem; }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/fullcalendar.global.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        let mCalendarInstance = null;
        let mCalendarWireId = null;
        let quillInstance = null;

        function initQuillEditor() {
            const container = document.getElementById('quillEditor');
            if (!container || quillInstance) return;

            quillInstance = new Quill(container, {
                theme: 'snow',
                placeholder: 'Soạn thảo nội dung bài viết truyền thông...',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['blockquote', 'code-block'],
                        ['link', 'clean']
                    ]
                }
            });

            quillInstance.on('text-change', function() {
                const html = quillInstance.root.innerHTML;
                const hiddenInput = document.getElementById('contentHiddenInput');
                if (hiddenInput) {
                    hiddenInput.value = html;
                    hiddenInput.dispatchEvent(new Event('input'));
                }
            });
        }

        function setQuillContent(html) {
            if (!quillInstance) initQuillEditor();
            if (quillInstance) {
                quillInstance.root.innerHTML = html || '';
            }
        }

        function showBootstrapModal(modalId) {
            const modalEl = document.getElementById(modalId);
            if (!modalEl || typeof bootstrap === 'undefined') return;
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }

        function hideBootstrapModal(modalId) {
            const modalEl = document.getElementById(modalId);
            if (!modalEl || typeof bootstrap === 'undefined') return;
            bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        }

        function initMarketingCalendar() {
            const calendarEl = document.getElementById('marketingCalendar');
            if (!calendarEl) return;

            if (typeof Livewire === 'undefined') {
                document.addEventListener('livewire:init', initMarketingCalendar, { once: true });
                return;
            }

            const componentEl = calendarEl.closest('[wire\\:id]');
            if (!componentEl) return;
            const currentWireId = componentEl.getAttribute('wire:id');
            const wire = Livewire.find(currentWireId);
            if (!wire) return;

            if (mCalendarInstance && mCalendarWireId === currentWireId) {
                mCalendarInstance.refetchEvents();
                return;
            }

            if (mCalendarInstance) {
                mCalendarInstance.destroy();
            }

            mCalendarWireId = currentWireId;

            mCalendarInstance = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                fixedWeekCount: false,
                locale: 'vi',
                firstDay: 1,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                buttonText: {
                    today: 'Hôm nay',
                    month: 'Tháng'
                },
                editable: false,
                selectable: true,
                dayCellContent: function(arg) {
                    const numberEl = document.createElement('span');
                    const cleanNum = arg.dayNumberText.replace('thg', '').replace('tháng', '').replace(/[a-zA-Z]/g, '').trim();
                    numberEl.className = arg.isToday ? 'day-cell-today-number' : 'day-cell-number';
                    numberEl.innerText = cleanNum;

                    const rightContainer = document.createElement('div');
                    rightContainer.className = 'day-cell-actions';

                    @can('create', App\Models\MarketingPlan::class)
                        const plusBtn = document.createElement('button');
                        plusBtn.type = 'button';
                        plusBtn.className = 'btn-plus-day';
                        plusBtn.innerHTML = '<i class="fi fi-rr-plus" style="font-size: 8px; line-height: 1;"></i>';
                        plusBtn.onclick = function(e) {
                            e.stopPropagation();
                            const year = arg.date.getFullYear();
                            const month = String(arg.date.getMonth() + 1).padStart(2, '0');
                            const day = String(arg.date.getDate()).padStart(2, '0');
                            wire.openCreate(`${year}-${month}-${day}`);
                        };
                        rightContainer.appendChild(plusBtn);
                    @endcan

                    return { domNodes: [numberEl, rightContainer] };
                },
                eventContent: function(arg) {
                    const props = arg.event.extendedProps;
                    const card = document.createElement('div');
                    card.className = `greeco-event-card border p-1 rounded shadow-2xs`;

                    let statusBadgeClass = 'bg-secondary text-white';
                    if (props.status === 'pending_approval') statusBadgeClass = 'bg-warning text-dark';
                    else if (props.status === 'approved') statusBadgeClass = 'bg-success text-white';
                    else if (props.status === 'rejected') statusBadgeClass = 'bg-danger text-white';

                    let imgHtml = props.thumbnail_url 
                        ? `<img src="${props.thumbnail_url}" style="width: 24px; height: 24px; object-fit: cover; border-radius: 3px;" class="me-1 border">`
                        : `<i class="fi fi-rr-document me-1"></i>`;

                    card.innerHTML = `
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="badge ${statusBadgeClass} text-xs me-1">${props.status_label}</span>
                            <span class="small text-muted">${props.category_label}</span>
                        </div>
                        <div class="fw-bold text-dark text-truncate mt-1 d-flex align-items-center" style="font-size: 11px;">
                            ${imgHtml}
                            <span class="text-truncate">${props.raw_title}</span>
                        </div>
                    `;

                    return { domNodes: [card] };
                },
                events: function(info, successCallback, failureCallback) {
                    wire.getEvents(info.startStr, info.endStr)
                        .then(events => successCallback(events))
                        .catch(err => failureCallback(err));
                },
                dateClick: function(info) {
                    @can('create', App\Models\MarketingPlan::class)
                        wire.openCreate(info.dateStr);
                    @endcan
                },
                eventClick: function(info) {
                    const dbId = info.event.extendedProps.db_id;
                    if (dbId) {
                        wire.openDetail(dbId);
                    }
                }
            });

            mCalendarInstance.render();
        }

        document.addEventListener('DOMContentLoaded', () => {
            initMarketingCalendar();
            setTimeout(initQuillEditor, 300);

            Livewire.on('marketing:open-create', (eventData) => {
                setQuillContent(eventData?.content || '');
                showBootstrapModal('modalPlanForm');
            });

            Livewire.on('marketing:open-edit', (eventData) => {
                setQuillContent(eventData?.content || '');
                showBootstrapModal('modalPlanForm');
            });

            Livewire.on('marketing:open-detail', () => {
                showBootstrapModal('modalPlanDetail');
            });

            Livewire.on('marketing:close-detail', () => {
                hideBootstrapModal('modalPlanDetail');
            });

            Livewire.on('marketing:open-reject-modal', () => {
                showBootstrapModal('modalRejectReason');
            });

            Livewire.on('marketing:close-reject-modal', () => {
                hideBootstrapModal('modalRejectReason');
            });

            Livewire.on('marketing:saved', (data) => {
                hideBootstrapModal('modalPlanForm');
                if (mCalendarInstance) mCalendarInstance.refetchEvents();
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: data.message || 'Kế hoạch Marketing đã được lưu thành công.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });

            Livewire.on('marketing:filter-changed', () => {
                if (mCalendarInstance) mCalendarInstance.refetchEvents();
            });

            Livewire.on('swal:alert', (data) => {
                if (window.Swal) {
                    Swal.fire({
                        icon: data.icon || 'info',
                        title: data.title || '',
                        text: data.text || '',
                    });
                }
            });
        });

        document.addEventListener('livewire:navigated', () => {
            initMarketingCalendar();
            setTimeout(initQuillEditor, 300);
        });
    </script>
@endpush
