<div>
    <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between">
        <div class="clearfix">
            <h1 class="app-page-title">Quản lý Phòng ban</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Phòng ban
                    </li>
                </ol>
            </nav>
        </div>
        <button
            type="button"
            class="btn btn-primary waves-effect waves-light"
            wire:click="openCreate"
        >
            <i class="fi fi-rr-plus me-1"></i> Thêm phòng ban mới
        </button>
    </div>

    @if ($successMessage)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fi fi-rr-check me-2"></i> {{ $successMessage }}
            <button type="button" class="btn-close" wire:click="$set('successMessage', null)" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fi fi-rr-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── Summary Cards ─────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                            <i class="fi fi-rr-bank"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Tổng số phòng ban</div>
                            <div class="h4 mb-0 fw-bold">{{ \App\Models\Department::count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                            <i class="fi fi-rr-users"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Nhân sự đã phân phòng</div>
                            <div class="h4 mb-0 fw-bold">
                                {{ \App\Models\User::whereNotNull('department_id')->count() }} / {{ \App\Models\User::count() }} Thành viên
                            </div>
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
                <div class="col-md-6 col-sm-12">
                    <label class="form-label mb-0 text-muted small">Tìm kiếm</label>
                    <input
                        type="text"
                        class="form-control form-control-sm"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Tìm tên, mã phòng ban hoặc mô tả..."
                    />
                </div>
                @if ($search)
                    <div class="col-sm-auto ms-auto align-self-end">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary"
                            wire:click="$set('search', '')"
                        >
                            <i class="fi fi-rr-cross-small me-1"></i> Xóa lọc
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Departments Table ──────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="fi fi-rr-list me-2 text-primary"></i>Danh sách phòng ban hệ thống
            </h5>
        </div>
        <div class="card-body p-0">
            @if ($departments->isEmpty())
                <div class="text-center py-5">
                    <i class="fi fi-rr-bank scale-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">Không tìm thấy phòng ban nào.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-nowrap" style="width: 150px;">Mã phòng ban</th>
                                <th>Tên phòng ban</th>
                                <th>Mô tả chi tiết</th>
                                <th class="text-center" style="width: 150px;">Số thành viên</th>
                                <th class="text-end pe-3" style="width: 140px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departments as $department)
                                <tr>
                                    <td class="ps-3 text-nowrap">
                                        <span class="badge bg-primary-subtle text-primary fw-semibold">{{ $department->code }}</span>
                                    </td>
                                    <td class="fw-semibold text-dark">{{ $department->name }}</td>
                                    <td>
                                        <span class="text-body text-truncate d-inline-block" style="max-width: 400px;" title="{{ $department->description }}">
                                            {{ $department->description ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1">
                                            {{ $department->users_count }} nhân sự
                                        </span>
                                    </td>
                                    <td class="text-end pe-3 text-nowrap">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-warning"
                                            title="Sửa"
                                            wire:click="openEdit({{ $department->id }})"
                                        >
                                            <i class="fi fi-rr-edit"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Xóa"
                                            wire:click="delete({{ $department->id }})"
                                            wire:confirm="Bạn có chắc chắn muốn xóa phòng ban {{ $department->name }} không?"
                                        >
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($departments->hasPages())
                    <div class="p-3 border-top">
                        {{ $departments->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ── Modal Create/Edit Form ────────────────────────────────────────── --}}
    <div
        wire:ignore.self
        class="modal fade"
        id="modalForm"
        tabindex="-1"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <form wire:submit.prevent="save" class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fi fi-rr-edit-alt text-primary me-2"></i>
                        {{ $departmentId ? 'Cập nhật phòng ban' : 'Thêm mới phòng ban' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="resetForm"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Mã phòng ban <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control @error('code') is-invalid @enderror"
                                wire:model="code"
                                placeholder="Ví dụ: IT, HCNS, KD"
                            />
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Tên phòng ban <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                wire:model="name"
                                placeholder="Nhập tên phòng ban..."
                            />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Mô tả ngắn</label>
                            <textarea
                                class="form-control @error('description') is-invalid @enderror"
                                wire:model="description"
                                rows="3"
                                placeholder="Mô tả chức năng/nhiệm vụ..."
                            ></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" wire:click="resetForm">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu phòng ban</button>
                </div>
            </form>
        </div>
    </div>
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

        window.addEventListener('department:open-create', () => showModal('modalForm'));
        window.addEventListener('department:open-edit', () => showModal('modalForm'));
        window.addEventListener('department:saved', () => hideModal('modalForm'));
    });
</script>
@endpush
