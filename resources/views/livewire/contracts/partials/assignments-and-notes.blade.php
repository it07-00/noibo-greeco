<div class="row g-3 mb-4">
    <div class="col-12 col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center gap-2">
                <h2 class="h6 mb-0">Giao việc và hạn thực hiện</h2>
                @if ($canAssign)<button type="button" class="btn btn-sm btn-outline-primary" wire:click="openAssign">Giao việc</button>@endif
            </div>
            <div class="card-body">
                @forelse ($contract->assignments as $assignment)
                    <div class="border-bottom pb-2 mb-2" wire:key="contract-assignment-{{ $assignment->id }}">
                        <div class="fw-semibold">{{ $assignment->user?->name ?: $assignment->external_assignee }}</div>
                        <div class="small text-muted">Người giao: {{ $assignment->assigner?->name ?: '—' }}</div>
                        <div class="small {{ $assignment->deadline?->isPast() && $contract->workflow_status !== 'finished' ? 'text-danger' : 'text-muted' }}">Hạn: {{ $assignment->deadline?->format('d/m/Y') ?: 'Chưa đặt' }}</div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Chưa có người thực hiện được phân công.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-7">
        <div class="card h-100">
            <div class="card-header"><h2 class="h6 mb-0">Lịch sử trao đổi và ghi chú</h2></div>
            <div class="card-body">
                <form wire:submit="addProgressNote" class="mb-3">
                    <label for="contractProgressNote" class="form-label">Thêm ghi chú</label>
                    <textarea id="contractProgressNote" class="form-control" wire:model="progressNote" rows="3" maxlength="2000"></textarea>
                    @error('progressNote')<div class="text-danger">{{ $message }}</div>@enderror
                    <button type="submit" class="btn btn-primary mt-2" wire:loading.attr="disabled">Lưu ghi chú</button>
                </form>
                @forelse ($contract->progressNotes as $note)
                    <div class="border-start ps-3 mb-3" wire:key="contract-progress-note-{{ $note->id }}">
                        <div class="small text-muted">{{ $note->creator?->name }} · {{ $note->created_at?->format('d/m/Y H:i') }}</div>
                        <div>{!! nl2br(e($note->content)) !!}</div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Chưa có ghi chú.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
