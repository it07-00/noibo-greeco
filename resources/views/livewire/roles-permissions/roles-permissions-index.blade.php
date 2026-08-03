<div>
    <!-- Page Header -->
    <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between mb-4">
        <div class="clearfix">
            <h1 class="app-page-title mb-1">
                <i class="fi fi-rr-shield-check text-primary me-2"></i>Vai trò &amp; Quyền hạn
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Vai trò &amp; Quyền hạn
                    </li>
                </ol>
            </nav>
        </div>
        <div>
            <button
                type="button"
                class="btn btn-primary waves-effect waves-light d-inline-flex align-items-center gap-2 shadow-sm"
                data-bs-toggle="modal"
                data-bs-target="#addRoleModal"
            >
                <i class="fi fi-rr-plus"></i> Thêm Vai trò Mới
            </button>
        </div>
    </div>

    @if ($activeRole)
        @php
            $percentAssigned = $totalPermissionsCount > 0 ? round(($activeRolePermissionCount / $totalPermissionsCount) * 100) : 0;
        @endphp
        <!-- KPI Metrics Header -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="avatar avatar-md bg-primary-subtle text-primary rounded-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center">
                            <i class="fi fi-rr-users-alt fs-5"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Tổng số vai trò</span>
                            <h4 class="mb-0 fw-bold text-dark">{{ $roles->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="avatar avatar-md bg-info-subtle text-info rounded-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center">
                            <i class="fi fi-rr-user-gear fs-5"></i>
                        </div>
                        <div class="overflow-hidden">
                            <span class="text-muted small d-block">Vai trò đang chọn</span>
                            <h6 class="mb-0 fw-bold text-dark text-truncate" title="{{ $activeRole->name }}">{{ $activeRole->name }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="text-muted small">Quyền đã cấp</span>
                            <span class="badge bg-success-subtle text-success fw-semibold">{{ $percentAssigned }}%</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h4 class="mb-0 fw-bold text-dark">{{ $activeRolePermissionCount }}</h4>
                            <span class="text-muted small">/ {{ $totalPermissionsCount }}</span>
                        </div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentAssigned }}%" aria-valuenow="{{ $percentAssigned }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="avatar avatar-md bg-warning-subtle text-warning rounded-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center">
                            <i class="fi fi-rr-badge-check fs-5"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Phân loại vai trò</span>
                            <span class="badge {{ \App\Enums\RoleEnum::isSystemRole($activeRole->name) ? 'bg-primary' : 'bg-secondary' }} px-2 py-1">
                                {{ \App\Enums\RoleEnum::isSystemRole($activeRole->name) ? 'Vai trò Hệ thống' : 'Vai trò Tùy chỉnh' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content Grid -->
    <div class="row g-4">
        <!-- Sidebar: Roles Navigation -->
        <div class="col-lg-3 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fi fi-rr-list-check text-primary"></i> Danh sách Vai trò
                    </h6>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2">{{ $roles->count() }}</span>
                </div>
                <div class="card-body p-2">
                    <div class="list-group list-group-flush gap-1">
                        @foreach ($roles as $role)
                            @php
                                $isSuperAdmin = $role->name === \App\Enums\RoleEnum::SuperAdmin->value;
                                $isSystem = \App\Enums\RoleEnum::isSystemRole($role->name);
                                $isActive = $activeRoleId === $role->id;
                                $permCount = $isSuperAdmin ? $totalPermissionsCount : $role->permissions->count();
                            @endphp
                            <div
                                wire:click="selectRole({{ $role->id }})"
                                wire:key="role-item-{{ $role->id }}"
                                class="list-group-item list-group-item-action border-0 rounded-3 p-2.5 d-flex align-items-center justify-content-between cursor-pointer transition-all {{ $isActive ? 'bg-primary text-white shadow-sm fw-semibold' : 'hover-bg-light text-dark' }}"
                                style="cursor: pointer;"
                            >
                                <div class="d-flex align-items-center gap-2.5 min-w-0">
                                    <div class="flex-shrink-0">
                                        @if ($isSuperAdmin)
                                            <i class="fi fi-rr-star fs-6 {{ $isActive ? 'text-warning' : 'text-warning' }}"></i>
                                        @elseif ($isSystem)
                                            <i class="fi fi-rr-shield-check fs-6 {{ $isActive ? 'text-white' : 'text-primary' }}"></i>
                                        @else
                                            <i class="fi fi-rr-user fs-6 {{ $isActive ? 'text-white' : 'text-muted' }}"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-truncate {{ $isActive ? 'text-white' : 'text-dark' }}" style="font-size: 0.9rem;">
                                            {{ $role->name }}
                                        </div>
                                        @if ($role->department)
                                            <small class="{{ $isActive ? 'text-white-50' : 'text-muted' }} d-block text-truncate" style="font-size: 0.75rem;">
                                                <i class="fi fi-rr-building me-1"></i>{{ $role->department->name }}
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-1 ms-2">
                                    <span class="badge {{ $isActive ? 'bg-white text-primary' : 'bg-light text-dark' }} rounded-pill" style="font-size: 0.7rem;">
                                        {{ $permCount }}
                                    </span>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-icon border-0 {{ $isActive ? 'text-white-50 hover-text-white' : 'text-muted hover-text-primary' }} p-1"
                                        title="Chỉnh sửa vai trò"
                                        wire:click.stop="openEditModal({{ $role->id }})"
                                    >
                                        <i class="fi fi-rr-edit" style="font-size: 0.8rem;"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content: Active Role Permissions Matrix -->
        <div class="col-lg-9 col-md-8">
            <div class="card border-0 shadow-sm">
                @if ($activeRole)
                    <!-- Active Role Info Banner -->
                    <div class="card-header bg-transparent border-bottom py-3">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h5 class="card-title text-dark mb-0 fw-bold">{{ $activeRole->name }}</h5>
                                    @if (\App\Enums\RoleEnum::isSystemRole($activeRole->name))
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Hệ thống</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border">Tùy chỉnh</span>
                                    @endif
                                    @if ($activeRole->department)
                                        <span class="badge bg-info-subtle text-info border border-info-subtle">
                                            <i class="fi fi-rr-building me-1"></i>{{ $activeRole->department->name }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-muted mb-0 small">
                                    <i class="fi fi-rr-info text-primary me-1"></i>
                                    {{ $activeRole->description ?? 'Chưa có mô tả chi tiết cho vai trò này.' }}
                                </p>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary waves-effect d-inline-flex align-items-center gap-1"
                                    wire:click="openEditModal({{ $activeRole->id }})"
                                >
                                    <i class="fi fi-rr-edit"></i> Chỉnh sửa
                                </button>

                                @if (! \App\Enums\RoleEnum::isSystemRole($activeRole->name))
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger waves-effect d-inline-flex align-items-center gap-1"
                                        wire:click="deleteRole({{ $activeRole->id }})"
                                        wire:confirm="Bạn có chắc chắn muốn xóa vai trò '{{ $activeRole->name }}' không? Thao tác này không thể hoàn tác!"
                                    >
                                        <i class="fi fi-rr-trash"></i> Xóa vai trò
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Toolbar & Permission Search -->
                    <div class="card-body pb-0">
                        <div class="row align-items-center g-3 mb-3">
                            <div class="col-md-6 col-lg-5">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="fi fi-rr-search"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="form-control border-start-0 bg-light"
                                        placeholder="Tìm kiếm quyền hạn..."
                                        wire:model.live.debounce.250ms="permissionSearch"
                                    />
                                    @if ($permissionSearch !== '')
                                        <button
                                            type="button"
                                            class="btn btn-light border border-start-0 text-muted"
                                            wire:click="$set('permissionSearch', '')"
                                            title="Xóa tìm kiếm"
                                        >
                                            <i class="fi fi-rr-cross-small"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-7 text-md-end">
                                @if ($activeRole->name === \App\Enums\RoleEnum::SuperAdmin->value)
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle py-2 px-3">
                                        <i class="fi fi-rr-lock text-warning me-1"></i> Super Admin tự động có toàn bộ quyền hạn trong hệ thống
                                    </span>
                                @else
                                    <span class="text-muted small me-2">
                                        <i class="fi fi-rr-bulb text-warning me-1"></i> Nhấp vào công tắc để bật/tắt quyền hạn tức thì
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Permissions Matrix Grid -->
                    <div class="card-body pt-2">
                        @if (empty($permissionsGrouped))
                            <div class="text-center py-5 my-3">
                                <div class="avatar avatar-xl bg-light rounded-circle text-muted mx-auto mb-3 d-flex align-items-center justify-content-center">
                                    <i class="fi fi-rr-search fs-3"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Không tìm thấy quyền hạn phù hợp</h6>
                                <p class="text-muted small">Không tìm thấy kết quả nào khớp với từ khóa "<strong>{{ $permissionSearch }}</strong>"</p>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" wire:click="$set('permissionSearch', '')">
                                    Xóa bộ lọc tìm kiếm
                                </button>
                            </div>
                        @else
                            <div class="row g-3">
                                @foreach ($permissionsGrouped as $groupName => $permissions)
                                    @php
                                        $groupKeys = array_keys($permissions);
                                        $enabledCount = 0;
                                        foreach ($groupKeys as $k) {
                                            if (in_array($k, $activeRolePermissions, true) || $activeRole->name === \App\Enums\RoleEnum::SuperAdmin->value) {
                                                $enabledCount++;
                                            }
                                        }
                                        $allEnabledInGroup = count($groupKeys) > 0 && $enabledCount === count($groupKeys);
                                    @endphp

                                    <div class="col-md-6 col-xl-4" wire:key="group-card-{{ Str::slug($groupName) }}">
                                        <div class="card h-100 border rounded-3 bg-body-tertiary bg-opacity-50 hover-shadow transition-all">
                                            <div class="card-header bg-transparent py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="fi fi-rr-shield-check text-primary"></i>
                                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.92rem;">{{ $groupName }}</h6>
                                                </div>

                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge {{ $enabledCount > 0 ? 'bg-primary-subtle text-primary' : 'bg-light text-muted' }} rounded-pill" style="font-size: 0.72rem;">
                                                        {{ $enabledCount }}/{{ count($permissions) }}
                                                    </span>

                                                    @if ($activeRole->name !== \App\Enums\RoleEnum::SuperAdmin->value)
                                                        <div class="dropdown">
                                                            <button class="btn btn-xs btn-icon btn-light border-0 text-muted p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Thao tác nhanh">
                                                                <i class="fi fi-rr-menu-dots-vertical" style="font-size: 0.75rem;"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end text-sm">
                                                                <li>
                                                                    <button
                                                                        type="button"
                                                                        class="dropdown-item d-flex align-items-center gap-2 text-primary"
                                                                        wire:click="toggleGroupPermissions({{ json_encode($groupKeys) }}, true)"
                                                                    >
                                                                        <i class="fi fi-rr-check-double"></i> Bật tất cả trong nhóm
                                                                    </button>
                                                                </li>
                                                                <li>
                                                                    <button
                                                                        type="button"
                                                                        class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                                                        wire:click="toggleGroupPermissions({{ json_encode($groupKeys) }}, false)"
                                                                    >
                                                                        <i class="fi fi-rr-cross-circle"></i> Tắt tất cả trong nhóm
                                                                    </button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="card-body p-3">
                                                <div class="d-flex flex-column gap-2.5">
                                                    @foreach ($permissions as $permValue => $permLabel)
                                                        @php
                                                            $isChecked = in_array($permValue, $activeRolePermissions, true) || $activeRole->name === \App\Enums\RoleEnum::SuperAdmin->value;
                                                        @endphp
                                                        <div
                                                            class="form-check form-switch p-2 rounded-2 transition-all {{ $isChecked ? 'bg-primary-subtle bg-opacity-25' : 'hover-bg-light' }}"
                                                            wire:key="perm-item-{{ $permValue }}"
                                                        >
                                                            <input
                                                                class="form-check-input ms-0 me-2.5"
                                                                type="checkbox"
                                                                id="perm_{{ Str::slug($permValue) }}"
                                                                wire:click="togglePermission('{{ $permValue }}')"
                                                                @if ($isChecked) checked @endif
                                                                @if ($activeRole->name === \App\Enums\RoleEnum::SuperAdmin->value) disabled @endif
                                                                style="cursor: pointer;"
                                                            />
                                                            <label
                                                                class="form-check-label text-dark fw-medium cursor-pointer user-select-none d-block ms-1"
                                                                for="perm_{{ Str::slug($permValue) }}"
                                                                style="font-size: 0.85rem;"
                                                            >
                                                                {{ $permLabel }}
                                                                <small class="text-muted d-block font-monospace" style="font-size: 0.7rem;">{{ $permValue }}</small>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <i class="fi fi-rr-info scale-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">Không tìm thấy vai trò nào. Hãy thêm một vai trò mới để bắt đầu.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal: Add Role -->
    <div wire:ignore.self class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form wire:submit.prevent="createRole" class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2">
                        <i class="fi fi-rr-plus-circle"></i> Thêm Vai trò Mới
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" for="newRoleName">Tên vai trò <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            id="newRoleName"
                            wire:model.defer="newRoleName"
                            class="form-control @error('newRoleName') is-invalid @enderror"
                            placeholder="Ví dụ: Kỹ thuật viên, Trưởng nhóm sales,..."
                            required
                        />
                        @error('newRoleName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" for="newRoleDepartmentId">Phòng ban trực thuộc</label>
                        <select
                            id="newRoleDepartmentId"
                            wire:model.defer="newRoleDepartmentId"
                            class="form-select @error('newRoleDepartmentId') is-invalid @enderror"
                        >
                            <option value="">-- Không trực thuộc phòng ban nào --</option>
                            @foreach ($availableDepartments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                            @endforeach
                        </select>
                        @error('newRoleDepartmentId')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" for="newRoleDescription">Mô tả chi tiết</label>
                        <textarea
                            id="newRoleDescription"
                            wire:model.defer="newRoleDescription"
                            class="form-control @error('newRoleDescription') is-invalid @enderror"
                            rows="3"
                            placeholder="Mô tả nhiệm vụ và phạm vi công việc của vai trò này"
                        ></textarea>
                        @error('newRoleDescription')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer bg-light py-2.5">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm">
                        <i class="fi fi-rr-check"></i> Thêm Vai trò
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Role -->
    <div wire:ignore.self class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form wire:submit.prevent="updateRole" class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2">
                        <i class="fi fi-rr-edit"></i> Chỉnh sửa Vai trò
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" for="editRoleName">Tên vai trò <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            id="editRoleName"
                            wire:model.defer="editRoleName"
                            class="form-control @error('editRoleName') is-invalid @enderror"
                            placeholder="Tên vai trò"
                            required
                            @if ($editingRoleId && \App\Enums\RoleEnum::isSystemRole($editRoleName)) disabled @endif
                        />
                        @if ($editingRoleId && \App\Enums\RoleEnum::isSystemRole($editRoleName))
                            <small class="text-warning d-block mt-1">
                                <i class="fi fi-rr-info me-1"></i> Tên vai trò hệ thống cố định không thể thay đổi.
                            </small>
                        @endif
                        @error('editRoleName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" for="editRoleDepartmentId">Phòng ban trực thuộc</label>
                        <select
                            id="editRoleDepartmentId"
                            wire:model.defer="editRoleDepartmentId"
                            class="form-select @error('editRoleDepartmentId') is-invalid @enderror"
                        >
                            <option value="">-- Không trực thuộc phòng ban nào --</option>
                            @foreach ($availableDepartments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                            @endforeach
                        </select>
                        @error('editRoleDepartmentId')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" for="editRoleDescription">Mô tả chi tiết</label>
                        <textarea
                            id="editRoleDescription"
                            wire:model.defer="editRoleDescription"
                            class="form-control @error('editRoleDescription') is-invalid @enderror"
                            rows="3"
                            placeholder="Mô tả nhiệm vụ và phạm vi công việc của vai trò này"
                        ></textarea>
                        @error('editRoleDescription')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer bg-light py-2.5">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm">
                        <i class="fi fi-rr-check"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.addEventListener('role-create:hide', () => {
            const modalEl = document.getElementById('addRoleModal');
            if (modalEl) {
                bootstrap.Modal.getInstance(modalEl)?.hide();
            }
        });

        window.addEventListener('role-edit:show', () => {
            const modalEl = document.getElementById('editRoleModal');
            if (modalEl) {
                let modal = bootstrap.Modal.getInstance(modalEl);
                if (!modal) modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        });

        window.addEventListener('role-edit:hide', () => {
            const modalEl = document.getElementById('editRoleModal');
            if (modalEl) {
                bootstrap.Modal.getInstance(modalEl)?.hide();
            }
        });
    });
</script>
@endpush
