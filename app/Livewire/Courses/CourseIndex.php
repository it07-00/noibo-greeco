<?php

declare(strict_types=1);

namespace App\Livewire\Courses;

use App\Enums\CustomerType;
use App\Models\Course;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Quản lý khóa học')]
final class CourseIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public int $editingId = 0;

    public string $code = '';

    public string $name = '';

    public string $startsAt = '';

    public string $endsAt = '';

    public string $location = '';

    public string $description = '';

    public string $duration = '';

    public string $fee = '';

    public string $instructor = '';

    public string $audience = '';

    public string $objectives = '';

    public string $contentSummary = '';

    public string $contentDetail = '';

    /** @var list<int|string> */
    public array $selectedStudentIds = [];

    public function mount(): void
    {
        Gate::authorize('viewAny', Course::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        Gate::authorize('create', Course::class);
        $this->resetForm();
        $this->dispatch('course-form:show');
    }

    public function openEdit(int $courseId): void
    {
        $course = Course::query()->with('students:id')->findOrFail($courseId);
        Gate::authorize('update', $course);

        $this->editingId = $course->id;
        $this->code = $course->code ?? '';
        $this->name = $course->name;
        $this->startsAt = $course->starts_at?->format('Y-m-d') ?? '';
        $this->endsAt = $course->ends_at?->format('Y-m-d') ?? '';
        $this->location = $course->location ?? '';
        $this->description = $course->description ?? '';
        $this->duration = $course->duration ?? '';
        $this->fee = $course->fee > 0 ? (string) (int) $course->fee : '';
        $this->instructor = $course->instructor ?? '';
        $this->audience = $course->audience ?? '';
        $this->objectives = $course->objectives ?? '';
        $this->contentSummary = $course->content_summary ?? '';
        $this->contentDetail = $course->content_detail ?? '';
        $this->selectedStudentIds = $course->students->pluck('id')->all();
        $this->resetValidation();
        $this->dispatch('course-form:show');
    }

    public function save(): void
    {
        $course = $this->editingId > 0
            ? Course::query()->findOrFail($this->editingId)
            : null;
        Gate::authorize($course ? 'update' : 'create', $course ?? Course::class);

        $validated = $this->validate([
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('courses', 'code')->ignore($course?->id),
            ],
            'name' => ['required', 'string', 'max:191'],
            'startsAt' => ['nullable', 'date'],
            'endsAt' => ['nullable', 'date', 'after_or_equal:startsAt'],
            'location' => ['nullable', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:3000'],
            'duration' => ['nullable', 'string', 'max:100'],
            'fee' => ['nullable', 'numeric', 'min:0'],
            'instructor' => ['nullable', 'string', 'max:191'],
            'audience' => ['nullable', 'string'],
            'objectives' => ['nullable', 'string'],
            'contentSummary' => ['nullable', 'string'],
            'contentDetail' => ['nullable', 'string'],
            'selectedStudentIds' => ['array'],
            'selectedStudentIds.*' => [
                'integer',
                'distinct',
                Rule::exists('customers', 'id')->where(
                    fn ($query) => $query->where('type', CustomerType::Individual->value)->whereNull('deleted_at')
                ),
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên khóa học.',
            'code.unique' => 'Mã khóa học đã tồn tại.',
            'endsAt.after_or_equal' => 'Ngày kết thúc phải từ ngày khai giảng trở đi.',
            'selectedStudentIds.*.exists' => 'Danh sách có khách hàng không phải học viên cá nhân.',
        ]);

        DB::transaction(function () use ($course, $validated): void {
            $data = [
                'code' => trim($validated['code']) !== '' ? mb_strtoupper(trim($validated['code'])) : null,
                'name' => trim($validated['name']),
                'starts_at' => $validated['startsAt'] ?: null,
                'ends_at' => $validated['endsAt'] ?: null,
                'location' => $validated['location'] ?: null,
                'description' => $validated['description'] ?: null,
                'duration' => $validated['duration'] ?: null,
                'fee' => $validated['fee'] !== '' ? (float) $validated['fee'] : null,
                'instructor' => $validated['instructor'] ?: null,
                'audience' => $validated['audience'] ?: null,
                'objectives' => $validated['objectives'] ?: null,
                'content_summary' => $validated['contentSummary'] ?: null,
                'content_detail' => $validated['contentDetail'] ?: null,
            ];

            $savedCourse = $course ?? new Course;
            $savedCourse->fill($data)->save();
            $savedCourse->students()->sync(array_map('intval', $validated['selectedStudentIds']));
        });

        $this->dispatch('course-form:hide');
        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Đã lưu khóa học và danh sách học viên',
            'toast' => true,
            'position' => 'top-end',
            'timer' => 2500,
        ]);
        $this->resetForm();
    }

    public function delete(int $courseId): void
    {
        $course = Course::query()->findOrFail($courseId);
        Gate::authorize('delete', $course);
        $course->delete();

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Đã xóa khóa học',
            'toast' => true,
            'position' => 'top-end',
            'timer' => 2500,
        ]);
    }

    public function render(): View
    {
        $courses = Course::query()
            ->with(['students' => fn ($query) => $query->orderBy('name')])
            ->withCount('students')
            ->when(trim($this->search) !== '', function ($query): void {
                $search = trim($this->search);
                $query->where(function ($nested) use ($search): void {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('instructor', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhereHas('students', fn ($studentQuery) => $studentQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('code')
            ->paginate(12);

        return view('livewire.courses.course-index', [
            'courses' => $courses,
            'individualCustomers' => Customer::query()
                ->where('type', CustomerType::Individual->value)
                ->orderBy('name')
                ->get(['id', 'name', 'phone', 'email']),
        ]);
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'code',
            'name',
            'startsAt',
            'endsAt',
            'location',
            'description',
            'duration',
            'fee',
            'instructor',
            'audience',
            'objectives',
            'contentSummary',
            'contentDetail',
            'selectedStudentIds',
        ]);
        $this->resetValidation();
    }
}
