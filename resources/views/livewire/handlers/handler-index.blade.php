<div>
    <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between">
        <div>
            <h1 class="app-page-title">Nhà thầu phụ</h1>
            <p class="text-muted mb-0">{{ number_format($totalHandlers) }} nhà thầu phụ đang được quản lý.</p>
        </div>
        @can('handlers.create')
            <button type="button" class="btn btn-primary" wire:click="openCreate"><i class="fi fi-rr-plus me-1"></i> Thêm nhà thầu phụ</button>
        @endcan
    </div>
    <div class="card mt-3">
        <div class="card-header">
            <label class="visually-hidden" for="handlerSearch">Tìm nhà thầu phụ</label>
            <input id="handlerSearch" type="search" class="form-control" style="max-width: 440px" wire:model.live.debounce.300ms="search" placeholder="Tìm theo tên, số điện thoại hoặc địa chỉ...">
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Nhà thầu phụ</th><th>Số điện thoại</th><th class="d-none d-md-table-cell">Địa chỉ</th><th>Hợp đồng</th><th class="text-end">Thao tác</th></tr></thead>
                    <tbody>
                        @forelse ($handlers as $handler)
                            <tr wire:key="handler-{{ $handler->id }}">
                                <td>
                                    @can('contract.view')
                                        <a class="fw-semibold" href="{{ route('handlers.contracts', $handler) }}">{{ $handler->name }}</a>
                                    @else
                                        <span class="fw-semibold">{{ $handler->name }}</span>
                                    @endcan
                                    <div class="small text-muted d-md-none">{{ $handler->address }}</div>
                                </td>
                                <td>{{ $handler->phone ?: '—' }}</td>
                                <td class="d-none d-md-table-cell">{{ $handler->address ?: '—' }}</td>
                                <td>
                                    @can('contract.view')
                                        <a href="{{ route('handlers.contracts', $handler) }}" class="badge bg-primary-subtle text-primary">{{ $handler->contracts_count }} HĐ</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endcan
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        @can('handlers.edit')
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $handler->id }})">Sửa</button>
                                        @endcan
                                        @can('handlers.delete')
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $handler->id }})" wire:confirm="Xác nhận xóa nhà thầu phụ này?" @disabled($handler->contracts_count > 0)>Xóa</button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-5">Không tìm thấy nhà thầu phụ.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">{{ $handlers->links() }}</div>
    </div>
    <div wire:ignore.self class="modal fade" id="handlerFormModal" tabindex="-1" aria-labelledby="handlerFormLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form class="modal-content" wire:submit="save">
                <div class="modal-header">
                    <h2 class="h5 modal-title" id="handlerFormLabel">{{ $editingId ? 'Cập nhật nhà thầu phụ' : 'Thêm nhà thầu phụ' }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="handlerName" class="form-label">Tên nhà thầu phụ <span class="text-danger">*</span></label>
                        <input id="handlerName" class="form-control @error('name') is-invalid @enderror" wire:model="name" maxlength="255">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="handlerPhone" class="form-label">Số điện thoại</label>
                        <input id="handlerPhone" type="tel" class="form-control @error('phone') is-invalid @enderror" wire:model="phone" maxlength="30">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="handlerAddress" class="form-label">Địa chỉ</label>
                        <textarea id="handlerAddress" class="form-control @error('address') is-invalid @enderror" wire:model="address" rows="3" maxlength="2000"></textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Lưu nhà thầu phụ</button>
                </div>
            </form>
        </div>
    </div>
</div>
