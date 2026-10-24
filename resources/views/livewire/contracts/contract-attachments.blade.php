<div class="card mb-3">
    <div class="card-header"><h2 class="h6 mb-0">File hợp đồng và hồ sơ bổ sung</h2></div>
    <div class="card-body">
        @if ($canManage)
            <form wire:submit="upload" class="mb-3">
                <label for="contractAttachments-{{ $contractId }}" class="form-label">Đính kèm hồ sơ</label>
                <input id="contractAttachments-{{ $contractId }}" type="file" multiple class="form-control" wire:model="files" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                <div class="form-text">Tối đa 10 file/lần, mỗi file 200MB. PDF, Word, Excel, JPG, PNG.</div>
                @error('uploadFiles')<div class="text-danger">{{ $message }}</div>@enderror
                @error('uploadFiles.*')<div class="text-danger">{{ $message }}</div>@enderror
                @error('files')<div class="text-danger">{{ $message }}</div>@enderror
                <button type="submit" class="btn btn-primary mt-2" wire:loading.attr="disabled" wire:target="files,upload">Tải hồ sơ lên</button>
                <span class="small text-muted" wire:loading wire:target="files,upload">Đang tải...</span>
            </form>
        @endif
        @forelse ($attachments as $file)
            <div class="d-flex flex-wrap justify-content-between gap-2 border-bottom py-2" wire:key="contract-attachment-{{ $file->id }}">
                <div>
                    <a href="{{ route('contracts.milestone-files.download', [$contract, $file]) }}">{{ $file->original_name }}</a>
                    <div class="small text-muted">{{ $file->uploader?->name }} · {{ $file->created_at?->format('d/m/Y H:i') }}</div>
                </div>
                @if ($canManage)<button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteFile({{ $file->id }})" wire:confirm="Xóa file hồ sơ này?">Xóa file</button>@endif
            </div>
        @empty
            <p class="text-muted mb-0">Chưa có file hợp đồng đính kèm.</p>
        @endforelse
    </div>
</div>
