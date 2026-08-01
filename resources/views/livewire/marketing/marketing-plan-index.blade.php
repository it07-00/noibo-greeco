<div class="container-fluid p-0">
    {{-- ── Header Section ────────────────────────────────────────────────── --}}
    <section class="d-flex align-items-center justify-content-between flex-wrap gap-3 p-4 mb-3 bg-white border rounded-4 shadow-sm overflow-hidden" aria-labelledby="marketing-page-title">
        <div>
            <nav aria-label="Điều hướng phân cấp">
                <ol class="breadcrumb mb-2 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Kế hoạch Marketing</li>
                </ol>
            </nav>
            <div class="d-flex align-items-start gap-3">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-3 p-3 flex-shrink-0">
                    <i class="fi fi-rr-calendar-lines fs-4"></i>
                </div>
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1" id="marketing-page-title">Kế hoạch Marketing & Nội dung</h1>
                    <p class="text-muted small mb-0">Sắp xếp lịch xuất bản, theo dõi tiến độ và phê duyệt nội dung tại một nơi.</p>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="btn-group bg-light border rounded-3 p-1" role="group" aria-label="Chọn kiểu hiển thị">
                <button type="button"
                    class="btn btn-sm rounded-2 px-3 {{ $viewMode === 'calendar' ? 'btn-primary' : 'btn-light text-muted' }}"
                    wire:click="setViewMode('calendar')"
                    aria-pressed="{{ $viewMode === 'calendar' ? 'true' : 'false' }}">
                    <i class="fi fi-rr-calendar me-1" aria-hidden="true"></i>
                    <span>Lịch</span>
                </button>
                <button type="button"
                    class="btn btn-sm rounded-2 px-3 {{ $viewMode === 'list' ? 'btn-primary' : 'btn-light text-muted' }}"
                    wire:click="setViewMode('list')"
                    aria-pressed="{{ $viewMode === 'list' ? 'true' : 'false' }}">
                    <i class="fi fi-rr-list me-1" aria-hidden="true"></i>
                    <span>Danh sách</span>
                </button>
            </div>
            @can('create', App\Models\MarketingPlan::class)
                <button type="button" class="btn btn-primary px-3 waves-effect waves-light"
                    wire:click="openCreate()" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="openCreate">
                        <i class="fi fi-rr-plus me-2" aria-hidden="true"></i>Tạo kế hoạch
                    </span>
                    <span wire:loading wire:target="openCreate">
                        <span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Đang mở...
                    </span>
                </button>
            @endcan
        </div>
    </section>

    {{-- ── Filter Bar ───────────────────────────────────────────────────────── --}}
    <section class="bg-white border rounded-4 shadow-sm p-3 p-lg-4 mb-3" aria-label="Bộ lọc kế hoạch">
        <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
            <div>
                <span class="d-block text-primary text-uppercase fw-bold small mb-1">Bộ lọc</span>
                <h2 class="h6 fw-bold text-dark mb-0">Tìm nhanh kế hoạch</h2>
            </div>
            <span class="badge bg-light text-muted border rounded-pill px-3 py-2">
                <i class="fi fi-rr-document me-1" aria-hidden="true"></i>
                {{ number_format($listPlans->total()) }} kế hoạch
            </span>
        </div>
        <div class="row g-3 align-items-end">
            <div class="col-xl-4 col-md-6">
                <label for="marketing-search" class="form-label small fw-semibold">Tìm kiếm</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fi fi-rr-search" aria-hidden="true"></i></span>
                    <input id="marketing-search" type="search" class="form-control border-start-0"
                        placeholder="Tìm tiêu đề hoặc nội dung..."
                        wire:model.live.debounce.300ms="search">
                </div>
            </div>
            <div class="col-xl col-md-6">
                <label for="marketing-category-filter" class="form-label small fw-semibold">Danh mục</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fi fi-rr-apps" aria-hidden="true"></i></span>
                    <select id="marketing-category-filter" class="form-select border-start-0" wire:model.live="filterCategory">
                        <option value="all">Tất cả danh mục</option>
                        @foreach ($categoriesEnum as $cat)
                            <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xl col-md-6">
                <label for="marketing-status-filter" class="form-label small fw-semibold">Trạng thái</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fi fi-rr-settings-sliders" aria-hidden="true"></i></span>
                    <select id="marketing-status-filter" class="form-select border-start-0" wire:model.live="filterStatus">
                        <option value="all">Tất cả trạng thái</option>
                        @foreach ($statusesEnum as $st)
                            <option value="{{ $st->value }}">{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xl col-md-6">
                <label for="marketing-creator-filter" class="form-label small fw-semibold">Người soạn</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fi fi-rr-user" aria-hidden="true"></i></span>
                    <select id="marketing-creator-filter" class="form-select border-start-0" wire:model.live="filterCreatorId">
                        <option value="0">Tất cả thành viên</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </section>

    @if ($viewMode === 'calendar')
        {{-- ── Calendar View ───────────────────────────────────────────────────── --}}
        <section class="bg-white border rounded-4 shadow-sm mb-3 overflow-hidden" aria-label="Lịch kế hoạch Marketing" wire:key="marketing-calendar-view">
            <header class="d-flex align-items-center justify-content-between flex-wrap gap-3 p-3 border-bottom bg-white">
                <div>
                    <span class="d-block text-primary text-uppercase fw-bold small mb-1">Lịch nội dung</span>
                    <h2 class="h6 fw-bold text-dark mb-0">Kế hoạch theo tháng</h2>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3 small text-muted" aria-label="Chú thích trạng thái">
                    @foreach ($statusesEnum as $st)
                        <span class="d-inline-flex align-items-center gap-1 small fw-semibold me-2">
                            <span class="badge rounded-pill {{ $st->badgeClass() }} me-1">{{ $st->label() }}</span>
                        </span>
                    @endforeach
                </div>
            </header>
            <div class="p-4 calendar-scroll-wrapper overflow-auto">
                <div id="calendar" wire:ignore></div>
            </div>
        </section>
    @else
        {{-- ── List / Table View ────────────────────────────────────────────────── --}}
        <section class="bg-white border rounded-4 shadow-sm mb-3 overflow-hidden" aria-label="Danh sách kế hoạch Marketing" wire:key="marketing-list-view">
            <header class="d-flex align-items-center justify-content-between flex-wrap gap-3 p-3 border-bottom bg-white">
                <div>
                    <span class="d-block text-primary text-uppercase fw-bold small mb-1">Danh sách nội dung</span>
                    <h2 class="h6 fw-bold text-dark mb-0">Tất cả kế hoạch</h2>
                </div>
                <span class="small text-muted">
                    <i class="fi fi-rr-info me-1" aria-hidden="true"></i> Chọn tiêu đề để xem chi tiết
                </span>
            </header>
            <div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase text-secondary small fw-bold">
                            <tr>
                                <th style="width: 70px;">Nội dung</th>
                                <th>Tiêu đề & mô tả</th>
                                <th>Danh mục</th>
                                <th>Thời gian xuất bản</th>
                                <th>Người soạn</th>
                                <th>Trạng thái</th>
                                <th class="text-end" style="width: 130px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($listPlans as $plan)
                                <tr wire:key="marketing-plan-row-{{ $plan->id }}">
                                    <td>
                                        @if ($plan->images->count() > 0)
                                            <img src="{{ Storage::url($plan->images->first()->file_path) }}"
                                                class="rounded-3 border object-fit-cover"
                                                alt="{{ $plan->title }}"
                                                width="48" height="48" loading="lazy">
                                        @else
                                            <div class="d-inline-flex align-items-center justify-content-center bg-light border rounded-3 text-secondary p-2">
                                                <i class="fi fi-rr-document fs-5"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-link p-0 text-start text-dark fw-bold text-decoration-none d-block mb-1" wire:click="openDetail({{ $plan->id }})">
                                            {{ $plan->title }}
                                        </button>
                                        <p class="text-muted small mb-0 text-truncate" style="max-width: 360px;">{{ Str::limit(strip_tags($plan->content), 90) ?: 'Chưa có nội dung chi tiết.' }}</p>
                                    </td>
                                    <td>
                                        @php $catEnum = $plan->category; @endphp
                                        @if ($catEnum)
                                            <span class="badge rounded-pill {{ $catEnum->badgeClass() }}">
                                                {{ $catEnum->label() }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-inline-flex align-items-center justify-content-center bg-light border rounded-2 p-2 text-secondary">
                                                <i class="fi fi-rr-calendar"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block small text-dark">{{ $plan->scheduled_at->format('H:i · d/m/Y') }}</strong>
                                                <small class="text-muted">{{ $plan->scheduled_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-inline-flex align-items-center justify-content-center bg-light border rounded-circle text-secondary p-1">
                                                <i class="fi fi-rr-user small"></i>
                                            </div>
                                            <span class="small fw-semibold text-dark">{{ $plan->creator?->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill {{ $plan->status->badgeClass() }}">
                                            {{ $plan->status->label() }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-light border text-secondary" aria-label="Xem chi tiết {{ $plan->title }}" title="Xem chi tiết" wire:click="openDetail({{ $plan->id }})">
                                                <i class="fi fi-rr-eye"></i>
                                            </button>
                                            @can('update', $plan)
                                                <button type="button" class="btn btn-sm btn-light border text-secondary" aria-label="Chỉnh sửa {{ $plan->title }}" title="Chỉnh sửa" wire:click="openEdit({{ $plan->id }})">
                                                    <i class="fi fi-rr-edit"></i>
                                                </button>
                                            @endcan
                                            @can('delete', $plan)
                                                <button type="button" class="btn btn-sm btn-light border text-danger" aria-label="Xóa {{ $plan->title }}" title="Xóa" wire:click="deletePlan({{ $plan->id }})" wire:confirm="Bạn có chắc chắn muốn xóa bài viết này?">
                                                    <i class="fi fi-rr-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="py-3">
                                            <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-4 p-3 mb-3">
                                                <i class="fi fi-rr-search-alt fs-2"></i>
                                            </div>
                                            <h3 class="h6 fw-bold text-dark">Chưa tìm thấy kế hoạch phù hợp</h3>
                                            <p class="text-muted small mb-3">Thử thay đổi từ khóa hoặc bộ lọc để xem thêm nội dung.</p>
                                            @can('create', App\Models\MarketingPlan::class)
                                                <button type="button" class="btn btn-outline-primary" wire:click="openCreate()">
                                                    <i class="fi fi-rr-plus me-2" aria-hidden="true"></i>Tạo kế hoạch mới
                                                </button>
                                            @endcan
                                        </div>
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
        </section>
    @endif

    {{-- ── Modal Create / Edit Plan ─────────────────────────────────────────── --}}
    <div class="modal fade" id="modalPlanForm" tabindex="-1" aria-labelledby="modalPlanFormLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content border-0 rounded-4 overflow-hidden shadow">
                <div class="modal-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                            <i class="fi {{ $planId ? 'fi-rr-edit' : 'fi-rr-add-document' }} fs-4"></i>
                        </div>
                        <div>
                            <span class="d-block text-primary text-uppercase fw-bold small mb-1">{{ $planId ? 'Chỉnh sửa nội dung' : 'Nội dung mới' }}</span>
                            <h2 class="modal-title h5 fw-bold text-dark mb-1" id="modalPlanFormLabel">
                                {{ $planId ? 'Cập nhật kế hoạch bài viết' : 'Tạo kế hoạch bài viết' }}
                            </h2>
                            <p class="text-muted small mb-0 d-none d-sm-block">Điền thông tin, lịch xuất bản và nội dung cần phê duyệt.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng" wire:click="resetForm"></button>
                </div>
                <form wire:submit.prevent="save" class="d-flex flex-column flex-grow-1 overflow-hidden">
                    <div class="modal-body bg-light p-4">
                        <div class="bg-white border rounded-3 p-4 mb-3 shadow-sm">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <span class="badge bg-primary rounded-pill px-3 py-2 fs-6 fw-bold d-inline-flex align-items-center justify-content-center shadow-sm" style="min-width: 50px; height: 34px;">01</span>
                                <div>
                                    <h3 class="h5 fw-bold text-dark mb-1">Thông tin xuất bản</h3>
                                    <p class="text-secondary mb-0">Thông tin chính để hiển thị trên lịch và danh sách.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="marketing-plan-title" class="form-label small fw-semibold">Tiêu đề bài viết / Kế hoạch truyền thông <span class="text-danger">*</span></label>
                                    <input id="marketing-plan-title" type="text" wire:model="title" class="form-control @error('title') is-invalid @enderror" placeholder="Nhập tiêu đề bài viết truyền thông..." />
                                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label for="marketing-plan-scheduled-at" class="form-label small fw-semibold">Thời gian dự kiến xuất bản <span class="text-danger">*</span></label>
                                    <input id="marketing-plan-scheduled-at" type="datetime-local" wire:model="scheduled_at" class="form-control @error('scheduled_at') is-invalid @enderror" />
                                    @error('scheduled_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label for="marketing-plan-status" class="form-label small fw-semibold">Trạng thái gửi duyệt</label>
                                    <select id="marketing-plan-status" wire:model="status" class="form-select @error('status') is-invalid @enderror">
                                        <option value="draft">Bản nháp (Lưu tạm)</option>
                                        <option value="pending_approval">Gửi duyệt (Ban quản lý)</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border rounded-3 p-4 mb-3 shadow-sm">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <span class="badge bg-primary rounded-pill px-3 py-2 fs-6 fw-bold d-inline-flex align-items-center justify-content-center shadow-sm" style="min-width: 50px; height: 34px;">02</span>
                                <div>
                                    <h3 class="h5 fw-bold text-dark mb-1">Nội dung bài viết</h3>
                                    <p class="text-secondary mb-0">Soạn nội dung cần xuất bản hoặc gửi người quản lý phê duyệt.</p>
                                </div>
                            </div>
                            <div wire:ignore>
                                <label class="visually-hidden" for="quillEditor">Nội dung bài viết</label>
                                <div id="quillEditor" class="bg-white rounded-3 border" style="min-height: 200px;"></div>
                                <input type="hidden" id="contentHiddenInput" wire:model="content">
                            </div>
                            @error('content') <div class="text-danger small mt-2" role="alert">{{ $message }}</div> @enderror
                        </div>

                        <div class="bg-white border rounded-3 p-4 shadow-sm">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <span class="badge bg-primary rounded-pill px-3 py-2 fs-6 fw-bold d-inline-flex align-items-center justify-content-center shadow-sm" style="min-width: 50px; height: 34px;">03</span>
                                <div>
                                    <h3 class="h5 fw-bold text-dark mb-1">Tài liệu & ghi chú</h3>
                                    <p class="text-secondary mb-0">Đính kèm hình ảnh minh họa và ghi chú cho đội ngũ.</p>
                                </div>
                            </div>

                            <div class="bg-light border rounded-3 p-3">
                                <label for="marketing-plan-images" class="d-flex align-items-center gap-3 mb-2 cursor-pointer">
                                    <div class="d-inline-flex align-items-center justify-content-center bg-white border rounded-3 p-3 text-primary">
                                        <i class="fi fi-rr-cloud-upload-alt fs-4"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block small text-dark">Chọn hình ảnh đính kèm</strong>
                                        <small class="d-block text-muted">JPG, PNG hoặc WEBP · tối đa 10MB mỗi ảnh</small>
                                    </div>
                                </label>
                                <input id="marketing-plan-images" type="file" wire:model="newImages" multiple accept="image/*" class="form-control @error('newImages.*') is-invalid @enderror">
                                @error('newImages.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                                <div wire:loading wire:target="newImages" class="text-primary small mt-2" role="status">
                                    <span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Đang tải ảnh lên...
                                </div>

                                @if (count($existingImages) > 0)
                                    <div class="mt-3">
                                        <span class="d-block text-muted small fw-semibold mb-2">Hình ảnh hiện tại</span>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($existingImages as $img)
                                                <div class="position-relative border rounded-3 p-1 bg-white" style="width: 80px; height: 80px;">
                                                    <img src="{{ $img['url'] }}" alt="{{ $img['name'] }}" class="w-100 h-100 rounded-2 object-fit-cover">
                                                    <button type="button" class="btn btn-danger btn-sm p-0 position-absolute top-0 end-0 translate-middle-y rounded-circle d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;" aria-label="Xóa ảnh {{ $img['name'] }}" title="Xóa ảnh này" wire:click="markForImageDeletion({{ $img['id'] }})">
                                                        <i class="fi fi-rr-cross extra-small" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if (count($newImages) > 0)
                                    <div class="mt-3">
                                        <span class="d-block text-muted small fw-semibold mb-2">Hình ảnh vừa chọn</span>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($newImages as $index => $imgFile)
                                                @if ($imgFile && method_exists($imgFile, 'temporaryUrl'))
                                                    <div class="position-relative border rounded-3 p-1 bg-white" style="width: 80px; height: 80px;">
                                                        <img src="{{ $imgFile->temporaryUrl() }}" alt="Ảnh vừa chọn {{ $index + 1 }}" class="w-100 h-100 rounded-2 object-fit-cover">
                                                        <button type="button" class="btn btn-danger btn-sm p-0 position-absolute top-0 end-0 translate-middle-y rounded-circle d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;" aria-label="Hủy ảnh vừa chọn {{ $index + 1 }}" title="Hủy ảnh này" wire:click="removeNewImage({{ $index }})">
                                                            <i class="fi fi-rr-cross extra-small" aria-hidden="true"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-3">
                                <label for="marketing-plan-notes" class="form-label small fw-semibold">Ghi chú nội bộ</label>
                                <textarea id="marketing-plan-notes" wire:model="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Ghi chú thêm cho người duyệt hoặc đội nhóm..."></textarea>
                                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between">
                        <p class="small text-muted mb-0 d-none d-sm-block"><span class="text-danger">*</span> Thông tin bắt buộc</p>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal" wire:click="resetForm">Hủy</button>
                            <button type="submit" class="btn btn-primary waves-effect waves-light" wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading.remove wire:target="save">
                                    <i class="fi fi-rr-disk me-2" aria-hidden="true"></i>Lưu kế hoạch
                                </span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Đang lưu...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Modal View Detail & Approval ─────────────────────────────────────── --}}
    <div class="modal fade" id="modalPlanDetail" tabindex="-1" aria-labelledby="modalPlanDetailLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content border-0 rounded-4 overflow-hidden shadow">
                @if ($selectedPlan)
                    <div class="modal-header p-4 border-bottom bg-white">
                        <div class="w-100">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <span class="badge rounded-pill {{ $selectedPlan->status->badgeClass() }}">
                                        {{ $selectedPlan->status->label() }}
                                    </span>
                                    @php $catEnum = $selectedPlan->category; @endphp
                                    @if ($catEnum)
                                        <span class="badge rounded-pill {{ $catEnum->badgeClass() }}">
                                            {{ $catEnum->label() }}
                                        </span>
                                    @endif
                                </div>
                                <span class="text-muted small">
                                    <i class="fi fi-rr-time-fast me-1 text-primary"></i>
                                    Tạo {{ $selectedPlan->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <h2 class="modal-title h4 fw-bold text-dark mb-0" id="modalPlanDetailLabel">{{ $selectedPlan->title }}</h2>
                        </div>
                        <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>

                    <div class="modal-body bg-light p-4">
                        {{-- Rejection reason alert --}}
                        @if ($selectedPlan->status === App\Enums\MarketingPlanStatus::Rejected && $selectedPlan->rejection_reason)
                            <div class="alert alert-danger d-flex align-items-start gap-3 p-3 rounded-3 mb-4 shadow-sm" role="alert">
                                <i class="fi fi-rr-exclamation fs-5 flex-shrink-0"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Lý do từ chối phê duyệt</h6>
                                    <p class="mb-0 small text-danger-emphasis">{{ $selectedPlan->rejection_reason }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Metadata Cards Grid --}}
                        <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-lg-4 mb-4">
                            <div class="col">
                              <div class="p-3 bg-white border rounded-3 shadow-sm d-flex align-items-center gap-3 h-100">
                                <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-3 p-2 flex-shrink-0">
                                    <i class="fi fi-rr-user fs-5"></i>
                                </div>
                                <div class="min-w-0 flex-grow-1">
                                    <span class="d-block text-uppercase text-secondary fw-bold small">Người soạn bài</span>
                                    <strong class="d-block text-dark text-truncate small fw-bold">{{ $selectedPlan->creator?->name ?? 'N/A' }}</strong>
                                </div>
                              </div>
                            </div>
                            <div class="col">
                              <div class="p-3 bg-white border rounded-3 shadow-sm d-flex align-items-center gap-3 h-100">
                                <div class="d-inline-flex align-items-center justify-content-center bg-info-subtle text-info rounded-3 p-2 flex-shrink-0">
                                    <i class="fi fi-rr-clock fs-5"></i>
                                </div>
                                <div class="min-w-0 flex-grow-1">
                                    <span class="d-block text-uppercase text-secondary fw-bold small">Ngày tạo kế hoạch</span>
                                    <strong class="d-block text-dark small fw-bold">{{ $selectedPlan->created_at->format('H:i · d/m/Y') }}</strong>
                                </div>
                              </div>
                            </div>
                            <div class="col">
                              <div class="p-3 bg-white border rounded-3 shadow-sm d-flex align-items-center gap-3 h-100">
                                <div class="d-inline-flex align-items-center justify-content-center bg-secondary-subtle text-secondary rounded-3 p-2 flex-shrink-0">
                                    <i class="fi fi-rr-calendar-clock fs-5"></i>
                                </div>
                                <div class="min-w-0 flex-grow-1">
                                    <span class="d-block text-uppercase text-secondary fw-bold small">Thời gian xuất bản</span>
                                    <strong class="d-block text-dark small fw-bold">{{ $selectedPlan->scheduled_at->format('H:i · d/m/Y') }}</strong>
                                    <span class="d-block text-muted small">{{ $selectedPlan->scheduled_at->diffForHumans() }}</span>
                                </div>
                              </div>
                            </div>
                            <div class="col">
                              <div class="p-3 bg-white border rounded-3 shadow-sm d-flex align-items-center gap-3 h-100">
                                <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-3 p-2 flex-shrink-0">
                                    <i class="fi {{ $selectedPlan->approved_by ? 'fi-rr-user-check' : 'fi-rr-shield' }} fs-5"></i>
                                </div>
                                <div class="min-w-0 flex-grow-1">
                                    <span class="d-block text-uppercase text-secondary fw-bold small">Người xử lý duyệt</span>
                                    <strong class="d-block text-dark text-truncate small fw-bold">{{ $selectedPlan->approver?->name ?? 'Chưa duyệt' }}</strong>
                                    @if ($selectedPlan->approved_at)
                                        <span class="d-block text-muted small">{{ $selectedPlan->approved_at->format('H:i · d/m/Y') }}</span>
                                    @endif
                                </div>
                              </div>
                            </div>
                        </div>

                        {{-- Rich Text Content Display --}}
                        <section class="bg-white border rounded-3 p-4 mb-4 shadow-sm">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-2 p-2">
                                    <i class="fi fi-rr-document-signed"></i>
                                </div>
                                <div>
                                    <span class="d-block text-primary text-uppercase fw-bold small">Nội dung</span>
                                    <h3 class="h6 fw-bold text-dark mb-0">Nội dung truyền thông</h3>
                                </div>
                            </div>
                            <div class="bg-light border rounded-3 p-3">
                                {!! $selectedPlan->content ?: '<em class="text-muted">Chưa nhập nội dung chi tiết.</em>' !!}
                            </div>
                        </section>

                        {{-- Images Gallery & Management --}}
                        <section class="bg-white border rounded-3 p-4 mb-4 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-2 p-2">
                                        <i class="fi fi-rr-picture"></i>
                                    </div>
                                    <div>
                                        <span class="d-block text-primary text-uppercase fw-bold small">Tài liệu & Hình ảnh</span>
                                        <h3 class="h6 fw-bold text-dark mb-0">Hình ảnh bài viết <small class="text-muted">({{ $selectedPlan->images->count() }} ảnh)</small></h3>
                                    </div>
                                </div>
                                <div>
                                    <label for="detail-upload-input" class="btn btn-sm btn-primary waves-effect waves-light cursor-pointer mb-0">
                                        <i class="fi fi-rr-cloud-upload-alt me-1"></i> Tải thêm ảnh
                                    </label>
                                    <input id="detail-upload-input" type="file" wire:model.live="uploadDetailImages" multiple accept="image/*" class="d-none">
                                </div>
                            </div>
                            @error('uploadDetailImages.*')
                                <div class="alert alert-danger small p-2 mb-3">{{ $message }}</div>
                            @enderror
                            <div wire:loading wire:target="uploadDetailImages" class="text-primary small mb-3">
                                <span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Đang tải ảnh lên bài viết...
                            </div>

                            @if ($selectedPlan->images->count() > 0)
                                <div class="row g-3 row-cols-2 row-cols-md-3 row-cols-lg-4">
                                    @foreach ($selectedPlan->images as $img)
                                        <div class="col" wire:key="detail-img-{{ $img->id }}">
                                            <div class="position-relative border rounded-3 overflow-hidden bg-light shadow-sm">
                                                <a href="{{ Storage::url($img->file_path) }}" target="_blank" rel="noopener" class="d-block text-decoration-none">
                                                    <img src="{{ Storage::url($img->file_path) }}" alt="{{ $img->file_name }}" class="w-100 object-fit-cover" style="aspect-ratio: 4/3;" loading="lazy">
                                                    <span class="position-absolute bottom-0 start-0 m-2 px-2 py-1 bg-dark bg-opacity-75 text-white rounded small">
                                                        <i class="fi fi-rr-expand me-1"></i> Xem
                                                    </span>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm p-0 position-absolute top-0 end-0 m-2 rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 28px; height: 28px;" title="Xóa ảnh này khỏi bài viết" aria-label="Xóa ảnh này" wire:click="deleteSingleImage({{ $img->id }})" wire:confirm="Bạn có chắc chắn muốn xóa ảnh này khỏi bài viết?">
                                                    <i class="fi fi-rr-trash small" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 bg-light rounded-3 border border-dashed">
                                    <i class="fi fi-rr-picture text-muted fs-3 mb-2 d-block"></i>
                                    <p class="text-muted small mb-0">Bài viết này chưa có hình ảnh đính kèm. Bấm nút <strong>"Tải thêm ảnh"</strong> để chọn hình ảnh.</p>
                                </div>
                            @endif
                        </section>

                        @if ($selectedPlan->notes)
                            <section class="bg-warning-subtle border border-warning-subtle rounded-3 p-4 shadow-sm">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="d-inline-flex align-items-center justify-content-center bg-warning text-dark rounded-2 p-2">
                                        <i class="fi fi-rr-notebook"></i>
                                    </div>
                                    <div>
                                        <span class="d-block text-warning-emphasis text-uppercase fw-bold small">Ghi chú nội bộ</span>
                                        <h3 class="h6 fw-bold text-dark mb-0">Ghi chú cho đội ngũ</h3>
                                    </div>
                                </div>
                                <p class="mb-0 text-dark small">{{ $selectedPlan->notes }}</p>
                            </section>
                        @endif
                    </div>

                    <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between">
                        <div>
                            @can('delete', $selectedPlan)
                                <button type="button" class="btn btn-outline-danger" wire:click="deletePlan({{ $selectedPlan->id }})" wire:confirm="Xóa kế hoạch này?">
                                    <i class="fi fi-rr-trash me-2" aria-hidden="true"></i>Xóa
                                </button>
                            @endcan
                        </div>
                        <div class="d-flex flex-wrap justify-content-end gap-2">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>

                            @can('update', $selectedPlan)
                                <button type="button" class="btn btn-outline-primary waves-effect waves-light" wire:click="openEdit({{ $selectedPlan->id }})">
                                    <i class="fi fi-rr-edit me-2" aria-hidden="true"></i>Chỉnh sửa
                                </button>
                            @endcan

                            @if (($selectedPlan->status === App\Enums\MarketingPlanStatus::Draft || $selectedPlan->status === App\Enums\MarketingPlanStatus::Rejected) && (int)$selectedPlan->created_by === (int)auth()->id())
                                <button type="button" class="btn btn-warning waves-effect waves-light" wire:click="submitForReview({{ $selectedPlan->id }})">
                                    <i class="fi fi-rr-paper-plane me-2" aria-hidden="true"></i>Gửi phê duyệt
                                </button>
                            @endif

                            @can('approve', App\Models\MarketingPlan::class)
                                @if ($selectedPlan->status !== App\Enums\MarketingPlanStatus::Approved)
                                    <button type="button" class="btn btn-success waves-effect waves-light" wire:click="approvePlan({{ $selectedPlan->id }})">
                                        <i class="fi fi-rr-check-circle me-2" aria-hidden="true"></i>Phê duyệt
                                    </button>
                                @endif
                                @if ($selectedPlan->status !== App\Enums\MarketingPlanStatus::Rejected)
                                    <button type="button" class="btn btn-outline-danger waves-effect waves-light" wire:click="openRejectModal({{ $selectedPlan->id }})">
                                        <i class="fi fi-rr-cross-circle me-2" aria-hidden="true"></i>Từ chối
                                    </button>
                                @endif
                            @endcan
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Modal Reject Reason ─────────────────────────────────────────────── --}}
    <div class="modal fade" id="modalRejectReason" tabindex="-1" aria-labelledby="modalRejectReasonLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 overflow-hidden shadow">
                <div class="modal-header bg-white border-bottom p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                            <i class="fi fi-rr-cross-circle fs-4"></i>
                        </div>
                        <div>
                            <span class="d-block text-danger text-uppercase fw-bold small mb-1">Yêu cầu chỉnh sửa</span>
                            <h2 class="modal-title h5 fw-bold text-dark mb-0" id="modalRejectReasonLabel">Từ chối phê duyệt</h2>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body bg-light p-3">
                    <p class="small text-muted bg-white border rounded-3 p-3">Mô tả rõ nội dung cần chỉnh sửa để người soạn có thể xử lý nhanh chóng.</p>
                    <div>
                        <label for="marketing-rejection-reason" class="form-label small fw-semibold">Lý do từ chối & yêu cầu chỉnh sửa <span class="text-danger">*</span></label>
                        <textarea id="marketing-rejection-reason" wire:model="rejection_reason" class="form-control @error('rejection_reason') is-invalid @enderror" rows="5" placeholder="Ví dụ: Cần điều chỉnh hình ảnh đúng chuẩn logo và rút gọn phần mở đầu..."></textarea>
                        @error('rejection_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer bg-white border-top p-3 justify-content-end">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger waves-effect waves-light" wire:click="confirmReject" wire:loading.attr="disabled" wire:target="confirmReject">
                        <span wire:loading.remove wire:target="confirmReject"><i class="fi fi-rr-cross-circle me-2" aria-hidden="true"></i>Xác nhận từ chối</span>
                        <span wire:loading wire:target="confirmReject"><span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Đang xử lý...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/duty-schedule.css') }}?v=1.2.0">
@endpush

@push('scripts')
    <script src="{{ asset('js/fullcalendar.global.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        let mCalendarInstance = null;
        let mCalendarWireId = '{{ $this->getId() }}';
        let mCalendarElement = null;
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
                        ['link', 'image', 'clean']
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

        function initMarketingCalendar() {
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) return;

            if (mCalendarInstance) {
                mCalendarInstance.destroy();
                mCalendarInstance = null;
            }

            mCalendarElement = calendarEl;

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
                buttonHints: {
                    prev: '$one trước',
                    next: '$one sau'
                },
                selectable: true,
                editable: false,
                events: function(fetchInfo, successCallback, failureCallback) {
                    if (window.Livewire && mCalendarWireId) {
                        const component = window.Livewire.find(mCalendarWireId);
                        if (component) {
                            component.$call('getCalendarEvents', fetchInfo.startStr, fetchInfo.endStr)
                                .then(events => successCallback(events))
                                .catch(err => {
                                    console.warn('FullCalendar fetch error:', err);
                                    failureCallback(err);
                                });
                        } else {
                            successCallback([]);
                        }
                    } else {
                        successCallback([]);
                    }
                },
                dateClick: function(info) {
                    if (window.Livewire && mCalendarWireId) {
                        const component = window.Livewire.find(mCalendarWireId);
                        if (component) {
                            component.$call('openCreate', info.dateStr);
                        }
                    }
                },
                eventClick: function(info) {
                    if (window.Livewire && mCalendarWireId && info.event.id) {
                        const component = window.Livewire.find(mCalendarWireId);
                        if (component) {
                            component.$call('openDetail', info.event.id);
                        }
                    }
                },
                eventDidMount: function(info) {
                    const title = info.event.extendedProps.raw_title || info.event.title;
                    info.el.setAttribute('title', title);
                    info.el.setAttribute('aria-label', `Xem kế hoạch: ${title}`);
                },
                dayCellContent: function(arg) {
                    const numberEl = document.createElement('span');
                    const cleanNum = arg.dayNumberText
                        .replace('thg', '')
                        .replace('tháng', '')
                        .replace(/[a-zA-Z]/g, '')
                        .trim();

                    numberEl.className = arg.isToday ? 'day-cell-today-number' : 'day-cell-number';
                    numberEl.textContent = cleanNum;

                    const actionsEl = document.createElement('div');
                    actionsEl.className = 'day-cell-actions';

                    @can('create', App\Models\MarketingPlan::class)
                        const plusBtn = document.createElement('button');
                        plusBtn.type = 'button';
                        plusBtn.className = 'btn-plus-day';
                        plusBtn.title = 'Tạo kế hoạch';
                        plusBtn.setAttribute('aria-label', `Tạo kế hoạch ngày ${cleanNum}`);
                        plusBtn.innerHTML = '<i class="fi fi-rr-plus" style="font-size: 8px; line-height: 1;" aria-hidden="true"></i>';
                        plusBtn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const year = arg.date.getFullYear();
                            const month = String(arg.date.getMonth() + 1).padStart(2, '0');
                            const day = String(arg.date.getDate()).padStart(2, '0');
                            const dateStr = `${year}-${month}-${day}`;

                            if (window.Livewire && mCalendarWireId) {
                                window.Livewire.find(mCalendarWireId).$call('openCreate', dateStr);
                            }
                        });
                        actionsEl.appendChild(plusBtn);
                    @endcan

                    return { domNodes: [numberEl, actionsEl] };
                },
                eventContent: function(arg) {
                    const st = arg.event.extendedProps.status || 'draft';
                    const rawTitle = arg.event.extendedProps.raw_title || arg.event.title;
                    const statusLabel = arg.event.extendedProps.status_label || '';
                    const creatorName = arg.event.extendedProps.creator_name || 'Kế hoạch Marketing';
                    const themeClasses = {
                        draft: 'warning',
                        pending_approval: 'info',
                        approved: 'success',
                        rejected: 'danger'
                    };

                    const eventEl = document.createElement('div');
                    const contextEl = document.createElement('span');
                    const ownerEl = document.createElement('span');
                    const timeEl = document.createElement('span');
                    const titleEl = document.createElement('span');
                    const metaEl = document.createElement('span');

                    eventEl.className = `greeco-event-card event-theme-${themeClasses[st] || 'primary'}`;
                    eventEl.title = [rawTitle, creatorName, statusLabel].filter(Boolean).join(' · ');

                    contextEl.className = 'greeco-event-context';

                    ownerEl.className = 'greeco-event-owner';
                    ownerEl.textContent = creatorName;
                    contextEl.appendChild(ownerEl);

                    if (arg.event.start) {
                        timeEl.className = 'greeco-event-time';
                        timeEl.textContent = arg.event.start.toLocaleTimeString('vi-VN', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: false
                        });
                        contextEl.appendChild(timeEl);
                    }

                    titleEl.className = 'greeco-event-title';
                    titleEl.textContent = rawTitle;

                    metaEl.className = 'greeco-event-meta';
                    metaEl.textContent = statusLabel;

                    eventEl.append(contextEl, titleEl, metaEl);

                    return { domNodes: [eventEl] };
                }
            });

            mCalendarInstance.render();
        }

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('marketing:open-create', (data) => {
                const modalEl = document.getElementById('modalPlanForm');
                if (modalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    setTimeout(() => {
                        initQuillEditor();
                        setQuillContent('');
                    }, 150);
                }
            });

            Livewire.on('marketing:open-edit', (data) => {
                const modalEl = document.getElementById('modalPlanForm');
                if (modalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    setTimeout(() => {
                        initQuillEditor();
                        if (data && typeof data.content !== 'undefined') {
                            setQuillContent(data.content);
                        } else if (data && typeof data[0] !== 'undefined' && data[0].content) {
                            setQuillContent(data[0].content);
                        }
                    }, 150);
                }
            });

            Livewire.on('marketing:close-modal-form', () => {
                const modalEl = document.getElementById('modalPlanForm');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
            });

            Livewire.on('marketing:open-detail', () => {
                const modalEl = document.getElementById('modalPlanDetail');
                if (modalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            });

            Livewire.on('marketing:close-detail', () => {
                const modalEl = document.getElementById('modalPlanDetail');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
            });

            Livewire.on('marketing:open-reject-modal', () => {
                const modalEl = document.getElementById('modalRejectReason');
                if (modalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            });

            Livewire.on('marketing:close-reject-modal', () => {
                const modalEl = document.getElementById('modalRejectReason');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
            });

            Livewire.on('marketing:filter-changed', () => {
                if (mCalendarInstance) {
                    mCalendarInstance.refetchEvents();
                }
            });

            if (document.getElementById('calendar')) {
                initMarketingCalendar();
            }
        });

        document.addEventListener('livewire:navigated', () => {
            if (document.getElementById('calendar')) {
                setTimeout(initMarketingCalendar, 100);
            }
        });
    </script>
@endpush
