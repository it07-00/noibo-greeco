<div class="sales-page py-4">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/sales.css') }}?v=1.0.0">
    @endpush

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="text-uppercase text-success fw-semibold sales-eyebrow">Đào tạo</div>
            <h1 class="h3 mb-1">Quản lý khóa học</h1>
            <p class="sales-supporting-text mb-0">Theo dõi khóa học và danh sách học viên tham gia từng khóa.</p>
        </div>

        @can('create', \App\Models\Course::class)
            <button type="button" class="btn btn-success sales-primary-action" wire:click="openCreate">
                <i class="fi fi-rr-plus me-2" aria-hidden="true"></i>
                Thêm khóa học
            </button>
        @endcan
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body border-bottom">
            <label class="form-label visually-hidden" for="courseSearch">Tìm kiếm khóa học</label>
            <div class="input-group sales-search">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fi fi-rr-search sales-supporting-text" aria-hidden="true"></i>
                </span>
                <input
                    id="courseSearch"
                    type="search"
                    class="form-control border-start-0 ps-0"
                    placeholder="Tìm theo tên, mã, địa điểm hoặc tên học viên..."
                    wire:model.live.debounce.350ms="search"
                >
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 sales-table">
                <thead>
                    <tr>
                        <th>Khóa học</th>
                        <th class="d-none d-lg-table-cell">Thời gian / địa điểm</th>
                        <th>Học viên tham gia</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($courses as $course)
                        <tr wire:key="course-{{ $course->id }}">
                            <td>
                                <div class="fw-semibold text-body">{{ $course->name }}</div>
                                <div class="small sales-supporting-text">{{ $course->code ?: 'Chưa có mã khóa học' }}</div>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <div>
                                    @if ($course->starts_at)
                                        {{ $course->starts_at->format('d/m/Y') }}
                                        @if ($course->ends_at) — {{ $course->ends_at->format('d/m/Y') }} @endif
                                    @else
                                        Chưa xếp lịch
                                    @endif
                                </div>
                                <div class="small sales-supporting-text">{{ $course->location ?: 'Chưa có địa điểm' }}</div>
                            </td>
                            <td style="min-width: 260px;">
                                @forelse ($course->students as $student)
                                    <span class="badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle me-1 mb-1">
                                        {{ $student->name }}
                                    </span>
                                @empty
                                    <span class="small sales-supporting-text">Chưa có học viên</span>
                                @endforelse
                            </td>
                            <td class="text-center">
                                <span class="sales-count-pill">{{ $course->students_count }}</span>
                            </td>
                            <td class="text-end text-nowrap">
                                @can('update', $course)
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary sales-icon-button"
                                        wire:click="openEdit({{ $course->id }})"
                                        aria-label="Chỉnh sửa {{ $course->name }}"
                                        title="Chỉnh sửa"
                                    >
                                        <i class="fi fi-rr-edit" aria-hidden="true"></i>
                                    </button>
                                @endcan
                                @can('delete', $course)
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger sales-icon-button"
                                        wire:click="delete({{ $course->id }})"
                                        wire:confirm="Bạn có chắc muốn xóa khóa học {{ $course->name }}?"
                                        aria-label="Xóa {{ $course->name }}"
                                        title="Xóa"
                                    >
                                        <i class="fi fi-rr-trash" aria-hidden="true"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fi fi-rr-graduation-cap d-block fs-2 sales-supporting-text mb-2" aria-hidden="true"></i>
                                <div class="fw-semibold">Chưa có khóa học phù hợp</div>
                                <div class="small sales-supporting-text">Thử đổi từ khóa hoặc thêm khóa học mới.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($courses->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $courses->links() }}
            </div>
        @endif
    </div>

    <div wire:ignore.self class="modal fade" id="courseFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <form class="modal-content" wire:submit.prevent="save">
                <div class="modal-header">
                    <div>
                        <h2 class="h5 modal-title mb-1">{{ $editingId > 0 ? 'Cập nhật khóa học' : 'Thêm khóa học' }}</h2>
                        <div class="small sales-supporting-text">Có thể chọn nhiều học viên; mỗi học viên cũng có thể tham gia nhiều khóa.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="courseCode" class="form-label">Mã khóa học</label>
                            <input id="courseCode" class="form-control @error('code') is-invalid @enderror" wire:model="code" placeholder="VD: ESG-0726">
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="courseName" class="form-label">Tên khóa học <span class="text-danger">*</span></label>
                            <input id="courseName" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="courseStartsAt" class="form-label">Ngày khai giảng</label>
                            <input id="courseStartsAt" type="date" class="form-control @error('startsAt') is-invalid @enderror" wire:model="startsAt">
                            @error('startsAt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="courseEndsAt" class="form-label">Ngày kết thúc</label>
                            <input id="courseEndsAt" type="date" class="form-control @error('endsAt') is-invalid @enderror" wire:model="endsAt">
                            @error('endsAt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="courseLocation" class="form-label">Địa điểm</label>
                            <input id="courseLocation" class="form-control @error('location') is-invalid @enderror" wire:model="location">
                            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label for="courseStudents" class="form-label">Học viên tham gia</label>
                            <select
                                id="courseStudents"
                                class="form-select @error('selectedStudentIds') is-invalid @enderror @error('selectedStudentIds.*') is-invalid @enderror"
                                wire:model="selectedStudentIds"
                                multiple
                                size="8"
                            >
                                @foreach ($individualCustomers as $customer)
                                    <option value="{{ $customer->id }}">
                                        {{ $customer->name }}{{ $customer->phone ? ' — '.$customer->phone : ($customer->email ? ' — '.$customer->email : '') }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Giữ Ctrl (Windows) hoặc Command (macOS) để chọn nhiều học viên.
                                @if ($individualCustomers->isEmpty())
                                    Chưa có khách hàng cá nhân; hãy thêm tại mục Khách hàng trước.
                                @endif
                            </div>
                            @error('selectedStudentIds') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @error('selectedStudentIds.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label for="courseDescription" class="form-label">Mô tả / nội dung</label>
                            <textarea id="courseDescription" rows="3" class="form-control @error('description') is-invalid @enderror" wire:model="description"></textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save">Lưu khóa học</span>
                        <span wire:loading wire:target="save">Đang lưu...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.addEventListener('course-form:show', () => window.GreecoModal?.show('courseFormModal'));
        window.addEventListener('course-form:hide', () => window.GreecoModal?.hide('courseFormModal'));
    </script>
</div>
