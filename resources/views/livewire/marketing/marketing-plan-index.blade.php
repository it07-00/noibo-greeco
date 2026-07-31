<div class="marketing-workspace">
    <section class="marketing-page-head d-flex align-items-center justify-content-between flex-wrap gap-4 p-4 mb-3 bg-white border rounded-4 shadow-sm overflow-hidden" aria-labelledby="marketing-page-title">
        <div class="marketing-page-copy">
            <nav aria-label="Điều hướng phân cấp">
                <ol class="breadcrumb marketing-breadcrumb mb-2">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Kế hoạch Marketing</li>
                </ol>
            </nav>
            <div class="d-flex align-items-start gap-3">
                <span class="marketing-page-icon" aria-hidden="true">
                    <i class="fi fi-rr-calendar-lines"></i>
                </span>
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1" id="marketing-page-title">Kế hoạch Marketing & Nội dung</h1>
                    <p class="text-muted small mb-0">Sắp xếp lịch xuất bản, theo dõi tiến độ và phê duyệt nội dung tại một nơi.</p>
                </div>
            </div>
        </div>

        <div class="marketing-page-actions d-flex align-items-center flex-wrap gap-2">
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
                    wire:click="openCreate('{{ date('Y-m-d') }}')" wire:loading.attr="disabled">
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
                <span class="d-block text-primary text-uppercase fw-bold text-xs mb-1">Bộ lọc</span>
                <h2 class="h6 fw-bold text-dark mb-0">Tìm nhanh kế hoạch</h2>
            </div>
            <span class="badge bg-light text-muted border rounded-pill px-3 py-2">
                <i class="fi fi-rr-document" aria-hidden="true"></i>
                {{ number_format($listPlans->total()) }} kế hoạch
            </span>
        </div>
        <div class="row g-3 align-items-end">
            <div class="col-xl-4 col-md-6">
                <label for="marketing-search" class="form-label small fw-semibold">Tìm kiếm</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fi fi-rr-search" aria-hidden="true"></i></span>
                    <input id="marketing-search" type="search" class="form-control"
                        placeholder="Tìm tiêu đề hoặc nội dung..."
                        wire:model.live.debounce.300ms="search">
                </div>
            </div>
            <div class="col-xl col-md-6">
                <label for="marketing-category-filter" class="form-label small fw-semibold">Danh mục</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fi fi-rr-apps" aria-hidden="true"></i></span>
                    <select id="marketing-category-filter" class="form-select" wire:model.live="filterCategory">
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
                    <select id="marketing-status-filter" class="form-select" wire:model.live="filterStatus">
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
                    <select id="marketing-creator-filter" class="form-select" wire:model.live="filterCreatorId">
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
                    <span class="d-block text-primary text-uppercase fw-bold text-xs mb-1">Lịch nội dung</span>
                    <h2 class="h6 fw-bold text-dark mb-0">Kế hoạch theo tháng</h2>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3 small text-muted" aria-label="Chú thích trạng thái">
                    @foreach ($statusesEnum as $st)
                        <span class="marketing-legend-item marketing-status-{{ str_replace('_approval', '', $st->value) }}">
                            <span class="marketing-legend-dot" aria-hidden="true"></span>{{ $st->label() }}
                        </span>
                    @endforeach
                </div>
            </header>
            <div class="calendar-scroll-wrapper p-3">
                <div id="marketingCalendar" wire:ignore></div>
            </div>
        </section>
    @else
        {{-- ── List / Table View ────────────────────────────────────────────────── --}}
        <section class="marketing-list-card bg-white border rounded-4 shadow-sm mb-3 overflow-hidden" aria-label="Danh sách kế hoạch Marketing" wire:key="marketing-list-view">
            <header class="d-flex align-items-center justify-content-between flex-wrap gap-3 p-3 border-bottom bg-white">
                <div>
                    <span class="d-block text-primary text-uppercase fw-bold text-xs mb-1">Danh sách nội dung</span>
                    <h2 class="h6 fw-bold text-dark mb-0">Tất cả kế hoạch</h2>
                </div>
                <span class="small text-muted">
                    <i class="fi fi-rr-info" aria-hidden="true"></i> Chọn tiêu đề để xem chi tiết
                </span>
            </header>
            <div class="marketing-list-body">
                <div class="table-responsive">
                    <table class="table marketing-plan-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="marketing-thumbnail-column">Nội dung</th>
                                <th>Tiêu đề & mô tả</th>
                                <th>Danh mục</th>
                                <th>Thời gian xuất bản</th>
                                <th>Người soạn</th>
                                <th>Trạng thái</th>
                                <th class="text-end" style="width: 150px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($listPlans as $plan)
                                <tr wire:key="marketing-plan-row-{{ $plan->id }}">
                                    <td data-label="Nội dung">
                                        @if ($plan->images->count() > 0)
                                            <img src="{{ Storage::url($plan->images->first()->file_path) }}"
                                                class="marketing-plan-thumbnail"
                                                alt="Ảnh minh họa cho {{ $plan->title }}"
                                                width="56" height="56" loading="lazy">
                                        @else
                                            <div class="marketing-plan-thumbnail marketing-plan-thumbnail-empty" aria-hidden="true">
                                                <i class="fi fi-rr-document"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td data-label="Tiêu đề">
                                        <button type="button" class="marketing-plan-title-button" wire:click="openDetail({{ $plan->id }})">
                                            {{ $plan->title }}
                                        </button>
                                        <p class="marketing-plan-excerpt mb-0">{{ Str::limit(strip_tags($plan->content), 90) ?: 'Chưa có nội dung chi tiết.' }}</p>
                                    </td>
                                    <td data-label="Danh mục">
                                        @php $catEnum = $plan->category; @endphp
                                        @if ($catEnum)
                                            <span class="badge marketing-category-badge {{ $catEnum->badgeClass() }}">
                                                {{ $catEnum->label() }}
                                            </span>
                                        @endif
                                    </td>
                                    <td data-label="Xuất bản">
                                        <div class="marketing-plan-date">
                                            <span class="marketing-date-icon" aria-hidden="true"><i class="fi fi-rr-calendar"></i></span>
                                            <span>
                                                <strong>{{ $plan->scheduled_at->format('H:i · d/m/Y') }}</strong>
                                                <small>{{ $plan->scheduled_at->diffForHumans() }}</small>
                                            </span>
                                        </div>
                                    </td>
                                    <td data-label="Người soạn">
                                        <span class="marketing-creator">
                                            <span class="marketing-creator-avatar" aria-hidden="true"><i class="fi fi-rr-user"></i></span>
                                            {{ $plan->creator?->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td data-label="Trạng thái">
                                        <span class="badge marketing-status-badge {{ $plan->status->badgeClass() }}">
                                            {{ $plan->status->label() }}
                                        </span>
                                    </td>
                                    <td class="text-end" data-label="Thao tác">
                                        <div class="marketing-row-actions">
                                            <button type="button" class="marketing-icon-button marketing-icon-button-view" aria-label="Xem chi tiết {{ $plan->title }}" title="Xem chi tiết" wire:click="openDetail({{ $plan->id }})">
                                                <i class="fi fi-rr-eye"></i>
                                            </button>
                                            @can('update', $plan)
                                                <button type="button" class="marketing-icon-button marketing-icon-button-edit" aria-label="Chỉnh sửa {{ $plan->title }}" title="Chỉnh sửa" wire:click="openEdit({{ $plan->id }})">
                                                    <i class="fi fi-rr-edit"></i>
                                                </button>
                                            @endcan
                                            @can('delete', $plan)
                                                <button type="button" class="marketing-icon-button marketing-icon-button-delete" aria-label="Xóa {{ $plan->title }}" title="Xóa" wire:click="deletePlan({{ $plan->id }})" wire:confirm="Bạn có chắc chắn muốn xóa bài viết này?">
                                                    <i class="fi fi-rr-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="marketing-empty-state">
                                            <span class="marketing-empty-icon" aria-hidden="true"><i class="fi fi-rr-search-alt"></i></span>
                                            <h3>Chưa tìm thấy kế hoạch phù hợp</h3>
                                            <p>Thử thay đổi từ khóa hoặc bộ lọc để xem thêm nội dung.</p>
                                            @can('create', App\Models\MarketingPlan::class)
                                                <button type="button" class="btn btn-outline-primary" wire:click="openCreate('{{ date('Y-m-d') }}')">
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
                    <div class="marketing-pagination">
                        {{ $listPlans->links() }}
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ── Modal Create / Edit Plan ─────────────────────────────────────────── --}}
    <div class="modal fade" id="modalPlanForm" tabindex="-1" aria-labelledby="modalPlanFormLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content border-0 rounded-4 overflow-hidden">
                <div class="modal-header bg-white border-bottom p-3 p-lg-4">
                    <div class="d-flex align-items-center gap-3">
                        <span class="marketing-modal-icon" aria-hidden="true">
                            <i class="fi {{ $planId ? 'fi-rr-edit' : 'fi-rr-add-document' }}"></i>
                        </span>
                        <div>
                            <span class="d-block text-primary text-uppercase fw-bold text-xs mb-1">{{ $planId ? 'Chỉnh sửa nội dung' : 'Nội dung mới' }}</span>
                            <h2 class="modal-title h5 fw-bold text-dark mb-1" id="modalPlanFormLabel">
                                {{ $planId ? 'Cập nhật kế hoạch bài viết' : 'Tạo kế hoạch bài viết' }}
                            </h2>
                            <p class="text-muted small mb-0 d-none d-sm-block">Điền thông tin, lịch xuất bản và nội dung cần phê duyệt.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng" wire:click="resetForm"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body bg-light p-3">
                        <div class="bg-white border rounded-3 p-3">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <span class="marketing-form-step">01</span>
                                <div>
                                    <h3 class="h6 fw-bold text-dark mb-1">Thông tin xuất bản</h3>
                                    <p class="text-muted small mb-0">Thông tin chính để hiển thị trên lịch và danh sách.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="marketing-plan-title" class="form-label small fw-semibold">Tiêu đề bài viết / Kế hoạch truyền thông <span class="text-danger">*</span></label>
                                    <input id="marketing-plan-title" type="text" wire:model="title" class="form-control @error('title') is-invalid @enderror" placeholder="Nhập tiêu đề bài viết truyền thông..." />
                                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-lg-4">
                                    <label for="marketing-plan-category" class="form-label small fw-semibold">Danh mục kế hoạch <span class="text-danger">*</span></label>
                                    <select id="marketing-plan-category" wire:model="category" class="form-select @error('category') is-invalid @enderror">
                                        @foreach ($categoriesEnum as $cat)
                                            <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-lg-4">
                                    <label for="marketing-plan-scheduled-at" class="form-label small fw-semibold">Thời gian dự kiến xuất bản <span class="text-danger">*</span></label>
                                    <input id="marketing-plan-scheduled-at" type="datetime-local" wire:model="scheduled_at" class="form-control @error('scheduled_at') is-invalid @enderror" />
                                    @error('scheduled_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-lg-4">
                                    <label for="marketing-plan-status" class="form-label small fw-semibold">Trạng thái gửi duyệt</label>
                                    <select id="marketing-plan-status" wire:model="status" class="form-select @error('status') is-invalid @enderror">
                                        <option value="draft">Bản nháp (Lưu tạm)</option>
                                        <option value="pending_approval">Gửi duyệt (Ban quản lý)</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border rounded-3 p-3 mt-3">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <span class="marketing-form-step">02</span>
                                <div>
                                    <h3 class="h6 fw-bold text-dark mb-1">Nội dung bài viết</h3>
                                    <p class="text-muted small mb-0">Soạn nội dung cần xuất bản hoặc gửi người quản lý phê duyệt.</p>
                                </div>
                            </div>
                            <div wire:ignore>
                                <label class="visually-hidden" for="quillEditor">Nội dung bài viết</label>
                                <div id="quillEditor" class="marketing-quill-editor"></div>
                                <input type="hidden" id="contentHiddenInput" wire:model="content">
                            </div>
                            @error('content') <div class="text-danger small mt-2" role="alert">{{ $message }}</div> @enderror
                        </div>

                        <div class="bg-white border rounded-3 p-3 mt-3">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <span class="marketing-form-step">03</span>
                                <div>
                                    <h3 class="h6 fw-bold text-dark mb-1">Tài liệu & ghi chú</h3>
                                    <p class="text-muted small mb-0">Đính kèm hình ảnh minh họa và ghi chú cho đội ngũ.</p>
                                </div>
                            </div>

                            <div class="bg-light border rounded-3 p-3">
                                <label for="marketing-plan-images" class="d-flex align-items-center gap-2 mb-2">
                                    <span class="marketing-upload-icon" aria-hidden="true"><i class="fi fi-rr-cloud-upload-alt"></i></span>
                                    <span>
                                        <strong class="d-block small">Chọn hình ảnh đính kèm</strong>
                                        <small class="d-block text-muted">JPG, PNG hoặc WEBP · tối đa 10MB mỗi ảnh</small>
                                    </span>
                                </label>
                                <input id="marketing-plan-images" type="file" wire:model="newImages" multiple accept="image/*" class="form-control @error('newImages.*') is-invalid @enderror">
                                @error('newImages.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                                <div wire:loading wire:target="newImages" class="marketing-upload-progress" role="status">
                                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Đang tải ảnh lên...
                                </div>

                                @if (count($existingImages) > 0)
                                    <div class="marketing-image-group">
                                        <span class="marketing-image-group-title">Hình ảnh hiện tại</span>
                                        <div class="marketing-image-grid">
                                            @foreach ($existingImages as $img)
                                                <div class="marketing-image-preview">
                                                    <img src="{{ $img['url'] }}" alt="{{ $img['name'] }}" width="96" height="96">
                                                    <button type="button" class="marketing-image-remove" aria-label="Xóa ảnh {{ $img['name'] }}" title="Xóa ảnh này" wire:click="markForImageDeletion({{ $img['id'] }})">
                                                        <i class="fi fi-rr-cross" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if (count($newImages) > 0)
                                    <div class="marketing-image-group">
                                        <span class="marketing-image-group-title">Hình ảnh vừa chọn</span>
                                        <div class="marketing-image-grid">
                                            @foreach ($newImages as $index => $imgFile)
                                                @if ($imgFile && method_exists($imgFile, 'temporaryUrl'))
                                                    <div class="marketing-image-preview">
                                                        <img src="{{ $imgFile->temporaryUrl() }}" alt="Ảnh vừa chọn {{ $index + 1 }}" width="96" height="96">
                                                        <button type="button" class="marketing-image-remove" aria-label="Hủy ảnh vừa chọn {{ $index + 1 }}" title="Hủy ảnh này" wire:click="removeNewImage({{ $index }})">
                                                            <i class="fi fi-rr-cross" aria-hidden="true"></i>
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
            <div class="modal-content border-0 rounded-4 overflow-hidden">
                @if ($selectedPlan)
                    <div class="modal-header marketing-detail-header p-3 p-lg-4 border-bottom">
                        <div class="marketing-detail-heading">
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <span class="badge marketing-status-badge {{ $selectedPlan->status->badgeClass() }}">
                                    {{ $selectedPlan->status->label() }}
                                </span>
                                @php $catEnum = $selectedPlan->category; @endphp
                                @if ($catEnum)
                                    <span class="badge marketing-category-badge {{ $catEnum->badgeClass() }}">
                                        {{ $catEnum->label() }}
                                    </span>
                                @endif
                            </div>
                            <h2 class="modal-title h4 fw-bold text-dark mb-2" id="modalPlanDetailLabel">{{ $selectedPlan->title }}</h2>
                            <p class="small text-muted mb-0">
                                <i class="fi fi-rr-calendar-clock" aria-hidden="true"></i>
                                Dự kiến xuất bản <strong>{{ $selectedPlan->scheduled_at->format('H:i · d/m/Y') }}</strong>
                            </p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>

                    <div class="modal-body bg-light p-3 p-lg-4">
                        {{-- Rejection reason alert --}}
                        @if ($selectedPlan->status === App\Enums\MarketingPlanStatus::Rejected && $selectedPlan->rejection_reason)
                            <div class="alert alert-danger d-flex align-items-start gap-2 mb-3" role="alert">
                                <span class="marketing-rejection-icon" aria-hidden="true"><i class="fi fi-rr-exclamation"></i></span>
                                <div>
                                    <strong>Lý do từ chối phê duyệt</strong>
                                    <p class="mb-0">{{ $selectedPlan->rejection_reason }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Metadata --}}
                        <div class="row g-2 mb-3">
                            <div class="col-md">
                              <div class="marketing-meta-item d-flex align-items-center gap-2 p-3 bg-white border rounded-3 h-100">
                                <span class="marketing-meta-icon" aria-hidden="true"><i class="fi fi-rr-user"></i></span>
                                <div>
                                    <small>Người soạn bài</small>
                                    <strong>{{ $selectedPlan->creator?->name ?? 'N/A' }}</strong>
                                </div>
                              </div>
                            </div>
                            <div class="col-md">
                              <div class="marketing-meta-item d-flex align-items-center gap-2 p-3 bg-white border rounded-3 h-100">
                                <span class="marketing-meta-icon" aria-hidden="true"><i class="fi fi-rr-time-check"></i></span>
                                <div>
                                    <small>Ngày tạo kế hoạch</small>
                                    <strong>{{ $selectedPlan->created_at->format('H:i · d/m/Y') }}</strong>
                                </div>
                              </div>
                            </div>
                            @if ($selectedPlan->approved_by)
                                <div class="col-md">
                                  <div class="marketing-meta-item d-flex align-items-center gap-2 p-3 bg-white border rounded-3 h-100">
                                    <span class="marketing-meta-icon" aria-hidden="true"><i class="fi fi-rr-user-check"></i></span>
                                    <div>
                                        <small>Người xử lý duyệt</small>
                                        <strong>{{ $selectedPlan->approver?->name ?? 'N/A' }}</strong>
                                        <span>{{ $selectedPlan->approved_at?->format('H:i · d/m/Y') }}</span>
                                    </div>
                                  </div>
                                </div>
                            @endif
                        </div>

                        {{-- Rich Text Content Display --}}
                        <section class="bg-white border rounded-3 p-3">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="marketing-detail-section-icon" aria-hidden="true"><i class="fi fi-rr-document-signed"></i></span>
                                <div>
                                    <span class="d-block text-primary text-uppercase fw-bold text-xs">Nội dung</span>
                                    <h3 class="h6 fw-bold text-dark mb-0">Nội dung truyền thông</h3>
                                </div>
                            </div>
                            <div class="ql-editor-preview bg-light border rounded-3 p-3">
                                {!! $selectedPlan->content ?: '<em class="text-muted">Chưa nhập nội dung chi tiết.</em>' !!}
                            </div>
                        </section>

                        {{-- Images Gallery --}}
                        @if ($selectedPlan->images->count() > 0)
                            <section class="bg-white border rounded-3 p-3 mt-3">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="marketing-detail-section-icon" aria-hidden="true"><i class="fi fi-rr-picture"></i></span>
                                    <div>
                                        <span class="d-block text-primary text-uppercase fw-bold text-xs">Tài liệu</span>
                                        <h3 class="h6 fw-bold text-dark mb-0">Hình ảnh đính kèm <small class="text-muted">{{ $selectedPlan->images->count() }} ảnh</small></h3>
                                    </div>
                                </div>
                                <div class="marketing-detail-gallery">
                                    @foreach ($selectedPlan->images as $img)
                                        <a href="{{ Storage::url($img->file_path) }}" target="_blank" rel="noopener" class="marketing-gallery-item">
                                            <img src="{{ Storage::url($img->file_path) }}" alt="{{ $img->file_name }}" loading="lazy">
                                            <span><i class="fi fi-rr-expand" aria-hidden="true"></i> Xem ảnh</span>
                                        </a>
                                    @endforeach
                                </div>
                            </section>
                        @endif

                        @if ($selectedPlan->notes)
                            <section class="bg-warning-subtle border border-warning-subtle rounded-3 p-3 mt-3">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="marketing-detail-section-icon" aria-hidden="true"><i class="fi fi-rr-notebook"></i></span>
                                    <div>
                                        <span class="d-block text-warning-emphasis text-uppercase fw-bold text-xs">Nội bộ</span>
                                        <h3 class="h6 fw-bold text-dark mb-0">Ghi chú cho đội ngũ</h3>
                                    </div>
                                </div>
                                <p class="marketing-notes-content mb-0">{{ $selectedPlan->notes }}</p>
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
            <div class="modal-content border-0 rounded-4 overflow-hidden">
                <div class="modal-header bg-white border-bottom p-3">
                    <div class="d-flex align-items-center gap-3">
                        <span class="marketing-modal-icon marketing-modal-icon-danger" aria-hidden="true"><i class="fi fi-rr-cross-circle"></i></span>
                        <div>
                            <span class="d-block text-danger text-uppercase fw-bold text-xs mb-1">Yêu cầu chỉnh sửa</span>
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
    <link rel="stylesheet" href="{{ asset('css/marketing-plan.css') }}?v=2.0.0">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/fullcalendar.global.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        let mCalendarInstance = null;
        let mCalendarWireId = null;
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

            if (mCalendarInstance && mCalendarWireId === currentWireId && mCalendarElement === calendarEl) {
                mCalendarInstance.refetchEvents();
                return;
            }

            if (mCalendarInstance) {
                mCalendarInstance.destroy();
            }

            mCalendarWireId = currentWireId;
            mCalendarElement = calendarEl;

            mCalendarInstance = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                fixedWeekCount: false,
                locale: 'vi',
                firstDay: 1,
                height: 'auto',
                dayMaxEvents: 3,
                displayEventTime: false,
                moreLinkText: function(count) {
                    return `+${count} nội dung`;
                },
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
                        plusBtn.setAttribute('aria-label', `Tạo kế hoạch ngày ${arg.date.toLocaleDateString('vi-VN')}`);

                        const plusIcon = document.createElement('i');
                        plusIcon.className = 'fi fi-rr-plus';
                        plusIcon.setAttribute('aria-hidden', 'true');
                        plusBtn.appendChild(plusIcon);

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
                    const allowedStatuses = ['draft', 'pending_approval', 'approved', 'rejected'];
                    const status = allowedStatuses.includes(props.status) ? props.status : 'draft';
                    const card = document.createElement('div');
                    card.className = `marketing-calendar-event status-${status}`;

                    const top = document.createElement('div');
                    top.className = 'marketing-calendar-event-top';

                    const statusEl = document.createElement('span');
                    statusEl.className = 'marketing-calendar-event-status';
                    const statusDot = document.createElement('span');
                    statusDot.className = 'marketing-calendar-event-dot';
                    statusDot.setAttribute('aria-hidden', 'true');
                    const statusText = document.createElement('span');
                    statusText.textContent = props.status_label || '';
                    statusEl.append(statusDot, statusText);

                    const categoryEl = document.createElement('span');
                    categoryEl.className = 'marketing-calendar-event-category';
                    categoryEl.textContent = props.category_label || '';
                    top.append(statusEl, categoryEl);

                    const titleRow = document.createElement('div');
                    titleRow.className = 'marketing-calendar-event-title';

                    if (props.thumbnail_url) {
                        const image = document.createElement('img');
                        image.src = props.thumbnail_url;
                        image.alt = '';
                        image.loading = 'lazy';
                        titleRow.appendChild(image);
                    } else {
                        const placeholder = document.createElement('span');
                        placeholder.className = 'marketing-calendar-event-placeholder';
                        placeholder.setAttribute('aria-hidden', 'true');
                        const documentIcon = document.createElement('i');
                        documentIcon.className = 'fi fi-rr-document';
                        placeholder.appendChild(documentIcon);
                        titleRow.appendChild(placeholder);
                    }

                    const titleText = document.createElement('span');
                    titleText.textContent = props.raw_title || '';
                    titleRow.appendChild(titleText);
                    card.append(top, titleRow);

                    return { domNodes: [card] };
                },
                eventDidMount: function(info) {
                    const props = info.event.extendedProps;
                    info.el.setAttribute('title', `${props.raw_title || ''} · ${props.status_label || ''}`);
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

            Livewire.hook('morph.updated', () => {
                window.requestAnimationFrame(initMarketingCalendar);
            });
        });

        document.addEventListener('livewire:navigated', () => {
            initMarketingCalendar();
            setTimeout(initQuillEditor, 300);
        });
    </script>
@endpush
