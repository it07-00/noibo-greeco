<?php

declare(strict_types=1);

namespace App\Livewire\DocumentRegulations;

use App\Enums\PermissionEnum;
use App\Models\DocumentRegulation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Quy định Tài liệu')]
final class DocumentRegulationIndex extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public string $search = '';
    public string $filterOwner = '';

    // Form inputs
    public ?int $regulationId = null;
    public string $code = '';
    public string $title = '';
    public string $owner = '';
    public string $status = 'active';
    public string $summary = '';
    public string $content = '';
    public $file; // temporary file upload

    // For detail view
    public ?DocumentRegulation $selectedRegulation = null;

    // Permissions and roles
    public bool $canManage = false;

    // Toast message
    public ?string $successMessage = null;

    protected function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'unique:document_regulations,code,' . $this->regulationId],
            'title' => ['required', 'string', 'max:255'],
            'owner' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:active,inactive'],
            'summary' => ['required', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:10240'], // 10MB limit
        ];
    }

    protected array $validationAttributes = [
        'code' => 'mã quy định',
        'title' => 'tên quy định',
        'owner' => 'phòng ban phụ trách',
        'status' => 'trạng thái',
        'summary' => 'tóm tắt nội dung',
        'content' => 'nội dung chi tiết',
        'file' => 'tài liệu đính kèm',
    ];

    public function mount(): void
    {
        abort_unless(auth()->user()?->can(PermissionEnum::DocumentView->value), 403);
        $this->canManage = auth()->user()?->can(PermissionEnum::DocumentManage->value) ?? false;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterOwner(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->regulationId = null;
        $this->code = '';
        $this->title = '';
        $this->owner = '';
        $this->status = 'active';
        $this->summary = '';
        $this->content = '';
        $this->file = null;
        $this->resetValidation();
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()?->can(PermissionEnum::DocumentManage->value), 403);
        $this->resetForm();
        $this->dispatch('document:open-create');
    }

    public function openEdit(int $id): void
    {
        abort_unless(auth()->user()?->can(PermissionEnum::DocumentManage->value), 403);
        $this->resetForm();
        
        $regulation = DocumentRegulation::findOrFail($id);
        $this->regulationId = $regulation->id;
        $this->code = $regulation->code;
        $this->title = $regulation->title;
        $this->owner = $regulation->owner;
        $this->status = $regulation->status;
        $this->summary = $regulation->summary;
        $this->content = $regulation->content ?? '';
        
        $this->dispatch('document:open-edit');
    }

    public function showDetails(int $id): void
    {
        $this->selectedRegulation = DocumentRegulation::findOrFail($id);
        $this->dispatch('document:open-detail');
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->can(PermissionEnum::DocumentManage->value), 403);
        $this->validate();

        $data = [
            'code' => $this->code,
            'title' => $this->title,
            'owner' => $this->owner,
            'status' => $this->status,
            'summary' => $this->summary,
            'content' => $this->content ?: null,
        ];

        if ($this->file) {
            $path = $this->file->store('document_regulations', 'public');
            $data['file_path'] = $path;
        }

        if ($this->regulationId) {
            $regulation = DocumentRegulation::findOrFail($this->regulationId);
            if ($this->file && $regulation->file_path) {
                Storage::disk('public')->delete($regulation->file_path);
            }
            $regulation->update($data);
            $this->successMessage = 'Cập nhật quy định tài liệu thành công!';
        } else {
            $data['created_by'] = auth()->id();
            DocumentRegulation::create($data);
            $this->successMessage = 'Thêm mới quy định tài liệu thành công!';
        }

        $this->resetForm();
        $this->dispatch('document:saved');
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()?->can(PermissionEnum::DocumentManage->value), 403);
        $regulation = DocumentRegulation::findOrFail($id);

        if ($regulation->file_path) {
            Storage::disk('public')->delete($regulation->file_path);
        }

        $regulation->delete();
        $this->successMessage = 'Xóa quy định tài liệu thành công!';
        $this->dispatch('document:deleted');
    }

    public function downloadFile(int $id)
    {
        $regulation = DocumentRegulation::findOrFail($id);
        
        if (!$regulation->file_path || !Storage::disk('public')->exists($regulation->file_path)) {
            session()->flash('error', 'Tệp đính kèm không tồn tại.');
            return null;
        }

        return Storage::disk('public')->download($regulation->file_path, $regulation->title . '.' . pathinfo($regulation->file_path, PATHINFO_EXTENSION));
    }

    public function render(): View
    {
        $query = DocumentRegulation::query()
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('summary', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterOwner, function ($q) {
                $q->where('owner', $this->filterOwner);
            })
            ->orderBy('code');

        $owners = DocumentRegulation::query()
            ->distinct()
            ->pluck('owner')
            ->filter()
            ->toArray();

        return view('livewire.document-regulations.document-regulation-index', [
            'regulations' => $query->paginate(10),
            'owners' => $owners,
            'canManage' => auth()->user()?->can(PermissionEnum::DocumentManage->value) ?? false,
        ]);
    }
}
