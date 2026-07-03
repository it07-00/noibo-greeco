<div>
    <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between">
        <div class="clearfix">
            <h1 class="app-page-title">Quy định Tài liệu</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Quy định Tài liệu
                    </li>
                </ol>
            </nav>
        </div>
        @if ($canManage)
            <button
                type="button"
                class="btn btn-primary waves-effect waves-light"
                wire:click="openCreate"
            >
                <i class="fi fi-rr-plus me-1"></i> Thêm quy định mới
            </button>
        @endif
    </div>

    @if ($successMessage)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fi fi-rr-check me-2"></i> {{ $successMessage }}
            <button type="button" class="btn-close" wire:click="$set('successMessage', null)" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── Summary Cards ─────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                            <i class="fi fi-rr-folder-open"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Tổng quy định</div>
                            <div class="h4 mb-0 fw-bold">{{ \App\Models\DocumentRegulation::count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                            <i class="fi fi-rr-check-circle"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Hiệu lực</div>
                            <div class="h4 mb-0 fw-bold">
                                {{ \App\Models\DocumentRegulation::where('status', 'active')->count() }} Đang áp dụng
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                            <i class="fi fi-rr-user-shield"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Phạm vi áp dụng</div>
                            <div class="h4 mb-0 fw-bold">Nội bộ hệ thống</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filter Bar ─────────────────────────────────────────────────────── --}}
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body py-2">
            <div class="row g-2 align-items-center">
                <div class="col-md-4 col-sm-6">
                    <label class="form-label mb-0 text-muted small">Tìm kiếm</label>
                    <input
                        type="text"
                        class="form-control form-control-sm"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Tìm mã, tên quy định hoặc tóm tắt..."
                    />
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label mb-0 text-muted small">Bộ phận phụ trách</label>
                    <select class="form-select form-select-sm" wire:model.live="filterOwner">
                        <option value="">Tất cả bộ phận</option>
                        @foreach ($owners as $o)
                            <option value="{{ $o }}">{{ $o }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($search || $filterOwner)
                    <div class="col-sm-auto ms-auto align-self-end">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary"
                            wire:click="$set('search', ''); $set('filterOwner', '')"
                        >
                            <i class="fi fi-rr-cross-small me-1"></i> Xóa lọc
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Regulations Table ──────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="fi fi-rr-document-signed me-2 text-primary"></i>Danh sách quy định tài liệu
            </h5>
        </div>
        <div class="card-body p-0">
            @if ($regulations->isEmpty())
                <div class="text-center py-5">
                    <i class="fi fi-rr-document-signed scale-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">Không tìm thấy quy định tài liệu nào.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-nowrap" style="width: 120px;">Mã</th>
                                <th>Tên quy định</th>
                                <th>Phụ trách</th>
                                <th>Trạng thái</th>
                                <th style="min-width: 320px;">Nóm tắt nội dung chính</th>
                                <th class="text-end pe-3" style="width: 140px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($regulations as $regulation)
                                <tr>
                                    <td class="ps-3 text-nowrap">
                                        <span class="badge bg-primary-subtle text-primary fw-semibold">{{ $regulation->code }}</span>
                                    </td>
                                    <td class="fw-semibold text-dark">{{ $regulation->title }}</td>
                                    <td class="text-muted">{{ $regulation->owner }}</td>
                                    <td>
                                        @if ($regulation->status === 'active')
                                            <span class="badge bg-success-subtle text-success border border-success">Đang áp dụng</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger">Hết hiệu lực</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-body">{{ $regulation->summary }}</span>
                                    </td>
                                    <td class="text-end pe-3 text-nowrap">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Chi tiết"
                                            wire:click="showDetails({{ $regulation->id }})"
                                        >
                                            <i class="fi fi-rr-eye"></i>
                                        </button>

                                        @if ($canManage)
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Sửa"
                                                wire:click="openEdit({{ $regulation->id }})"
                                            >
                                                <i class="fi fi-rr-edit"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Xóa"
                                                wire:click="delete({{ $regulation->id }})"
                                                wire:confirm="Bạn có chắc chắn muốn xóa quy định {{ $regulation->code }} không?"
                                            >
                                                <i class="fi fi-rr-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($regulations->hasPages())
                    <div class="p-3 border-top">
                        {{ $regulations->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ── Modal Detail ──────────────────────────────────────────────────── --}}
    <div
        wire:ignore.self
        class="modal fade"
        id="modalDetail"
        tabindex="-1"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fi fi-rr-document text-primary me-2"></i> Chi tiết quy định tài liệu
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                    @if ($selectedRegulation)
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                            <span class="badge bg-primary px-2 py-1">{{ $selectedRegulation->code }}</span>
                            <span class="badge bg-light-subtle text-dark border border-gray-300 px-2 py-1">Phụ trách: {{ $selectedRegulation->owner }}</span>
                            @if ($selectedRegulation->status === 'active')
                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1">Đang áp dụng</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">Hết hiệu lực</span>
                            @endif
                        </div>

                        <h4 class="text-dark fw-bold mb-3">{{ $selectedRegulation->title }}</h4>

                        <div class="mb-4">
                            <h6 class="fw-bold text-dark"><i class="fi fi-rr-info text-primary me-1"></i> Tóm tắt chính:</h6>
                            <div class="p-3 bg-light rounded-3 text-body">{{ $selectedRegulation->summary }}</div>
                        </div>

                        @if ($selectedRegulation->content)
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark"><i class="fi fi-rr-document-signed text-primary me-1"></i> Nội dung chi tiết:</h6>
                                <div class="p-3 border rounded-3 text-body bg-white" style="white-space: pre-wrap; font-family: system-ui; line-height: 1.6;">{{ $selectedRegulation->content }}</div>
                            </div>
                        @endif

                        @if ($selectedRegulation->file_path)
                            <div class="mb-2 p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fi fi-rr-file-pdf text-danger fs-3"></i>
                                    <div>
                                        <div class="fw-semibold text-dark">Tài liệu chính thức đính kèm</div>
                                        <div class="text-muted small">Tải về xem định dạng PDF/Word gốc</div>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-sm waves-effect"
                                    wire:click="downloadFile({{ $selectedRegulation->id }})"
                                >
                                    <i class="fi fi-rr-download me-1"></i> Tải tài liệu
                                </button>
                            </div>
                        @endif
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Modal Create/Edit Form ────────────────────────────────────────── --}}
    @if ($canManage)
        <div
            wire:ignore.self
            class="modal fade"
            id="modalForm"
            tabindex="-1"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <form wire:submit.prevent="save" class="modal-content border-0 shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="fi fi-rr-edit-alt text-primary me-2"></i>
                            {{ $regulationId ? 'Cập nhật quy định tài liệu' : 'Thêm mới quy định tài liệu' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="resetForm"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Mã quy định <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('code') is-invalid @enderror"
                                    wire:model="code"
                                    placeholder="Ví dụ: QD-TL-01"
                                />
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Tên quy định tài liệu <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('title') is-invalid @enderror"
                                    wire:model="title"
                                    placeholder="Nhập tên quy định, tài liệu..."
                                />
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phòng ban/Bộ phận phụ trách <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('owner') is-invalid @enderror"
                                    wire:model="owner"
                                    placeholder="Ví dụ: Hành chính, Kế toán, IT..."
                                />
                                @error('owner')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Trạng thái áp dụng <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                    <option value="active">Đang áp dụng</option>
                                    <option value="inactive">Hết hiệu lực</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Tóm tắt ngắn gọn <span class="text-danger">*</span></label>
                                <textarea
                                    class="form-control @error('summary') is-invalid @enderror"
                                    wire:model="summary"
                                    rows="2"
                                    placeholder="Mô tả tóm tắt nội dung quy định..."
                                ></textarea>
                                @error('summary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nội dung chi tiết</label>
                                <textarea
                                    class="form-control @error('content') is-invalid @enderror"
                                    wire:model="content"
                                    rows="6"
                                    placeholder="Nhập toàn văn nội dung chi tiết của quy định tài liệu..."
                                ></textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Tài liệu gốc đính kèm (PDF/Word)</label>
                                <input
                                    type="file"
                                    class="form-control @error('file') is-invalid @enderror"
                                    wire:model="file"
                                />
                                <div class="text-muted small mt-1">Hỗ trợ các tệp tin PDF, Doc, Docx dung lượng dưới 10MB</div>
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" wire:click="resetForm">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu quy định</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function showModal(id) {
            const modalEl = document.getElementById(id);
            if (!modalEl || typeof bootstrap === 'undefined') return;
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }

        function hideModal(id) {
            const modalEl = document.getElementById(id);
            if (!modalEl || typeof bootstrap === 'undefined') return;
            bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        }

        window.addEventListener('document:open-create', () => showModal('modalForm'));
        window.addEventListener('document:open-edit', () => showModal('modalForm'));
        window.addEventListener('document:open-detail', () => showModal('modalDetail'));
        window.addEventListener('document:saved', () => hideModal('modalForm'));
    });
</script>
@endpush
