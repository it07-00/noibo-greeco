<div class="sales-page py-4">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/sales.css') }}?v=1.0.0">
    @endpush

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="text-uppercase text-success fw-semibold sales-eyebrow">Đào tạo</div>
            <h1 class="h3 mb-1">Quản lý khóa học</h1>
            <p class="sales-supporting-text mb-0">Theo dõi 35+ khóa học đào tạo tập trung và danh sách học viên tham gia từng khóa.</p>
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
                    placeholder="Tìm theo tên khóa học, mã, giảng viên, tiêu chuẩn (ISO, ESG, GHG...)..."
                    wire:model.live.debounce.350ms="search"
                >
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 sales-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Mã</th>
                        <th>Tên khóa học & Giảng viên</th>
                        <th>Thời lượng & Học phí</th>
                        <th>Học viên đăng ký</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($courses as $course)
                        <tr wire:key="course-{{ $course->id }}">
                            <td>
                                <span class="badge bg-light text-dark border fw-bold fs-7">{{ $course->code ?: 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $course->name }}</div>
                                <div class="small text-muted mt-1">
                                    <i class="fi fi-rr-user-chalkboard me-1"></i>Giảng viên/Đơn vị: <strong>{{ $course->instructor ?: 'GREECO' }}</strong>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-success">
                                    {{ $course->fee > 0 ? number_format((float)$course->fee).' VNĐ' : 'Chưa cập nhật học phí' }}
                                </div>
                                <div class="small text-muted">
                                    <i class="fi fi-rr-clock me-1"></i>{{ $course->duration ?: '2 ngày' }}
                                </div>
                            </td>
                            <td style="min-width: 200px;">
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
                            <td colspan="6" class="text-center py-5">
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

    {{-- ── Form Modal Create / Edit Course ──────────────────────────────────── --}}
    <div wire:ignore.self class="modal fade" id="courseFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <form class="modal-content" wire:submit.prevent="save">
                <div class="modal-header">
                    <div>
                        <h2 class="h5 modal-title mb-1">{{ $editingId > 0 ? 'Cập nhật thông tin khóa học' : 'Thêm mới khóa học' }}</h2>
                        <div class="small sales-supporting-text">Thiết lập chương trình đào tạo, học phí, giảng viên và danh sách học viên.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="courseCode" class="form-label fw-semibold">Mã khóa học</label>
                            <input id="courseCode" class="form-control @error('code') is-invalid @enderror" wire:model="code" placeholder="VD: KHC-01">
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="courseName" class="form-label fw-semibold">Tên khóa học <span class="text-danger">*</span></label>
                            <input id="courseName" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Nhập tên khóa học đào tạo...">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="courseInstructor" class="form-label fw-semibold">Giảng viên / Đơn vị đào tạo</label>
                            <input id="courseInstructor" class="form-control @error('instructor') is-invalid @enderror" wire:model="instructor" placeholder="VD: Thầy Nhã, HUY THANH...">
                            @error('instructor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="courseDuration" class="form-label fw-semibold">Thời lượng đào tạo</label>
                            <input id="courseDuration" class="form-control @error('duration') is-invalid @enderror" wire:model="duration" placeholder="VD: 2 ngày, 3 ngày...">
                            @error('duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="courseFee" class="form-label fw-semibold">Học phí (VNĐ/học viên)</label>
                            <input id="courseFee" type="number" class="form-control @error('fee') is-invalid @enderror" wire:model="fee" placeholder="VD: 1860000">
                            @error('fee') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="courseStartsAt" class="form-label fw-semibold">Ngày khai giảng dự kiến</label>
                            <input id="courseStartsAt" type="date" class="form-control @error('startsAt') is-invalid @enderror" wire:model="startsAt">
                            @error('startsAt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="courseEndsAt" class="form-label fw-semibold">Ngày bế giảng dự kiến</label>
                            <input id="courseEndsAt" type="date" class="form-control @error('endsAt') is-invalid @enderror" wire:model="endsAt">
                            @error('endsAt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="courseLocation" class="form-label fw-semibold">Địa điểm tổ chức</label>
                            <input id="courseLocation" class="form-control @error('location') is-invalid @enderror" wire:model="location" placeholder="VD: Trung tâm GREECO...">
                            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label for="courseAudience" class="form-label fw-semibold">Đối tượng tham gia</label>
                            <textarea id="courseAudience" rows="2" class="form-control @error('audience') is-invalid @enderror" wire:model="audience" placeholder="Mô tả nhóm đối tượng phù hợp tham dự..."></textarea>
                            @error('audience') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label for="courseObjectives" class="form-label fw-semibold">Mục tiêu khóa học</label>
                            <textarea id="courseObjectives" rows="2" class="form-control @error('objectives') is-invalid @enderror" wire:model="objectives" placeholder="Kết quả kiến thức và kỹ năng đạt được sau khóa học..."></textarea>
                            @error('objectives') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label for="courseContentDetail" class="form-label fw-semibold">Chương trình / Nội dung chi tiết</label>
                            <textarea id="courseContentDetail" rows="4" class="form-control @error('contentDetail') is-invalid @enderror" wire:model="contentDetail" placeholder="Kịch bản chi tiết các chuyên đề đào tạo..."></textarea>
                            @error('contentDetail') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label for="courseStudents" class="form-label fw-semibold">Học viên tham gia khóa học</label>
                            <select
                                id="courseStudents"
                                class="form-select @error('selectedStudentIds') is-invalid @enderror @error('selectedStudentIds.*') is-invalid @enderror"
                                wire:model="selectedStudentIds"
                                multiple
                                size="6"
                            >
                                @foreach ($individualCustomers as $customer)
                                    <option value="{{ $customer->id }}">
                                        {{ $customer->name }}{{ $customer->phone ? ' — '.$customer->phone : ($customer->email ? ' — '.$customer->email : '') }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Giữ Ctrl (Windows) hoặc Command (macOS) để chọn nhiều học viên.
                            </div>
                            @error('selectedStudentIds') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @error('selectedStudentIds.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
