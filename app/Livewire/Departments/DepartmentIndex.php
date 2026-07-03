<?php

declare(strict_types=1);

namespace App\Livewire\Departments;

use App\Models\Department;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Quản lý Phòng ban')]
final class DepartmentIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public string $search = '';

    // Form fields
    public ?int $departmentId = null;
    public string $name = '';
    public string $code = '';
    public string $description = '';

    // Toast message
    public ?string $successMessage = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:departments,name,' . $this->departmentId],
            'code' => ['required', 'string', 'max:50', 'unique:departments,code,' . $this->departmentId],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected array $validationAttributes = [
        'name' => 'tên phòng ban',
        'code' => 'mã phòng ban',
        'description' => 'mô tả chi tiết',
    ];

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('role.manage'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->departmentId = null;
        $this->name = '';
        $this->code = '';
        $this->description = '';
        $this->resetValidation();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->dispatch('department:open-create');
    }

    public function openEdit(int $id): void
    {
        $this->resetForm();
        $department = Department::findOrFail($id);
        $this->departmentId = $department->id;
        $this->name = $department->name;
        $this->code = $department->code;
        $this->description = $department->description ?? '';

        $this->dispatch('department:open-edit');
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description ?: null,
        ];

        if ($this->departmentId) {
            $department = Department::findOrFail($this->departmentId);
            $department->update($data);
            $this->successMessage = 'Cập nhật thông tin phòng ban thành công!';
        } else {
            Department::create($data);
            $this->successMessage = 'Thêm mới phòng ban thành công!';
        }

        $this->resetForm();
        $this->dispatch('department:saved');
    }

    public function delete(int $id): void
    {
        $department = Department::findOrFail($id);

        // Optional: safety check if department has users
        if ($department->users()->exists()) {
            session()->flash('error', "Không thể xóa phòng ban '{$department->name}' vì đang có thành viên thuộc phòng ban này.");
            return;
        }

        $department->delete();
        $this->successMessage = 'Xóa phòng ban thành công!';
        $this->dispatch('department:deleted');
    }

    public function render(): View
    {
        $query = Department::query()
            ->withCount('users')
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name');

        return view('livewire.departments.department-index', [
            'departments' => $query->paginate(10),
        ]);
    }
}
