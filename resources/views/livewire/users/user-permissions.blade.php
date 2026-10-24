<div>
    <div wire:ignore.self class="modal fade" id="userPermissionsModal" tabindex="-1" aria-labelledby="userPermissionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" wire:submit="save">
                <div class="modal-header">
                    <h5 class="modal-title" id="userPermissionsModalLabel">Quyền riêng của người dùng</h5>
                    <button type="button" class="btn-close" wire:click="close" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    @if ($selectedUser)
                        <div class="fw-semibold">{{ $selectedUser->name }} <span class="text-muted">({{ $selectedUser->username }})</span></div>
                        <div class="text-muted small mb-3">Vai trò: {{ $selectedUser->roles->pluck('name')->join(', ') ?: 'Chưa có vai trò' }}</div>
                        <div class="alert alert-info">
                            Chọn các quyền cấp bổ sung cho riêng người này. Bỏ chọn để thu hồi quyền cấp riêng.
                            Quyền kế thừa từ vai trò vẫn có hiệu lực. Các quyền truy cập cần thiết sẽ được bổ sung khi lưu.
                        </div>
                        @if ($isSuperAdmin)
                            <div class="alert alert-warning">Super Admin luôn có toàn bộ quyền; không thể chỉnh sửa quyền riêng.</div>
                        @endif
                        <label for="user-permission-search" class="form-label">Tìm quyền</label>
                        <input id="user-permission-search" type="search" class="form-control mb-3" wire:model.live.debounce.300ms="search" placeholder="Tên nhóm, tên quyền hoặc mã quyền...">
                        @error('permissions') <div class="text-danger mb-2">{{ $message }}</div> @enderror
                        @error('permissions.*') <div class="text-danger mb-2">{{ $message }}</div> @enderror
                        <div class="row g-3">
                            @forelse ($permissionGroups as $group => $items)
                                <div class="col-12 col-lg-6" wire:key="user-permission-group-{{ md5($group) }}">
                                    <div class="card h-100">
                                        <div class="card-header fw-semibold">{{ $group }}</div>
                                        <div class="card-body">
                                            @foreach ($items as $permission => $label)
                                                <div class="form-check mb-2" wire:key="user-permission-{{ $permission }}">
                                                    <input class="form-check-input" type="checkbox" id="user-permission-{{ $permission }}" value="{{ $permission }}" wire:model="permissions" @disabled($isSuperAdmin)>
                                                    <label class="form-check-label" for="user-permission-{{ $permission }}">
                                                        {{ $label }}
                                                        @if ($isSuperAdmin || in_array($permission, $inheritedPermissions, true))
                                                            <span class="badge bg-secondary-subtle text-secondary">Từ vai trò</span>
                                                        @endif
                                                        <small class="d-block text-muted">{{ $permission }}</small>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">Không tìm thấy quyền phù hợp.</p>
                            @endforelse
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" wire:click="close">Đóng</button>
                    @if ($selectedUser && ! $isSuperAdmin)
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Lưu quyền riêng</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
