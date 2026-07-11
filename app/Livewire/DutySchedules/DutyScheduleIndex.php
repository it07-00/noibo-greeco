<?php

declare(strict_types=1);

namespace App\Livewire\DutySchedules;

use App\DTOs\DutyScheduleDTO;
use App\Enums\RoleEnum;
use App\Models\DutySchedule;
use App\Models\User;
use App\Repositories\NoiboWorkScheduleRepository;
use App\Services\DutyScheduleService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Lịch công tác')]
final class DutyScheduleIndex extends Component
{
    private DutyScheduleService $scheduleService;

    private NoiboWorkScheduleRepository $noiboRepo;

    // Day schedules view properties (for Director/non-creators)
    public string $selectedDateStr = '';

    public array $daySchedules = [];

    // Filters
    public int|string $filterUserId = 0;

    public bool $showNoiboSchedules = true;

    // Form properties
    public ?int $scheduleId = null;

    public array $user_ids = [];

    public string $title = '';

    public ?string $description = null;

    public ?string $location = null;

    public string $start_at = '';

    public ?string $end_at = null;

    public string $label_color = 'primary';

    public bool $is_private = false;

    public ?string $successMessage = null;

    public function boot(DutyScheduleService $scheduleService, NoiboWorkScheduleRepository $noiboRepo): void
    {
        $this->scheduleService = $scheduleService;
        $this->noiboRepo = $noiboRepo;
    }

    public function mount(): void
    {
        Gate::authorize('viewAny', DutySchedule::class);
    }

    public function updatedFilterUserId(): void
    {
        $this->dispatch('schedule:filter-changed');
    }

    public function updatedShowNoiboSchedules(): void
    {
        $this->dispatch('schedule:filter-changed');
    }

    protected function rules(): array
    {
        $startRules = ['required', 'date'];
        if ($this->scheduleId === null) {
            $startRules[] = 'after_or_equal:today';
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_at' => $startRules,
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'label_color' => ['required', 'string', 'in:primary,success,warning,danger,info,purple'],
            'is_private' => ['nullable', 'boolean'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['exists:users,id'],
        ];
    }

    protected array $validationAttributes = [
        'title' => 'tiêu đề',
        'start_at' => 'thời gian bắt đầu',
        'end_at' => 'thời gian kết thúc',
        'label_color' => 'nhãn màu',
        'user_ids' => 'thành viên tham gia',
    ];

    /**
     * Fetch events in a date range for FullCalendar.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getEvents(string $start, string $end): array
    {
        Gate::authorize('viewAny', DutySchedule::class);

        $events = $this->scheduleService->getEventsInRange($start, $end, $this->filterUserId ?: null);

        $result = [];

        foreach ($events as $event) {
            $isCreator = auth()->id() === $event->created_by;
            $isParticipant = $event->users->contains(auth()->id());
            $hasPrivatePermission = auth()->user()?->hasPermissionTo(\App\Enums\PermissionEnum::ScheduleViewPrivate->value) ?? false;
            $canSeeDetails = $isCreator || $isParticipant || $hasPrivatePermission;

            $isPrivate = (bool) $event->is_private;
            $titlePrefix = $isPrivate ? '🔒 ' : '';
            $rawTitle = $isPrivate && ! $canSeeDetails ? 'Lịch riêng tư' : $event->title;

            // Format verbose title for FullCalendar grid
            if ($isPrivate && ! $canSeeDetails) {
                $title = $titlePrefix.$rawTitle;
            } else {
                $creatorName = $event->creator?->name ?? 'N/A';
                $participantsStr = '';
                if ($event->users->isNotEmpty()) {
                    $participantsNames = $event->users->pluck('name')->toArray();
                    $participantsStr = ' (với '.implode(', ', $participantsNames).')';
                }
                $title = $titlePrefix."{$creatorName}: {$rawTitle}{$participantsStr}";
            }

            $description = $isPrivate && ! $canSeeDetails ? null : $event->description;
            $location = $isPrivate && ! $canSeeDetails ? null : $event->location;

            $startCal = $event->start_at;
            $endCal = $event->end_at;

            if ($endCal && $endCal->clone()->startOfDay()->gt($startCal->clone()->startOfDay())) {
                $currentDay = $startCal->clone()->startOfDay();
                $lastDay = $endCal->clone()->startOfDay();
                
                $startTimeStr = $startCal->format('H:i:s');
                $endTimeStr = $endCal->format('H:i:s');
                
                while ($currentDay->lte($lastDay)) {
                    $occStart = Carbon::parse($currentDay->format('Y-m-d') . ' ' . $startTimeStr);
                    $occEnd = Carbon::parse($currentDay->format('Y-m-d') . ' ' . $endTimeStr);
                    $dayStr = $currentDay->format('Y-m-d');

                    $result[] = [
                        'id' => $event->id . '_' . $dayStr,
                        'db_id' => $event->id,
                        'title' => $title,
                        'raw_title' => $titlePrefix.$rawTitle,
                        'start' => $occStart->toIso8601String(),
                        'end' => $occEnd->toIso8601String(),
                        'description' => $description,
                        'location' => $location,
                        'classNames' => $this->getEventClasses($isPrivate && ! $canSeeDetails ? 'private' : $event->label_color),
                        'label_color' => $event->label_color,
                        'creator_name' => $event->creator?->name ?? 'N/A',
                        'participants' => $event->users->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])->toArray(),
                        'can_edit' => auth()->user()?->can('update', $event) ?? false,
                        'can_delete' => auth()->user()?->can('delete', $event) ?? false,
                        'source' => 'greeco',
                    ];
                    
                    $currentDay->addDay();
                }
            } else {
                $result[] = [
                    'id' => $event->id,
                    'db_id' => $event->id,
                    'title' => $title,
                    'raw_title' => $titlePrefix.$rawTitle,
                    'start' => $startCal->toIso8601String(),
                    'end' => $endCal?->toIso8601String(),
                    'description' => $description,
                    'location' => $location,
                    'classNames' => $this->getEventClasses($isPrivate && ! $canSeeDetails ? 'private' : $event->label_color),
                    'label_color' => $event->label_color,
                    'creator_name' => $event->creator?->name ?? 'N/A',
                    'participants' => $event->users->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])->toArray(),
                    'can_edit' => auth()->user()?->can('update', $event) ?? false,
                    'can_delete' => auth()->user()?->can('delete', $event) ?? false,
                    'source' => 'greeco',
                ];
            }
        }

        // Merge Bảo Châu events when enabled for any member who can access this page.
        if ($this->showNoiboSchedules && $this->canViewNoiboSchedules()) {
            $noiboItems = $this->noiboRepo->getEventsInRange($start, $end);
            foreach ($noiboItems as $item) {
                $startCal = Carbon::parse($item['start_date'] . ($item['start_time'] ? ' ' . $item['start_time'] : ''));
                
                $endDate = $item['end_date'] ?? $item['start_date'];
                $endTime = $item['end_time'] ?? null;
                if ($endTime !== null && $endTime !== '') {
                    $endCal = Carbon::parse("{$endDate} {$endTime}");
                } else {
                    $endCal = Carbon::parse($endDate)->endOfDay();
                }

                $creatorName = $item['creator_name'] ?? 'N/A';
                $participants = $item['participants'] ?? [];
                $participantsStr = '';
                if (! empty($participants)) {
                    $names = array_column($participants, 'name');
                    $participantsStr = ' (với '.implode(', ', $names).')';
                }
                $title = "{$creatorName}: {$item['title']}{$participantsStr}";

                if ($endCal->clone()->startOfDay()->gt($startCal->clone()->startOfDay())) {
                    $currentDay = $startCal->clone()->startOfDay();
                    $lastDay = $endCal->clone()->startOfDay();
                    
                    $startTimeStr = $startCal->format('H:i:s');
                    $endTimeStr = $endCal->format('H:i:s');
                    
                    while ($currentDay->lte($lastDay)) {
                        $occStart = Carbon::parse($currentDay->format('Y-m-d') . ' ' . $startTimeStr);
                        $occEnd = Carbon::parse($currentDay->format('Y-m-d') . ' ' . $endTimeStr);
                        $dayStr = $currentDay->format('Y-m-d');

                        $result[] = [
                            'id' => 'noibo_' . $item['id'] . '_' . $dayStr,
                            'db_id' => 'noibo_' . $item['id'],
                            'title' => $title,
                            'raw_title' => $item['title'],
                            'start' => $occStart->toIso8601String(),
                            'end' => $occEnd->toIso8601String(),
                            'description' => $item['description'] ?? null,
                            'location' => null,
                            'classNames' => ['bg-warning-subtle', 'text-warning', 'border-warning', 'p-1', 'fw-semibold'],
                            'label_color' => 'noibo',
                            'creator_name' => $creatorName,
                            'participants' => $participants,
                            'can_edit' => false,
                            'can_delete' => false,
                            'source' => 'noibo',
                        ];
                        
                        $currentDay->addDay();
                    }
                } else {
                    $result[] = [
                        'id' => 'noibo_' . $item['id'],
                        'db_id' => 'noibo_' . $item['id'],
                        'title' => $title,
                        'raw_title' => $item['title'],
                        'start' => $startCal->toIso8601String(),
                        'end' => $endCal->toIso8601String(),
                        'description' => $item['description'] ?? null,
                        'location' => null,
                        'classNames' => ['bg-warning-subtle', 'text-warning', 'border-warning', 'p-1', 'fw-semibold'],
                        'label_color' => 'noibo',
                        'creator_name' => $creatorName,
                        'participants' => $participants,
                        'can_edit' => false,
                        'can_delete' => false,
                        'source' => 'noibo',
                    ];
                }
            }
        }

        return $result;
    }


    private function getEventClasses(string $labelColor): array
    {
        return match ($labelColor) {
            'success' => ['bg-success-subtle', 'text-success', 'border-success', 'p-1', 'fw-semibold'],
            'warning' => ['bg-warning-subtle', 'text-warning', 'border-warning', 'p-1', 'fw-semibold'],
            'danger' => ['bg-danger-subtle', 'text-danger', 'border-danger', 'p-1', 'fw-semibold'],
            'info' => ['bg-info-subtle', 'text-info', 'border-info', 'p-1', 'fw-semibold'],
            'purple' => ['bg-purple-subtle', 'text-purple', 'border-purple', 'p-1', 'fw-semibold'],
            'private' => ['bg-secondary-subtle', 'text-secondary', 'border-secondary', 'p-1', 'fw-semibold', 'opacity-75'],
            default => ['bg-primary-subtle', 'text-primary', 'border-primary', 'p-1', 'fw-semibold'],
        };
    }

    public function openCreate(string $dateStr): void
    {
        Gate::authorize('create', DutySchedule::class);

        // Prevent creating duty schedules in the past
        $selectedDate = date('Y-m-d', strtotime($dateStr));
        $todayDate = date('Y-m-d');
        if ($selectedDate < $todayDate) {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Thao tác không hợp lệ',
                'text' => 'Không thể tạo lịch công tác cho ngày trong quá khứ!',
            ]);

            return;
        }

        $this->resetErrorBag();
        $this->scheduleId = null;
        $this->title = '';
        $this->description = null;
        $this->location = null;

        // Parse date and set default time to current hour
        $date = date('Y-m-d', strtotime($dateStr));
        $time = date('H:i');
        $this->start_at = "{$date}T{$time}";
        $this->end_at = null;
        $this->label_color = 'primary';
        $this->is_private = false;
        $this->user_ids = [];

        $this->dispatch('schedule:open-create');
    }

    public function save(): void
    {
        $this->validate();

        $dto = DutyScheduleDTO::fromArray([
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'label_color' => $this->label_color,
            'is_private' => $this->is_private,
            'user_ids' => $this->user_ids,
        ]);

        if ($this->scheduleId !== null) {
            $schedule = DutySchedule::findOrFail($this->scheduleId);
            Gate::authorize('update', $schedule);

            $this->scheduleService->update($schedule, $dto);
            $message = 'Cập nhật lịch công tác thành công!';
        } else {
            Gate::authorize('create', DutySchedule::class);

            $this->scheduleService->create($dto);
            $message = 'Tạo lịch công tác thành công!';
        }

        $this->dispatch('schedule:saved', message: $message);
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $schedule = DutySchedule::with('users')->findOrFail($id);
        Gate::authorize('update', $schedule);

        $this->resetErrorBag();
        $this->scheduleId = $schedule->id;
        $this->title = $schedule->title;
        $this->description = $schedule->description;
        $this->location = $schedule->location;
        $this->start_at = $schedule->start_at->format('Y-m-d\TH:i');
        $this->end_at = $schedule->end_at?->format('Y-m-d\TH:i');
        $this->label_color = $schedule->label_color;
        $this->is_private = (bool) $schedule->is_private;
        $this->user_ids = $schedule->users->pluck('id')->toArray();

        $this->dispatch('schedule:open-edit');
    }

    public function delete(int $id): void
    {
        $schedule = DutySchedule::findOrFail($id);
        Gate::authorize('delete', $schedule);

        $this->scheduleService->delete($schedule);

        $this->dispatch('schedule:deleted', message: 'Xóa lịch công tác thành công!');
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->scheduleId = null;
        $this->title = '';
        $this->description = null;
        $this->location = null;
        $this->start_at = '';
        $this->end_at = null;
        $this->label_color = 'primary';
        $this->is_private = false;
        $this->user_ids = [];
    }

    public function showDaySchedules(string $dateStr): void
    {
        Gate::authorize('viewAny', DutySchedule::class);

        $this->selectedDateStr = Carbon::parse($dateStr)->format('d/m/Y');

        $start = $dateStr.' 00:00:00';
        $end = $dateStr.' 23:59:59';

        $schedules = $this->scheduleService->getEventsInRange($start, $end, $this->filterUserId ?: null);

        $this->daySchedules = $schedules->map(function (DutySchedule $event) {
            $isCreator = auth()->id() === $event->created_by;
            $isParticipant = $event->users->contains(auth()->id());
            $hasPrivatePermission = auth()->user()?->hasPermissionTo(\App\Enums\PermissionEnum::ScheduleViewPrivate->value) ?? false;
            $canSeeDetails = $isCreator || $isParticipant || $hasPrivatePermission;

            $isPrivate = (bool) $event->is_private;
            $titlePrefix = $isPrivate ? '🔒 ' : '';
            $title = $isPrivate && ! $canSeeDetails ? 'Lịch riêng tư' : $event->title;
            $title = $titlePrefix.$title;

            $description = $isPrivate && ! $canSeeDetails ? null : $event->description;
            $location = $isPrivate && ! $canSeeDetails ? null : $event->location;

            return [
                'id' => $event->id,
                'title' => $title,
                'start_formatted' => $event->start_at->format('H:i d/m/Y'),
                'end_formatted' => $event->end_at?->format('H:i d/m/Y'),
                'description' => $description,
                'location' => $location,
                'label_color' => $isPrivate && ! $canSeeDetails ? 'private' : $event->label_color,
                'creator_name' => $event->creator?->name ?? 'N/A',
                'participants' => $event->users->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])->toArray(),
                'can_edit' => auth()->user()?->can('update', $event) ?? false,
                'can_delete' => auth()->user()?->can('delete', $event) ?? false,
                'source' => 'greeco',
            ];
        })->toArray();

        // Merge noibo events for this day
        if ($this->showNoiboSchedules && $this->canViewNoiboSchedules()) {
            $noiboItems = $this->noiboRepo->getEventsInRange($start, $end);
            foreach ($noiboItems as $item) {
                $this->daySchedules[] = $this->noiboRepo->toDayScheduleItem($item);
            }
        }

        $this->dispatch('schedule:open-day-schedules');
    }

    public function openEditFromList(int $id): void
    {
        $this->dispatch('schedule:close-day-schedules');
        $this->edit($id);
    }

    public function openCreateFromList(): void
    {
        $this->dispatch('schedule:close-day-schedules');

        $dateStr = Carbon::createFromFormat('d/m/Y', $this->selectedDateStr)->format('Y-m-d');
        $this->openCreate($dateStr);
    }

    public function deleteFromList(int $id): void
    {
        $this->delete($id);

        // Refresh
        $dateStr = Carbon::createFromFormat('d/m/Y', $this->selectedDateStr)->format('Y-m-d');
        $this->showDaySchedules($dateStr);
    }

    public function canViewNoiboSchedules(): bool
    {
        return auth()->check();
    }

    public function render(): View
    {
        $users = User::query()->orderBy('name')->get(['id', 'name']);

        return view('livewire.duty-schedules.duty-schedule-index', [
            'users' => $users,
            'canViewNoibo' => $this->canViewNoiboSchedules(),
        ]);
    }
}
