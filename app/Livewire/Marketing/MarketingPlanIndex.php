<?php

declare(strict_types=1);

namespace App\Livewire\Marketing;

use App\DTOs\MarketingPlanDTO;
use App\Enums\MarketingCategory;
use App\Enums\MarketingPlanStatus;
use App\Models\MarketingPlan;
use App\Models\User;
use App\Services\MarketingPlanService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kế hoạch Marketing')]
final class MarketingPlanIndex extends Component
{
    use WithFileUploads;
    use WithPagination;

    private MarketingPlanService $planService;

    // View state ('calendar' or 'list')
    public string $viewMode = 'calendar';

    // Filters
    public string $filterCategory = 'all';

    public string $filterStatus = 'all';

    public int|string $filterCreatorId = 0;

    public string $search = '';

    // Form properties
    public ?int $planId = null;

    public string $title = '';

    public string $category = 'website';

    public ?string $content = null;

    public string $scheduled_at = '';

    public string $status = 'draft';

    public ?string $notes = null;

    public $newImages = [];

    public $existingImages = [];

    public $deleteImageIds = [];

    public $uploadDetailImages = [];

    // Rejection reason modal property
    public ?int $rejectPlanId = null;

    public string $rejection_reason = '';

    // Detail view modal ID
    public ?int $selectedPlanId = null;

    public function boot(MarketingPlanService $planService): void
    {
        $this->planService = $planService;
    }

    public function mount(): void
    {
        Gate::authorize('viewAny', MarketingPlan::class);
    }

    public function getSelectedPlanProperty(): ?MarketingPlan
    {
        if ($this->selectedPlanId === null) {
            return null;
        }

        return MarketingPlan::with(['creator', 'approver', 'images'])->find($this->selectedPlanId);
    }

    public function updatedFilterCategory(): void
    {
        $this->resetPage();
        $this->dispatch('marketing:filter-changed');
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
        $this->dispatch('marketing:filter-changed');
    }

    public function updatedFilterCreatorId(): void
    {
        $this->resetPage();
        $this->dispatch('marketing:filter-changed');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    protected function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'content' => ['nullable', 'string'],
            'scheduled_at' => ['required', 'date'],
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ];

        if (! empty($this->newImages)) {
            $rules['newImages.*'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'];
        }

        return $rules;
    }

    protected array $validationAttributes = [
        'title' => 'tiêu đề',
        'category' => 'danh mục kế hoạch',
        'content' => 'nội dung bài viết',
        'scheduled_at' => 'thời gian xuất bản',
        'status' => 'trạng thái',
        'newImages.*' => 'hình ảnh',
    ];

    /**
     * Fetch events for FullCalendar grid.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getEvents(string $start = '', string $end = ''): array
    {
        Gate::authorize('viewAny', MarketingPlan::class);

        $plans = $this->planService->getEventsInRange(
            $start,
            $end,
            $this->filterCategory,
            $this->filterStatus,
            $this->filterCreatorId
        );

        $result = [];

        foreach ($plans as $plan) {
            $statusEnum = $plan->status instanceof MarketingPlanStatus
                ? $plan->status
                : MarketingPlanStatus::from($plan->status);

            $catEnum = $plan->category instanceof MarketingCategory
                ? $plan->category
                : MarketingCategory::tryFrom($plan->category);

            $firstImage = $plan->images->first();
            $thumbnailUrl = $firstImage ? Storage::url($firstImage->file_path) : null;

            $catLabel = $catEnum?->label() ?? $plan->category;

            $result[] = [
                'id' => (string) $plan->id,
                'db_id' => $plan->id,
                'title' => "{$plan->title} [{$catLabel}]",
                'raw_title' => $plan->title,
                'start' => $plan->scheduled_at->toIso8601String(),
                'status' => $statusEnum->value,
                'status_label' => $statusEnum->label(),
                'category' => $plan->category,
                'category_label' => $catLabel,
                'creator_name' => $plan->creator?->name ?? 'N/A',
                'images_count' => $plan->images->count(),
                'thumbnail_url' => $thumbnailUrl,
                'classNames' => $statusEnum->calendarClass(),
                'can_edit' => auth()->user()?->can('update', $plan) ?? false,
                'can_delete' => auth()->user()?->can('delete', $plan) ?? false,
                'can_approve' => auth()->user()?->can('approve', MarketingPlan::class) ?? false,
            ];
        }

        return $result;
    }

    public function getCalendarEvents(string $start = '', string $end = ''): array
    {
        return $this->getEvents($start, $end);
    }

    public function fetchCalendarEvents(string $start = '', string $end = ''): array
    {
        return $this->getEvents($start, $end);
    }

    public function openCreate(?string $dateStr = null): void
    {
        Gate::authorize('create', MarketingPlan::class);

        $this->resetErrorBag();
        $this->resetForm();

        $dateStr = $dateStr ?: date('Y-m-d');
        $date = date('Y-m-d', strtotime($dateStr));
        $time = date('H:i');
        $this->scheduled_at = "{$date}T{$time}";
        $this->category = MarketingCategory::Website->value;
        $this->status = MarketingPlanStatus::Draft->value;

        $this->dispatch('marketing:open-create', content: '');
    }

    public function openEdit(int $id): void
    {
        $plan = MarketingPlan::with(['images'])->findOrFail($id);
        Gate::authorize('update', $plan);

        $this->resetErrorBag();
        $this->resetForm();

        $this->planId = $plan->id;
        $this->title = $plan->title;
        $this->category = $plan->category->value ?? (string) $plan->category;
        $this->content = $plan->content;
        $this->scheduled_at = $plan->scheduled_at->format('Y-m-d\TH:i');
        $this->status = $plan->status->value;
        $this->notes = $plan->notes;

        $this->existingImages = $plan->images->map(fn ($img) => [
            'id' => $img->id,
            'url' => Storage::url($img->file_path),
            'name' => $img->file_name,
        ])->toArray();

        $this->dispatch('marketing:open-edit', content: $this->content ?? '');
    }

    public function openDetail(int $id): void
    {
        $plan = MarketingPlan::with(['creator', 'approver', 'images'])->findOrFail($id);
        Gate::authorize('view', $plan);

        $this->selectedPlanId = $plan->id;
        $this->dispatch('marketing:open-detail');
    }

    public function markForImageDeletion(int $imageId): void
    {
        if (! in_array($imageId, $this->deleteImageIds, true)) {
            $this->deleteImageIds[] = $imageId;
        }

        $this->existingImages = array_filter(
            $this->existingImages,
            fn ($img) => (int) $img['id'] !== $imageId
        );
    }

    public function removeNewImage(int $index): void
    {
        unset($this->newImages[$index]);
        $this->newImages = array_values($this->newImages);
    }

    public function updatedUploadDetailImages(): void
    {
        if (empty($this->uploadDetailImages) || ! $this->selectedPlanId) {
            return;
        }

        $this->validate([
            'uploadDetailImages.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
        ], [], [
            'uploadDetailImages.*' => 'hình ảnh đính kèm',
        ]);

        $plan = MarketingPlan::findOrFail($this->selectedPlanId);
        Gate::authorize('view', $plan);

        $dto = MarketingPlanDTO::fromArray([
            'title' => $plan->title,
            'category' => $plan->category->value ?? (string) $plan->category,
            'content' => $plan->content,
            'scheduled_at' => $plan->scheduled_at->format('Y-m-d H:i:s'),
            'status' => $plan->status->value ?? (string) $plan->status,
            'notes' => $plan->notes,
        ]);

        $this->planService->update($plan, $dto, $this->uploadDetailImages, []);

        $this->uploadDetailImages = [];

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Thành công!',
            'text' => 'Đã tải thêm hình ảnh vào bài viết!',
            'toast' => true,
            'position' => 'top-end',
            'timer' => 3000,
        ]);
        $this->dispatch('marketing:filter-changed');
    }

    public function deleteSingleImage(int $imageId): void
    {
        $image = \App\Models\MarketingPlanImage::findOrFail($imageId);
        $plan = $image->marketingPlan;
        Gate::authorize('view', $plan);

        if (Storage::disk('public')->exists($image->file_path)) {
            Storage::disk('public')->delete($image->file_path);
        }
        $image->delete();

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Đã xóa!',
            'text' => 'Đã xóa hình ảnh khỏi bài viết.',
            'toast' => true,
            'position' => 'top-end',
            'timer' => 3000,
        ]);
        $this->dispatch('marketing:filter-changed');
    }

    public function save(): void
    {
        $this->validate();

        $dto = MarketingPlanDTO::fromArray([
            'title' => $this->title,
            'category' => $this->category,
            'content' => $this->content,
            'scheduled_at' => $this->scheduled_at,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        if ($this->planId !== null) {
            $plan = MarketingPlan::findOrFail($this->planId);
            Gate::authorize('update', $plan);

            $this->planService->update($plan, $dto, $this->newImages, $this->deleteImageIds);
            $message = 'Cập nhật kế hoạch Marketing thành công!';
        } else {
            Gate::authorize('create', MarketingPlan::class);

            $this->planService->create($dto, $this->newImages);
            $message = 'Tạo mới kế hoạch Marketing thành công!';
        }

        $this->dispatch('marketing:saved', message: $message);
        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Thành công!',
            'text' => $message,
        ]);

        $this->dispatch('marketing:close-modal-form');
        $this->dispatch('marketing:filter-changed');
        $this->resetForm();
    }

    public function submitForReview(int $id): void
    {
        $plan = MarketingPlan::findOrFail($id);
        Gate::authorize('update', $plan);

        $this->planService->submitForApproval($plan);

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Đã gửi duyệt!',
            'text' => 'Kế hoạch đã được chuyển sang trạng thái Chờ duyệt.',
        ]);

        $this->dispatch('marketing:close-detail');
        $this->dispatch('marketing:filter-changed');
    }

    public function approvePlan(int $id): void
    {
        Gate::authorize('approve', MarketingPlan::class);

        $plan = MarketingPlan::findOrFail($id);
        $this->planService->approve($plan);

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Phê duyệt thành công!',
            'text' => 'Bài viết Marketing đã được phê duyệt.',
        ]);

        $this->dispatch('marketing:close-detail');
        $this->dispatch('marketing:filter-changed');
    }

    public function openRejectModal(int $id): void
    {
        Gate::authorize('approve', MarketingPlan::class);

        $this->rejectPlanId = $id;
        $this->rejection_reason = '';
        $this->dispatch('marketing:open-reject-modal');
    }

    public function confirmReject(): void
    {
        Gate::authorize('approve', MarketingPlan::class);

        $this->validate([
            'rejection_reason' => ['required', 'string', 'min:3'],
        ], [], [
            'rejection_reason' => 'lý do từ chối',
        ]);

        if ($this->rejectPlanId) {
            $plan = MarketingPlan::findOrFail($this->rejectPlanId);
            $this->planService->reject($plan, $this->rejection_reason);

            $this->dispatch('swal:alert', [
                'icon' => 'info',
                'title' => 'Đã từ chối bài viết',
                'text' => 'Yêu cầu đã được trả về để tác giả chỉnh sửa.',
            ]);

            $this->dispatch('marketing:close-reject-modal');
            $this->dispatch('marketing:close-detail');
            $this->dispatch('marketing:filter-changed');
        }
    }

    public function deletePlan(int $id): void
    {
        $plan = MarketingPlan::findOrFail($id);
        Gate::authorize('delete', $plan);

        $this->planService->delete($plan);

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Đã xóa!',
            'text' => 'Kế hoạch Marketing đã được xóa thành công.',
        ]);

        $this->dispatch('marketing:close-detail');
        $this->dispatch('marketing:filter-changed');
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'planId',
            'title',
            'category',
            'content',
            'scheduled_at',
            'status',
            'notes',
            'newImages',
            'existingImages',
            'deleteImageIds',
            'selectedPlanId',
        ]);
        $this->category = 'website';
        $this->status = 'draft';
    }

    public function render(): View
    {
        $query = MarketingPlan::query()->with(['creator', 'approver', 'images']);

        if ($this->filterCategory !== 'all') {
            $query->where('category', $this->filterCategory);
        }

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ((int) $this->filterCreatorId > 0) {
            $query->where('created_by', (int) $this->filterCreatorId);
        }

        if (filled($this->search)) {
            $query->where(function ($q): void {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('content', 'like', '%'.$this->search.'%');
            });
        }

        $listPlans = $query->orderBy('scheduled_at', 'desc')->paginate(12);

        $users = User::query()->orderBy('name')->get(['id', 'name']);

        return view('livewire.marketing.marketing-plan-index', [
            'listPlans' => $listPlans,
            'users' => $users,
            'selectedPlan' => $this->selectedPlan,
            'categoriesEnum' => MarketingCategory::cases(),
            'statusesEnum' => MarketingPlanStatus::cases(),
        ]);
    }
}
