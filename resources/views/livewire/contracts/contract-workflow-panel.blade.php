<div class="p-3 p-md-4">
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
        <h2 class="h6 mb-0">Tiến độ xử lý hợp đồng</h2>
        <span class="badge bg-primary-subtle text-primary">{{ count($completedSteps) }}/{{ count($steps) }} bước</span>
    </div>
    <div class="row g-2">
        @foreach ($steps as $key => $label)
            <div class="col-12 col-md-6 col-xl-2" wire:key="workflow-step-{{ $key }}">
                <div class="border rounded p-3 h-100 {{ in_array($key, $completedSteps, true) ? 'border-success bg-success-subtle' : '' }}">
                    <div class="fw-semibold mb-2">{{ $loop->iteration }}. {{ $label }}</div>
                    @if (in_array($key, $completedSteps, true))
                        <span class="badge bg-success">Đã hoàn thành</span>
                        @if ($canSupplement)
                            <button type="button" class="btn btn-sm btn-outline-primary ms-2" wire:click="openFileSupplement('{{ $key }}')">Bổ sung file</button>
                        @endif
                    @elseif ($canEdit && $nextStep === $key)
                        <button type="button" class="btn btn-sm btn-primary" wire:click="openStep('{{ $key }}')">Xác nhận bước</button>
                    @else
                        <span class="small text-muted">Chưa thực hiện</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @error('activeStep') <div class="alert alert-warning mt-3">{{ $message }}</div> @enderror
    @if ($activeStep && isset($steps[$activeStep]))
        <form wire:submit="{{ $supplementStep ? 'saveSupplementFiles' : 'completeStep' }}" class="card border-primary mt-3">
            <div class="card-header fw-semibold">{{ $supplementStep ? 'Bổ sung file: ' : 'Xác nhận bước: ' }}{{ $steps[$activeStep] }}</div>
            <div class="card-body">
                @if ($supplementStep)
                    <p class="text-muted small">Bổ sung hồ sơ cho bước đã hoàn thành; tiến độ và lịch sử hoàn thành được giữ nguyên.</p>
                @endif
                <label for="workflowFiles-{{ $contractId }}" class="form-label">File đính kèm {{ $supplementStep ? '(bắt buộc)' : '(tùy chọn)' }}</label>
                <input id="workflowFiles-{{ $contractId }}" type="file" multiple class="form-control" wire:model="uploadFiles" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                <div class="form-text">Tối đa 10 file, mỗi file 200MB. PDF, Word, Excel, JPG, PNG.</div>
                @error('uploadFiles') <div class="text-danger">{{ $message }}</div> @enderror
                @error('uploadFiles.*') <div class="text-danger">{{ $message }}</div> @enderror
                <div class="small text-muted" wire:loading wire:target="uploadFiles">Đang tải file...</div>
                @foreach ($uploadFiles as $file)
                    <div class="small text-muted mt-1">{{ $file->getClientOriginalName() }}</div>
                @endforeach
                @if (! $supplementStep)
                    <label for="workflowComment-{{ $contractId }}" class="form-label mt-3">Ghi chú</label>
                    <textarea id="workflowComment-{{ $contractId }}" class="form-control" rows="3" maxlength="1000" wire:model="comment"></textarea>
                    @error('comment') <div class="text-danger">{{ $message }}</div> @enderror
                @endif
            </div>
            <div class="card-footer d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="uploadFiles,completeStep,saveSupplementFiles">{{ $supplementStep ? 'Lưu file bổ sung' : 'Xác nhận hoàn thành' }}</button>
                <button type="button" class="btn btn-light" wire:click="cancelStep">Hủy</button>
            </div>
        </form>
    @endif
    @if ($savedNotes->isNotEmpty())
        <h3 class="h6 mt-4">Lịch sử tiến độ và ghi chú</h3>
        @foreach ($savedNotes as $note)
            <div class="border-start border-primary ps-3 mb-3" wire:key="workflow-note-{{ $note->id }}">
                <div class="fw-semibold">{{ $steps[$note->step_name] ?? $note->step_name }}</div>
                <div class="small text-muted">{{ $note->user?->name }} · {{ $note->created_at?->format('d/m/Y H:i') }}</div>
                @if ($note->comment)
                    <div class="mt-1">{!! nl2br(e($note->comment)) !!}</div>
                @endif
            </div>
        @endforeach
    @endif
    @foreach ($filesByStep as $step => $files)
        <h3 class="h6 mt-4">File: {{ $steps[$step] ?? $step }}</h3>
        @foreach ($files as $file)
            <div class="d-flex flex-wrap gap-2 justify-content-between border-bottom py-2" wire:key="workflow-file-{{ $file->id }}">
                <div>
                    <a href="{{ route('contracts.milestone-files.download', [$contract, $file]) }}">{{ $file->original_name }}</a>
                    <div class="small text-muted">{{ $file->uploader?->name }} · {{ $file->created_at?->format('d/m/Y H:i') }}</div>
                </div>
                @if ($canEdit)
                    <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteFile({{ $file->id }})" wire:confirm="Xóa file đính kèm này?">Xóa file</button>
                @endif
            </div>
        @endforeach
    @endforeach
</div>
