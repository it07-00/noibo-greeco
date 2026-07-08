<?php

declare(strict_types=1);

namespace App\Livewire\RolesPermissions;

use App\DTOs\RoleDTO;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\Department;
use App\Models\Role;
use App\Services\RolePermissionService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Quản lý Vai trò & Quyền')]
final class RolesPermissionsIndex extends Component
{
    public int $activeRoleId = 0;

    public string $newRoleName = '';

    public string $newRoleDescription = '';

    public $newRoleDepartmentId = null;

    public int $editingRoleId = 0;

    public string $editRoleName = '';

    public string $editRoleDescription = '';

    public $editRoleDepartmentId = null;

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    protected array $rules = [
        'newRoleName' => ['required', 'string', 'max:255', 'unique:roles,name'],
        'newRoleDescription' => ['nullable', 'string', 'max:500'],
        'newRoleDepartmentId' => ['nullable', 'integer', 'exists:departments,id'],
        'editRoleName' => ['required', 'string', 'max:255'],
        'editRoleDescription' => ['nullable', 'string', 'max:500'],
        'editRoleDepartmentId' => ['nullable', 'integer', 'exists:departments,id'],
    ];

    public function mount(RolePermissionService $service): void
    {
        Gate::authorize('manage', Role::class);

        $firstRole = Role::query()->orderBy('id')->first();
        if ($firstRole) {
            $this->activeRoleId = (int) $firstRole->id;
        }
    }

    public function selectRole(int $roleId): void
    {
        Gate::authorize('manage', Role::class);
        $this->activeRoleId = $roleId;
    }

    public function togglePermission(RolePermissionService $service, string $permissionName): void
    {
        Gate::authorize('manage', Role::class);

        $role = Role::findOrFail($this->activeRoleId);

        if ($role->name === RoleEnum::SuperAdmin->value) {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Không thể chỉnh sửa!',
                'text' => 'Vai trò Super Admin luôn có tất cả các quyền hạn.',
            ]);

            return;
        }

        $service->togglePermission($role, $permissionName);

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Cập nhật thành công!',
            'text' => 'Đã cập nhật quyền '.$permissionName.' cho vai trò '.$role->name,
            'toast' => true,
            'position' => 'top-end',
            'timer' => 3000,
        ]);
    }

    public function createRole(RolePermissionService $service): void
    {
        Gate::authorize('manage', Role::class);

        $this->validate([
            'newRoleName' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'newRoleDescription' => ['nullable', 'string', 'max:500'],
            'newRoleDepartmentId' => ['nullable', 'integer', 'exists:departments,id'],
        ]);

        $dto = RoleDTO::fromArray([
            'name' => $this->newRoleName,
            'description' => $this->newRoleDescription,
            'department_id' => $this->newRoleDepartmentId,
        ]);

        $role = $service->createRole($dto);

        $this->activeRoleId = (int) $role->id;
        $this->newRoleName = '';
        $this->newRoleDescription = '';
        $this->newRoleDepartmentId = null;

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Tạo thành công!',
            'text' => 'Đã thêm vai trò mới '.$role->name,
        ]);

        $this->dispatch('role-create:hide');
    }

    public function deleteRole(RolePermissionService $service, int $roleId): void
    {
        Gate::authorize('manage', Role::class);

        $role = Role::findOrFail($roleId);

        if (RoleEnum::isSystemRole($role->name)) {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Không thể xóa!',
                'text' => 'Vai trò hệ thống không thể bị xóa.',
            ]);

            return;
        }

        $service->deleteRole($role);

        $firstRole = Role::query()->orderBy('id')->first();
        $this->activeRoleId = $firstRole ? (int) $firstRole->id : 0;

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Đã xóa!',
            'text' => 'Đã xóa vai trò '.$role->name.' thành công.',
        ]);
    }

    public function openEditModal(int $roleId): void
    {
        Gate::authorize('manage', Role::class);

        $role = Role::findOrFail($roleId);

        $this->editingRoleId = $roleId;
        $this->editRoleName = $role->name;
        $this->editRoleDescription = $role->description ?? '';
        $this->editRoleDepartmentId = $role->department_id;
        $this->resetValidation(['editRoleName', 'editRoleDescription', 'editRoleDepartmentId']);

        $this->dispatch('role-edit:show');
    }

    public function updateRole(RolePermissionService $service): void
    {
        Gate::authorize('manage', Role::class);

        $this->validate([
            'editRoleName' => ['required', 'string', 'max:255', 'unique:roles,name,'.$this->editingRoleId],
            'editRoleDescription' => ['nullable', 'string', 'max:500'],
            'editRoleDepartmentId' => ['nullable', 'integer', 'exists:departments,id'],
        ]);

        $role = Role::findOrFail($this->editingRoleId);

        if (RoleEnum::isSystemRole($role->name) && $role->name !== $this->editRoleName) {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Không thể chỉnh sửa!',
                'text' => 'Vai trò hệ thống không thể đổi tên.',
            ]);

            return;
        }

        $dto = RoleDTO::fromArray([
            'name' => $this->editRoleName,
            'description' => $this->editRoleDescription,
            'department_id' => $this->editRoleDepartmentId,
        ]);

        $service->updateRole($role, $dto);

        $this->dispatch('role-edit:hide');

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Cập nhật thành công!',
            'text' => 'Đã cập nhật thông tin vai trò.',
            'toast' => true,
            'position' => 'top-end',
            'timer' => 3000,
        ]);
    }

    public function render(RolePermissionService $service): View
    {
        $roles = $service->getRoles();
        $activeRole = $roles->firstWhere('id', $this->activeRoleId) ?? $roles->first();

        $permissionsGrouped = [
            'Quản lý người dùng' => [
                PermissionEnum::UserView->value => 'Xem danh sách người dùng',
                PermissionEnum::UserCreate->value => 'Thêm mới người dùng',
                PermissionEnum::UserUpdate->value => 'Cập nhật người dùng',
                PermissionEnum::UserDelete->value => 'Xóa người dùng',
            ],
            'Lịch công tác' => [
                PermissionEnum::ScheduleView->value => 'Xem lịch công tác',
                PermissionEnum::ScheduleCreate->value => 'Tạo lịch công tác',
                PermissionEnum::ScheduleUpdate->value => 'Cập nhật lịch công tác',
                PermissionEnum::ScheduleDelete->value => 'Xóa lịch công tác',
                PermissionEnum::ScheduleViewPrivate->value => 'Xem lịch công tác riêng tư',
            ],
            'Báo cáo ngày' => [
                PermissionEnum::ReportView->value => 'Xem báo cáo ngày',
                PermissionEnum::ReportCreate->value => 'Tạo báo cáo ngày',
                PermissionEnum::ReportUpdate->value => 'Sửa báo cáo ngày',
                PermissionEnum::ReportDelete->value => 'Xóa báo cáo ngày',
            ],
            'Email nội bộ' => [
                PermissionEnum::MailView->value => 'Xem hộp thư nội bộ',
                PermissionEnum::MailSend->value => 'Gửi email nội bộ',
                PermissionEnum::MailUpdate->value => 'Cập nhật cấu hình email',
            ],
            'Quy định tài liệu' => [
                PermissionEnum::DocumentView->value => 'Xem quy định tài liệu',
                PermissionEnum::DocumentManage->value => 'Quản lý quy định tài liệu',
            ],
            'Cài đặt hệ thống' => [
                PermissionEnum::SettingView->value => 'Xem cài đặt hệ thống',
                PermissionEnum::SettingUpdate->value => 'Cập nhật cài đặt hệ thống',
            ],
            'Bảo mật & Phân quyền' => [
                PermissionEnum::RoleManage->value => 'Quản lý vai trò và phân quyền',
            ],
            'Truy cập chung' => [
                PermissionEnum::DashboardView->value => 'Truy cập Dashboard chính',
            ],
            'Khách hàng' => [
                PermissionEnum::CustomerView->value => 'Xem khách hàng',
                PermissionEnum::CustomerManage->value => 'Quản lý khách hàng',
            ],
            'Theo dõi báo giá' => [
                PermissionEnum::QuotationView->value => 'Xem báo giá',
                PermissionEnum::QuotationCreate->value => 'Tạo báo giá',
                PermissionEnum::QuotationUpdate->value => 'Cập nhật báo giá',
                PermissionEnum::QuotationSend->value => 'Gửi báo giá',
                PermissionEnum::QuotationConvert->value => 'Chuyển báo giá thành hợp đồng',
            ],
            'Hợp đồng' => [
                PermissionEnum::ContractView->value => 'Xem hợp đồng',
                PermissionEnum::ContractCreate->value => 'Tạo hợp đồng',
                PermissionEnum::ContractUpdate->value => 'Cập nhật hợp đồng',
                PermissionEnum::ContractApprove->value => 'Phê duyệt hợp đồng',
                PermissionEnum::ContractActivate->value => 'Kích hoạt hợp đồng',
                PermissionEnum::ContractComplete->value => 'Hoàn thành hợp đồng',
                PermissionEnum::ContractCancel->value => 'Hủy hợp đồng',
            ],
            'Lịch thanh toán & Chứng từ' => [
                PermissionEnum::PaymentScheduleView->value => 'Xem lịch thanh toán',
                PermissionEnum::PaymentScheduleManage->value => 'Quản lý lịch thanh toán',
                PermissionEnum::PaymentScheduleConfirm->value => 'Xác nhận lịch thanh toán',
                PermissionEnum::PaymentRecord->value => 'Ghi nhận thanh toán',
                PermissionEnum::PaymentAdjust->value => 'Điều chỉnh thanh toán',
                PermissionEnum::ContractDocumentView->value => 'Xem chứng từ hợp đồng',
                PermissionEnum::ContractDocumentSubmit->value => 'Gửi chứng từ hợp đồng',
                PermissionEnum::ContractDocumentReview->value => 'Kiểm tra chứng từ hợp đồng',
            ],
            'Kinh doanh & Báo cáo' => [
                PermissionEnum::BusinessDashboardView->value => 'Xem dashboard kinh doanh',
                PermissionEnum::AccountingDashboardView->value => 'Xem dashboard kế toán',
                PermissionEnum::ManagementDashboardView->value => 'Xem dashboard quản trị',
                PermissionEnum::SalesReportView->value => 'Xem báo cáo kinh doanh',
                PermissionEnum::SalesTargetManage->value => 'Thiết lập KPI kinh doanh',
                PermissionEnum::CommissionView->value => 'Xem yêu cầu chi hoa hồng',
                PermissionEnum::CommissionCreate->value => 'Tạo yêu cầu chi hoa hồng',
                PermissionEnum::CommissionUpdate->value => 'Cập nhật yêu cầu chi hoa hồng',
                PermissionEnum::CommissionDelete->value => 'Xóa yêu cầu chi hoa hồng',
                PermissionEnum::CommissionApprove->value => 'Duyệt yêu cầu chi hoa hồng',
                PermissionEnum::CommissionPay->value => 'Xác nhận đã chi hoa hồng',
            ],
        ];

        $activeRolePermissions = $activeRole ? $activeRole->permissions->pluck('name')->toArray() : [];
        $availableDepartments = Department::orderBy('name')->get();

        return view('livewire.roles-permissions.roles-permissions-index', [
            'roles' => $roles,
            'activeRole' => $activeRole,
            'permissionsGrouped' => $permissionsGrouped,
            'activeRolePermissions' => $activeRolePermissions,
            'availableDepartments' => $availableDepartments,
        ]);
    }
}
