<div>
    <div class="app-page-head d-flex align-items-center justify-content-between">
        <div class="clearfix">
            <h1 class="app-page-title">Lịch công tác</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Lịch công tác
                    </li>
                </ol>
            </nav>
        </div>
        @can('create', App\Models\DutySchedule::class)
            <button type="button" class="btn btn-primary waves-effect waves-light"
                wire:click="openCreate('{{ date('Y-m-d') }}')" wire:loading.attr="disabled">
                <i class="fi fi-rr-plus me-1"></i> Thêm lịch công tác
            </button>
        @endcan
    </div>

    @if ($successMessage)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fi fi-rr-check me-2"></i> {{ $successMessage }}
            <button type="button" class="btn-close" wire:click="$set('successMessage', null)" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── Filter & Action Toolbar ────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-3" style="overflow: visible; position: relative; z-index: 20;">
        <div class="card-body py-2 px-3" style="overflow: visible;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2" style="overflow: visible;">
                
                {{-- Left: Filters --}}
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="d-flex align-items-center gap-1">
                        <label class="form-label mb-0 fw-semibold text-dark text-sm me-1" for="filterParticipant">Thành viên:</label>
                        <select class="form-select form-select-sm shadow-none" wire:model.live="filterUserId" id="filterParticipant"
                            style="min-width: 180px; border-color: #cbd5e1;">
                            <option value="0">Tất cả nhân viên</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <label class="form-label mb-0 fw-semibold text-dark text-sm me-1" for="filterMonth">Tháng:</label>
                        <input type="month" id="filterMonth" class="form-control form-control-sm shadow-none"
                            style="min-width: 160px; border-color: #cbd5e1;" />
                    </div>
                </div>

                {{-- Right: Actions & Toggle --}}
                <div class="d-flex flex-wrap align-items-center gap-2 ms-auto" style="overflow: visible; position: relative;">
                    @if ($canViewNoibo)
                        <div class="form-check form-switch mb-0 me-2">
                            <input type="checkbox" class="form-check-input" id="toggleNoiboSchedules"
                                wire:model.live="showNoiboSchedules" />
                            <label class="form-check-label fw-semibold small" for="toggleNoiboSchedules">
                                <span class="noibo-badge me-1">Bảo Châu</span> Hiển thị lịch Bảo Châu
                            </label>
                        </div>
                    @endif

                    {{-- Nút Xuất Excel (Dropdown không bị che) --}}
                    <div class="dropdown" style="position: relative; z-index: 30;">
                        <button class="btn btn-success btn-sm waves-effect waves-light dropdown-toggle d-inline-flex align-items-center gap-1 fw-semibold px-3 shadow-sm"
                            type="button" id="dropdownExportExcel" data-bs-toggle="dropdown" aria-expanded="false"
                            style="background-color: #10b981; border-color: #10b981;">
                            <i class="fi fi-rr-file-excel"></i>
                            <span>Xuất Excel</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border" aria-labelledby="dropdownExportExcel"
                            style="position: absolute; z-index: 1050; min-width: 210px; margin-top: 4px; border-color: #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 small py-2 fw-medium" href="#"
                                    onclick="alert('Đang tạo file Xuất tổng hợp Excel...'); return false;">
                                    <i class="fi fi-rr-file-spreadsheet text-success"></i>
                                    <span>Xuất tổng hợp</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 small py-2 fw-medium" href="#"
                                    onclick="alert('Đang tạo file Xuất chi tiết chấm công...'); return false;">
                                    <i class="fi fi-rr-time-check text-primary"></i>
                                    <span>Xuất chi tiết chấm công</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 small py-2 fw-medium" href="#"
                                    onclick="alert('Đang tạo file Báo cáo đi trễ / về sớm...'); return false;">
                                    <i class="fi fi-rr-exclamation text-warning"></i>
                                    <span>Xuất trễ / về sớm</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- Nút Import dữ liệu --}}
                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-light d-inline-flex align-items-center gap-1 fw-semibold px-3 shadow-sm"
                        style="background-color: #3b82f6; border-color: #3b82f6;"
                        onclick="alert('Chức năng Import dữ liệu đang được chuẩn bị...')">
                        <i class="fi fi-rr-file-import"></i>
                        <span>Import dữ liệu</span>
                    </button>

                    {{-- Nút Nhân viên --}}
                    <button type="button" class="btn btn-outline-warning btn-sm waves-effect d-inline-flex align-items-center gap-1 fw-semibold px-3"
                        style="color: #ea580c; border-color: #f97316;"
                        wire:click="$set('filterUserId', 0)">
                        <i class="fi fi-rr-users-alt"></i>
                        <span>Nhân viên</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="p-4 calendar-scroll-wrapper" wire:ignore>
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form (Add / Edit Event) -->
    <div class="modal fade" id="modalAddEvent" tabindex="-1" aria-labelledby="modalAddEventLabel" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark" id="modalAddEventLabel">
                        {{ $scheduleId ? 'Cập nhật lịch công tác' : 'Thêm lịch công tác' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="resetForm"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="save">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" wire:model="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    placeholder="Nhập tiêu đề công việc..." />
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Màu sắc <span
                                        class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                                    <label class="color-selector-label">
                                        <input type="radio" wire:model="label_color" value="primary" class="btn-check" />
                                        <span class="badge border border-2 d-inline-flex align-items-center gap-1 px-3 py-2 bg-primary-subtle text-primary fw-semibold rounded-pill color-badge-select">
                                            <span class="d-inline-block rounded-circle bg-primary" style="width: 10px; height: 10px;"></span>
                                            Mặc định
                                        </span>
                                    </label>
                                    <label class="color-selector-label">
                                        <input type="radio" wire:model="label_color" value="success" class="btn-check" />
                                        <span class="badge border border-2 d-inline-flex align-items-center gap-1 px-3 py-2 bg-success-subtle text-success fw-semibold rounded-pill color-badge-select">
                                            <span class="d-inline-block rounded-circle bg-success" style="width: 10px; height: 10px;"></span>
                                            Hoàn thành
                                        </span>
                                    </label>
                                    <label class="color-selector-label">
                                        <input type="radio" wire:model="label_color" value="info" class="btn-check" />
                                        <span class="badge border border-2 d-inline-flex align-items-center gap-1 px-3 py-2 bg-info-subtle text-info fw-semibold rounded-pill color-badge-select">
                                            <span class="d-inline-block rounded-circle bg-info" style="width: 10px; height: 10px;"></span>
                                            Cuộc họp
                                        </span>
                                    </label>
                                    <label class="color-selector-label">
                                        <input type="radio" wire:model="label_color" value="purple" class="btn-check" />
                                        <span class="badge border border-2 d-inline-flex align-items-center gap-1 px-3 py-2 bg-purple-subtle text-purple fw-semibold rounded-pill color-badge-select">
                                            <span class="d-inline-block rounded-circle bg-purple" style="width: 10px; height: 10px;"></span>
                                            Họp công tác
                                        </span>
                                    </label>
                                    <label class="color-selector-label">
                                        <input type="radio" wire:model="label_color" value="warning" class="btn-check" />
                                        <span class="badge border border-2 d-inline-flex align-items-center gap-1 px-3 py-2 bg-warning-subtle text-warning fw-semibold rounded-pill color-badge-select">
                                            <span class="d-inline-block rounded-circle bg-warning" style="width: 10px; height: 10px;"></span>
                                            Cá nhân / Nháp
                                        </span>
                                    </label>
                                    <label class="color-selector-label">
                                        <input type="radio" wire:model="label_color" value="danger" class="btn-check" />
                                        <span class="badge border border-2 d-inline-flex align-items-center gap-1 px-3 py-2 bg-danger-subtle text-danger fw-semibold rounded-pill color-badge-select">
                                            <span class="d-inline-block rounded-circle bg-danger" style="width: 10px; height: 10px;"></span>
                                            Khẩn cấp
                                        </span>
                                    </label>
                                </div>
                                @error('label_color')
                                    <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch mt-1">
                                    <input type="checkbox"
                                        class="form-check-input @error('is_private') is-invalid @enderror"
                                        id="is_private" wire:model="is_private" />
                                    <label class="form-check-label fw-semibold" for="is_private">
                                        Lịch riêng tư (Chỉ bạn và quản trị viên nhìn thấy chi tiết)
                                    </label>
                                    @error('is_private')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">Thời gian bắt đầu <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local" wire:model="start_at"
                                    class="form-control @error('start_at') is-invalid @enderror" />
                                @error('start_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">Thời gian kết thúc</label>
                                <input type="datetime-local" wire:model="end_at"
                                    class="form-control @error('end_at') is-invalid @enderror" />
                                @error('end_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <label class="form-label fw-semibold text-xs">Giờ Check-in thực tế</label>
                                <input type="datetime-local" wire:model.live="check_in_at"
                                    class="form-control form-control-sm @error('check_in_at') is-invalid @enderror" />
                                @error('check_in_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <label class="form-label fw-semibold text-xs">Giờ Check-out thực tế</label>
                                <input type="datetime-local" wire:model.live="check_out_at"
                                    class="form-control form-control-sm @error('check_out_at') is-invalid @enderror" />
                                @error('check_out_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <label class="form-label fw-semibold text-xs">Đi trễ (sau 08:00)</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" min="0" wire:model="late_minutes"
                                        class="form-control @error('late_minutes') is-invalid @enderror"
                                        placeholder="0" />
                                    <span class="input-group-text">phút</span>
                                </div>
                                @error('late_minutes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <label class="form-label fw-semibold text-xs">Về sớm (trước 17:00)</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" min="0" wire:model="early_minutes"
                                        class="form-control @error('early_minutes') is-invalid @enderror"
                                        placeholder="0" />
                                    <span class="input-group-text">phút</span>
                                </div>
                                @error('early_minutes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Địa điểm</label>
                                <input type="text" wire:model="location"
                                    class="form-control @error('location') is-invalid @enderror"
                                    placeholder="Nhập địa điểm họp, làm việc..." />
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Nội dung chi tiết</label>
                                <textarea wire:model="description"
                                    class="form-control @error('description') is-invalid @enderror" rows="3"
                                    placeholder="Mô tả nội dung công việc chi tiết..."></textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Thành viên tham gia</label>
                                <div
                                    class="border rounded p-3 user-select-list @error('user_ids') is-invalid @enderror"
                                    style="max-height: 220px; overflow-y: auto;">
                                    <div class="small fw-bold text-success mb-2 pb-1 border-bottom">Greeco</div>
                                    @foreach ($users as $u)
                                        <div class="form-check mb-1">
                                            <input type="checkbox" class="form-check-input" id="user_check_{{ $u->id }}"
                                                value="{{ $u->id }}" wire:model="user_ids" />
                                            <label class="form-check-label text-dark fw-medium"
                                                for="user_check_{{ $u->id }}">
                                                {{ $u->name }}@if($u->id === auth()->id()) (Bạn)@endif
                                            </label>
                                        </div>
                                    @endforeach

                                    @if (!empty($baochauUsers))
                                        <div class="small fw-bold text-primary mt-3 mb-2 pb-1 border-bottom">Bảo Châu</div>
                                        @foreach ($baochauUsers as $bu)
                                            <div class="form-check mb-1">
                                                <input type="checkbox" class="form-check-input" id="user_check_baochau_{{ $bu['id'] }}"
                                                    value="baochau_{{ $bu['id'] }}" wire:model="user_ids" />
                                                <label class="form-check-label text-dark fw-medium"
                                                    for="user_check_baochau_{{ $bu['id'] }}">
                                                    {{ $bu['name'] }} ({{ $bu['department'] ?? 'Bảo Châu' }})
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                @error('user_ids')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-light waves-effect me-2" data-bs-dismiss="modal"
                                    wire:click="resetForm">
                                    Hủy
                                </button>
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    Lưu lại
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal View Details -->
    <div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-hidden="true" wire:ignore>
        <div class="modal-dialog modal-dialog-centered duty-schedule-detail-dialog">
            <div class="modal-content duty-schedule-detail-modal">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark" id="detailTitle">Chi tiết lịch công tác</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body duty-schedule-detail-body">
                    <div class="schedule-detail-list">
                        <div class="schedule-detail-item">
                            <strong class="schedule-detail-label"><i class="fi fi-rr-clock"></i>Bắt đầu:</strong>
                            <span id="detailStart" class="schedule-detail-value"></span>
                        </div>
                        <div class="schedule-detail-item">
                            <strong class="schedule-detail-label"><i class="fi fi-rr-calendar"></i>Kết thúc:</strong>
                            <span id="detailEnd" class="schedule-detail-value"></span>
                        </div>
                        <div class="schedule-detail-item">
                            <strong class="schedule-detail-label"><i class="fi fi-rr-time-check"></i>Check-in thực tế:</strong>
                            <span id="detailCheckIn" class="schedule-detail-value"></span>
                        </div>
                        <div class="schedule-detail-item">
                            <strong class="schedule-detail-label"><i class="fi fi-rr-clock-three"></i>Check-out thực tế:</strong>
                            <span id="detailCheckOut" class="schedule-detail-value"></span>
                        </div>
                        <div class="schedule-detail-item">
                            <strong class="schedule-detail-label"><i class="fi fi-rr-exclamation"></i>Trạng thái đi trễ:</strong>
                            <span id="detailLateMinutes" class="schedule-detail-value"></span>
                        </div>
                        <div class="schedule-detail-item">
                            <strong class="schedule-detail-label"><i class="fi fi-rr-sign-out-alt"></i>Trạng thái về sớm:</strong>
                            <span id="detailEarlyMinutes" class="schedule-detail-value"></span>
                        </div>
                        <div class="schedule-detail-item">
                            <strong class="schedule-detail-label"><i class="fi fi-rr-marker"></i>Địa điểm:</strong>
                            <span id="detailLocation" class="schedule-detail-value"></span>
                        </div>
                        <div class="schedule-detail-item">
                            <strong class="schedule-detail-label"><i class="fi fi-rr-user"></i>Người tạo:</strong>
                            <div class="schedule-detail-value">
                                <span id="detailCreator" class="schedule-detail-chip schedule-detail-chip-info"></span>
                            </div>
                        </div>
                        <div class="schedule-detail-item">
                            <strong class="schedule-detail-label"><i class="fi fi-rr-users"></i>Thành viên tham
                                gia:</strong>
                            <div id="detailParticipants" class="schedule-detail-value schedule-detail-chips"></div>
                        </div>
                        <div class="schedule-detail-item">
                            <strong class="schedule-detail-label"><i class="fi fi-rr-document-signed"></i>Nội
                                dung:</strong>
                            <div id="detailDescription" class="schedule-detail-value schedule-detail-description"
                                style="white-space: pre-wrap;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-bs-dismiss="modal">
                        Đóng
                    </button>
                    <button type="button" id="detailBtnEdit" class="btn btn-warning waves-effect waves-light">
                        <i class="fi fi-rr-edit me-1"></i> Chỉnh sửa
                    </button>
                    <button type="button" id="detailBtnDelete" class="btn btn-danger waves-effect waves-light">
                        <i class="fi fi-rr-trash me-1"></i> Xóa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Day Schedules List (for Director / users who cannot create) -->
    <div class="modal fade" id="daySchedulesModal" tabindex="-1" aria-labelledby="daySchedulesModalLabel"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark" id="daySchedulesModalLabel">
                        <i class="fi fi-rr-calendar text-primary me-2"></i> Lịch công tác ngày {{ $selectedDateStr }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto; overflow-x: hidden;">
                    @if (empty($daySchedules))
                        <div class="text-center py-5">
                            <i class="fi fi-rr-calendar-slash scale-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-0">Không có lịch công tác nào trong ngày này.</p>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-3">
                            @foreach ($daySchedules as $schedule)
                                <div class="card daily-report-card">
                                    <div class="card-body p-4">
                                        {{-- Header: badge + time + actions --}}
                                        <div class="report-header">
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <span class="badge
                                                    @if($schedule['label_color'] === 'success') bg-success-subtle text-success-emphasis border border-success-subtle
                                                    @elseif($schedule['label_color'] === 'warning') bg-warning-subtle text-warning-emphasis border border-warning-subtle
                                                    @elseif($schedule['label_color'] === 'danger') bg-danger-subtle text-danger-emphasis border border-danger-subtle
                                                    @elseif($schedule['label_color'] === 'info') bg-info-subtle text-info-emphasis border border-info-subtle
                                                    @elseif($schedule['label_color'] === 'purple') bg-purple-subtle text-purple border border-purple-subtle
                                                    @elseif($schedule['label_color'] === 'noibo') bg-warning-subtle text-dark-emphasis border border-warning-subtle
                                                    @elseif($schedule['label_color'] === 'private') bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle opacity-75
                                                    @else bg-primary-subtle text-primary-emphasis border border-primary-subtle
                                                    @endif fw-semibold px-2 py-1">
                                                    @if($schedule['label_color'] === 'success') Hoàn thành
                                                    @elseif($schedule['label_color'] === 'warning') Cá nhân / Nháp
                                                    @elseif($schedule['label_color'] === 'danger') Khẩn cấp
                                                    @elseif($schedule['label_color'] === 'info') Cuộc họp
                                                    @elseif($schedule['label_color'] === 'purple') Họp công tác
                                                    @elseif($schedule['label_color'] === 'noibo') <span class="noibo-badge">Bảo Châu</span>
                                                    @elseif($schedule['label_color'] === 'private') Lịch riêng tư
                                                    @else Mặc định
                                                    @endif
                                                </span>
                                                <span class="report-time">
                                                    <i class="fi fi-rr-clock"></i>
                                                    {{ $schedule['start_formatted'] }}
                                                    @if ($schedule['end_formatted'])
                                                        - {{ $schedule['end_formatted'] }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center gap-1">
                                                @if (($schedule['source'] ?? 'greeco') !== 'noibo')
                                                    @if ($schedule['can_edit'])
                                                        <button type="button" class="report-action-btn btn-edit"
                                                            title="Chỉnh sửa" wire:click="openEditFromList({{ $schedule['id'] }})">
                                                            <i class="fi fi-rr-edit"></i>
                                                        </button>
                                                    @endif
                                                    @if ($schedule['can_delete'])
                                                        <button type="button" class="report-action-btn btn-delete" title="Xóa"
                                                            wire:click="deleteFromList({{ $schedule['id'] }})"
                                                            wire:confirm="Bạn có chắc chắn muốn xóa lịch công tác này không?">
                                                            <i class="fi fi-rr-trash"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Title --}}
                                        <h6 class="fw-bold text-dark mb-3" style="font-size: 15px;">{{ $schedule['title'] }}</h6>

                                        {{-- Details --}}
                                        <div class="d-flex flex-column gap-2">
                                            <div class="report-section-title text-muted" style="font-size: 13px; font-weight: 600;">
                                                <i class="fi fi-rr-marker" style="color: #64748b;"></i>
                                                <span><strong>Địa điểm:</strong> {{ $schedule['location'] ?: 'Không có' }}</span>
                                            </div>
                                            <div class="report-section-title text-muted" style="font-size: 13px; font-weight: 600;">
                                                <i class="fi fi-rr-user" style="color: #64748b;"></i>
                                                <span>
                                                    <strong>Người tạo:</strong>
                                                    <span class="badge bg-info-subtle text-info border border-info text-xs ms-1">{{ $schedule['creator_name'] }}</span>
                                                </span>
                                            </div>
                                            <div class="report-section-title text-muted" style="font-size: 13px; font-weight: 600;">
                                                <i class="fi fi-rr-users" style="color: #64748b;"></i>
                                                <span>
                                                    <strong>Thành viên:</strong>
                                                    @if (empty($schedule['participants']))
                                                        <span class="text-muted ms-1">Không có</span>
                                                    @else
                                                        <span class="d-inline-flex flex-wrap gap-1 align-items-center ms-1">
                                                            @foreach ($schedule['participants'] as $p)
                                                                <span class="badge bg-primary-subtle text-primary border border-primary text-xs">{{ $p['name'] }}</span>
                                                            @endforeach
                                                        </span>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="report-section-title text-muted" style="font-size: 13px; font-weight: 600;">
                                                <i class="fi fi-rr-time-check" style="color: #64748b;"></i>
                                                <span><strong>Giờ Check-in thực tế:</strong> {{ $schedule['check_in_formatted'] ?? 'Chưa ghi nhận' }}</span>
                                            </div>
                                            <div class="report-section-title text-muted" style="font-size: 13px; font-weight: 600;">
                                                <i class="fi fi-rr-clock-three" style="color: #64748b;"></i>
                                                <span><strong>Giờ Check-out thực tế:</strong> {{ $schedule['check_out_formatted'] ?? 'Chưa ghi nhận' }}</span>
                                            </div>
                                            <div class="report-section-title text-muted" style="font-size: 13px; font-weight: 600;">
                                                <i class="fi fi-rr-exclamation" style="color: #64748b;"></i>
                                                <span>
                                                    <strong>Đi trễ / Về sớm:</strong>
                                                    @if (!empty($schedule['late_minutes']) && $schedule['late_minutes'] > 0)
                                                        <span class="badge bg-danger-subtle text-danger border border-danger ms-1 fw-bold">Trễ {{ $schedule['late_minutes'] }} phút</span>
                                                    @else
                                                        <span class="badge bg-success-subtle text-success border border-success ms-1">Vào đúng giờ</span>
                                                    @endif

                                                    @if (!empty($schedule['early_minutes']) && $schedule['early_minutes'] > 0)
                                                        <span class="badge bg-warning-subtle text-warning border border-warning ms-1 fw-bold">Về sớm {{ $schedule['early_minutes'] }} phút</span>
                                                    @else
                                                        <span class="badge bg-success-subtle text-success border border-success ms-1">Đủ giờ (17:00)</span>
                                                    @endif
                                                </span>
                                            </div>
                                            @if ($schedule['description'])
                                                <div class="report-section-issues mt-1">{{ $schedule['description'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="modal-footer justify-content-between">
                    <div>
                        @can('create', App\Models\DutySchedule::class)
                            @if($selectedDateStr && \Carbon\Carbon::createFromFormat('d/m/Y', $selectedDateStr)->startOfDay()->gte(\Carbon\Carbon::today()))
                                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light"
                                    wire:click="openCreateFromList">
                                    <i class="fi fi-rr-plus me-1"></i> Thêm lịch ngày này
                                </button>
                            @endif
                        @endcan
                    </div>
                    <button type="button" class="btn btn-light btn-sm waves-effect mb-0" data-bs-dismiss="modal">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/duty-schedule.css') }}?v=1.2.0">
@endpush


@push('scripts')
    <script src="{{ asset('js/fullcalendar.global.min.js') }}"></script>
    <script>
        let calendarInstance = null;
        let calendarWireId = null;

        function formatDateTime(date) {
            const d = new Date(date);
            return d.toLocaleString('vi-VN', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function updateEventCounts(events) {
            // Clear existing badges
            document.querySelectorAll('.day-event-count-badge').forEach(el => el.remove());

            const counts = {};

            events.forEach(event => {
                if (!event.start) return;

                const start = new Date(event.start);
                const y = start.getFullYear();
                const m = String(start.getMonth() + 1).padStart(2, '0');
                const d = String(start.getDate()).padStart(2, '0');
                const dateStr = `${y}-${m}-${d}`;

                counts[dateStr] = (counts[dateStr] || 0) + 1;
            });

            // Render badges
            for (const [dateStr, count] of Object.entries(counts)) {
                const cellEl = document.querySelector(`.fc-daygrid-day[data-date="${dateStr}"]`);
                if (!cellEl) continue;

                const actionsContainer = cellEl.querySelector('.day-cell-actions');
                if (actionsContainer) {
                    const badge = document.createElement('span');
                    badge.className = 'day-event-count-badge';
                    badge.innerText = count;
                    actionsContainer.insertBefore(badge, actionsContainer.firstChild);
                }
            }
        }

        function showBootstrapModal(modalId) {
            if (window.GreecoModal) {
                window.GreecoModal.show(modalId);
                return;
            }

            const modalEl = document.getElementById(modalId);

            if (!modalEl || typeof bootstrap === 'undefined' || modalEl.classList.contains('show')) {
                return;
            }

            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }

        function hideBootstrapModal(modalId) {
            if (window.GreecoModal) {
                window.GreecoModal.hide(modalId);
                return;
            }

            const modalEl = document.getElementById(modalId);

            if (!modalEl || typeof bootstrap === 'undefined') {
                return;
            }

            bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        }

        function initCalendar() {
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) return;

            if (typeof Livewire === 'undefined') {
                document.addEventListener('livewire:init', initCalendar, { once: true });
                return;
            }

            const componentEl = calendarEl.closest('[wire\\:id]');
            if (!componentEl) return;
            const currentWireId = componentEl.getAttribute('wire:id');
            const wire = Livewire.find(currentWireId);
            if (!wire) return;

            if (calendarInstance && calendarWireId === currentWireId) {
                calendarInstance.refetchEvents();
                return;
            }

            if (calendarInstance) {
                calendarInstance.destroy();
            }

            calendarWireId = currentWireId;

            calendarInstance = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                fixedWeekCount: false,
                locale: 'vi',
                firstDay: 1, // Start week on Monday
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
                editable: false,
                selectable: true,
                datesSet: function (dateInfo) {
                    const currentDate = calendarInstance.getDate();
                    const year = currentDate.getFullYear();
                    const month = String(currentDate.getMonth() + 1).padStart(2, '0');
                    const monthInput = document.getElementById('filterMonth');
                    if (monthInput) {
                        monthInput.value = `${year}-${month}`;
                    }
                },
                dayCellContent: function (arg) {
                    // Day number element
                    const numberEl = document.createElement('span');
                    const cleanNum = arg.dayNumberText.replace('thg', '').replace('tháng', '').replace(/[a-zA-Z]/g, '').trim();

                    if (arg.isToday) {
                        numberEl.className = 'day-cell-today-number';
                    } else {
                        numberEl.className = 'day-cell-number';
                    }
                    numberEl.innerText = cleanNum;

                    // Actions container
                    const rightContainer = document.createElement('div');
                    rightContainer.className = 'day-cell-actions';

                    // Check if date is in the past
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const cellDate = new Date(arg.date);
                    cellDate.setHours(0, 0, 0, 0);
                    const isPast = cellDate < today;

                    // Plus button (only if not in the past)
                    if (!isPast) {
                        @can('create', App\Models\DutySchedule::class)
                            const plusBtn = document.createElement('button');
                            plusBtn.type = 'button';
                            plusBtn.className = 'btn-plus-day';
                            plusBtn.innerHTML = '<i class="fi fi-rr-plus" style="font-size: 8px; line-height: 1;"></i>';
                            plusBtn.onclick = function (e) {
                                e.stopPropagation();
                                // Format date to YYYY-MM-DD
                                const year = arg.date.getFullYear();
                                const month = String(arg.date.getMonth() + 1).padStart(2, '0');
                                const day = String(arg.date.getDate()).padStart(2, '0');
                                const dateStr = `${year}-${month}-${day}`;
                                wire.openCreate(dateStr);
                            };
                            rightContainer.appendChild(plusBtn);
                        @endcan
                    }

                    return { domNodes: [numberEl, rightContainer] };
                },
                eventContent: function (arg) {
                    const event = arg.event;
                    const props = event.extendedProps;

                    // 1. Get event time string
                    let timeStr = '';
                    if (event.allDay) {
                        timeStr = 'Cả ngày';
                    } else if (event.start) {
                        // Check if start time is 00:00:00 and end is null or 23:59:59 (all day)
                        const isAllDayLike = event.start.getHours() === 0 && event.start.getMinutes() === 0;
                        if (isAllDayLike && (!event.end || (event.end.getHours() === 0 && event.end.getMinutes() === 0))) {
                            timeStr = 'Cả ngày';
                        } else {
                            const startHours = String(event.start.getHours()).padStart(2, '0');
                            const startMinutes = String(event.start.getMinutes()).padStart(2, '0');
                            const startStr = `${startHours}:${startMinutes}`;

                            if (event.end) {
                                const endHours = String(event.end.getHours()).padStart(2, '0');
                                const endMinutes = String(event.end.getMinutes()).padStart(2, '0');
                                const endStr = `${endHours}:${endMinutes}`;
                                timeStr = `${startStr} - ${endStr}`;
                            } else {
                                timeStr = startStr;
                            }
                        }
                    }

                    // 2. Format creator & participants names list
                    let namesList = [];
                    if (props.creator_name) {
                        namesList.push(props.creator_name);
                    }
                    if (props.participants && props.participants.length > 0) {
                        props.participants.forEach(p => {
                            if (p.name !== props.creator_name) {
                                namesList.push(p.name);
                            }
                        });
                    }
                    // Limit display to at most 2 names
                    let namesStr = '';
                    if (namesList.length > 2) {
                        namesStr = namesList.slice(0, 2).join(', ') + ' + ' + (namesList.length - 2);
                    } else {
                        namesStr = namesList.filter(Boolean).join(', ');
                    }

                    // 3. Create DOM structure
                    const card = document.createElement('div');
                    const isNoibo = (props.source === 'noibo');
                    const themeClass = isNoibo ? 'event-theme-noibo' : ('event-theme-' + (props.label_color || 'primary'));
                    card.className = `greeco-event-card ${themeClass}`;
                    const eventTitle = props.raw_title || event.title;
                    card.title = [eventTitle, timeStr, namesStr].filter(Boolean).join(' · ');

                    // Check-in (top line) & Check-out (bottom line) display
                    const hasCheckIn = Boolean(props.check_in_time);
                    const hasCheckOut = Boolean(props.check_out_time);

                    if (hasCheckIn || hasCheckOut) {
                        const attendanceBlock = document.createElement('div');
                        attendanceBlock.className = 'greeco-attendance-block d-flex flex-column align-items-center justify-content-center p-1 rounded mb-1 text-center fw-bold shadow-sm';
                        attendanceBlock.style.cssText = 'background: #f97316; color: #ffffff; line-height: 1.2; font-size: 13px; width: 100%; border-radius: 4px;';

                        const inEl = document.createElement('div');
                        inEl.style.cssText = 'font-weight: 700; letter-spacing: 0.5px;';
                        inEl.innerText = props.check_in_time || '08:00';
                        attendanceBlock.appendChild(inEl);

                        const outEl = document.createElement('div');
                        outEl.style.cssText = 'font-weight: 700; letter-spacing: 0.5px; border-top: 1px solid rgba(255,255,255,0.2); width: 100%; margin-top: 1px; padding-top: 1px;';
                        outEl.innerText = props.check_out_time || '17:00';
                        attendanceBlock.appendChild(outEl);

                        card.appendChild(attendanceBlock);
                    }

                    // Warning Badges for Late (after 08:00) & Early (before 17:00)
                    if (props.late_minutes > 0 || props.early_minutes > 0) {
                        const badgeContainer = document.createElement('div');
                        badgeContainer.className = 'd-flex flex-wrap gap-1 mb-1';

                        if (props.late_minutes > 0) {
                            const lateBadge = document.createElement('span');
                            lateBadge.className = 'badge bg-danger text-white border border-white text-xs px-1';
                            lateBadge.innerText = `Trễ ${props.late_minutes}m`;
                            badgeContainer.appendChild(lateBadge);
                        }

                        if (props.early_minutes > 0) {
                            const earlyBadge = document.createElement('span');
                            earlyBadge.className = 'badge bg-warning text-dark border border-white text-xs px-1';
                            earlyBadge.innerText = `Sớm ${props.early_minutes}m`;
                            badgeContainer.appendChild(earlyBadge);
                        }

                        card.appendChild(badgeContainer);
                    }

                    const contextEl = document.createElement('span');
                    contextEl.className = 'greeco-event-context';

                    const ownerEl = document.createElement('span');
                    ownerEl.className = 'greeco-event-owner';
                    ownerEl.innerText = isNoibo ? (props.creator_name && props.creator_name !== 'N/A' ? `${props.creator_name} (Bảo Châu)` : 'Bảo Châu') : (props.creator_name || namesStr || 'Lịch công tác');
                    contextEl.appendChild(ownerEl);

                    if (timeStr && !hasCheckIn && !hasCheckOut) {
                        const timeEl = document.createElement('span');
                        timeEl.className = 'greeco-event-time';
                        timeEl.innerText = timeStr;
                        contextEl.appendChild(timeEl);
                    }
                    card.appendChild(contextEl);

                    const titleEl = document.createElement('span');
                    titleEl.className = 'greeco-event-title';
                    titleEl.innerText = eventTitle;
                    card.appendChild(titleEl);

                    const participantNames = namesList.filter(name => name && name !== props.creator_name);
                    if (participantNames.length > 0) {
                        const metaEl = document.createElement('span');
                        metaEl.className = 'greeco-event-meta';
                        metaEl.innerText = participantNames.length > 2
                            ? `${participantNames.slice(0, 2).join(', ')} +${participantNames.length - 2}`
                            : participantNames.join(', ');
                        card.appendChild(metaEl);
                    }

                    return { domNodes: [card] };
                },
                events: function (info, successCallback, failureCallback) {
                    wire.getEvents(info.startStr, info.endStr)
                        .then(events => {
                            successCallback(events);
                            updateEventCounts(events);
                        })
                        .catch(err => {
                            console.error('Error fetching calendar events:', err);
                            failureCallback(err);
                        });
                },
                dateClick: function (info) {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const cellDate = new Date(info.date);
                    cellDate.setHours(0, 0, 0, 0);

                    if (cellDate < today) {
                        wire.showDaySchedules(info.dateStr);
                        return;
                    }

                    @if(auth()->user()?->hasPermissionTo(\App\Enums\PermissionEnum::ScheduleViewPrivate->value))
                        wire.showDaySchedules(info.dateStr);
                    @else
                        @can('create', App\Models\DutySchedule::class)
                            wire.openCreate(info.dateStr);
                        @else
                            wire.showDaySchedules(info.dateStr);
                        @endcan
                    @endif
                },
                eventClick: function (info) {
                    const event = info.event;

                    if (event.start) {
                        const year = event.start.getFullYear();
                        const month = String(event.start.getMonth() + 1).padStart(2, '0');
                        const day = String(event.start.getDate()).padStart(2, '0');
                        wire.showDaySchedules(`${year}-${month}-${day}`);
                    }
                }
            });

            calendarInstance.render();

            const monthInput = document.getElementById('filterMonth');
            if (monthInput) {
                monthInput.onchange = function () {
                    if (calendarInstance && this.value) {
                        calendarInstance.gotoDate(this.value + '-01');
                    }
                };
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            initCalendar();
        });

        document.addEventListener('livewire:navigated', function () {
            initCalendar();
        });

        window.addEventListener('schedule:open-create', () => {
            showBootstrapModal('modalAddEvent');
        });

        window.addEventListener('schedule:open-edit', () => {
            showBootstrapModal('modalAddEvent');
        });

        window.addEventListener('schedule:open-day-schedules', () => {
            showBootstrapModal('daySchedulesModal');
        });

        window.addEventListener('schedule:close-day-schedules', () => {
            hideBootstrapModal('daySchedulesModal');
        });

        window.addEventListener('schedule:saved', () => {
            hideBootstrapModal('modalAddEvent');
            if (calendarInstance) {
                calendarInstance.refetchEvents();
            }
        });

        window.addEventListener('schedule:deleted', () => {
            if (calendarInstance) {
                calendarInstance.refetchEvents();
            }
        });

        window.addEventListener('schedule:filter-changed', () => {
            if (calendarInstance) {
                calendarInstance.refetchEvents();
            }
        });
    </script>
@endpush
