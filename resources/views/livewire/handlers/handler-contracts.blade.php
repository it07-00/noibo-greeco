<div>
    <div class="app-page-head d-flex flex-wrap gap-3 justify-content-between align-items-center">
        <div>
            <h1 class="app-page-title">{{ $handler->name }}</h1>
            <p class="text-muted mb-0">{{ $handler->phone }} {{ $handler->address ? ' · '.$handler->address : '' }}</p>
        </div>
        <a class="btn btn-light" href="{{ route('handlers.index') }}">Danh sách nhà thầu phụ</a>
    </div>
    <div class="card mt-3">
        <div class="card-header">
            <h2 class="h5 mb-3">Hợp đồng liên quan ({{ $contracts->total() }})</h2>
            <label class="visually-hidden" for="handlerContractSearch">Tìm hợp đồng</label>
            <input id="handlerContractSearch" class="form-control" type="search" wire:model.live.debounce.300ms="search" placeholder="Số hợp đồng, tên hợp đồng hoặc khách hàng...">
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>Hợp đồng</th><th>Khách hàng</th><th>Loại</th><th>Ngày ký</th><th>Trạng thái</th></tr></thead>
                <tbody>
                    @forelse ($contracts as $contract)
                        <tr wire:key="handler-contract-{{ $contract->id }}">
                            <td><a class="fw-semibold" href="{{ route('contracts.show', $contract) }}">{{ $contract->contract_number ?: 'Chưa có số HĐ' }}</a><div class="text-muted small">{{ $contract->title }}</div></td>
                            <td>{{ $contract->customer?->name }}</td>
                            <td>{{ $contract->type->label() }}</td>
                            <td>{{ $contract->signed_at?->format('d/m/Y') ?: 'Chưa ký' }}</td>
                            <td><span class="badge {{ $contract->status->badgeClass() }}">{{ $contract->status->label() }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-5">Chưa có hợp đồng phù hợp.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $contracts->links() }}</div>
    </div>
</div>
