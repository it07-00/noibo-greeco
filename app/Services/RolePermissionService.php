<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\RoleDTO;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

final class RolePermissionService
{
    /**
     * @var array<string, string>
     */
    private const array REQUIRED_PARENT_PERMISSIONS = [
        PermissionEnum::ContractViewAll->value => PermissionEnum::ContractView->value,
        PermissionEnum::ContractExport->value => PermissionEnum::ContractView->value,
        PermissionEnum::ContractAssign->value => PermissionEnum::ContractView->value,
        PermissionEnum::HandlerView->value => PermissionEnum::DashboardView->value,
        PermissionEnum::HandlerCreate->value => PermissionEnum::HandlerView->value,
        PermissionEnum::HandlerUpdate->value => PermissionEnum::HandlerView->value,
        PermissionEnum::HandlerDelete->value => PermissionEnum::HandlerView->value,
        PermissionEnum::UserView->value => PermissionEnum::DashboardView->value,
        PermissionEnum::UserCreate->value => PermissionEnum::UserView->value,
        PermissionEnum::UserUpdate->value => PermissionEnum::UserView->value,
        PermissionEnum::UserDelete->value => PermissionEnum::UserView->value,
        PermissionEnum::ScheduleView->value => PermissionEnum::DashboardView->value,
        PermissionEnum::ScheduleCreate->value => PermissionEnum::ScheduleView->value,
        PermissionEnum::ScheduleUpdate->value => PermissionEnum::ScheduleView->value,
        PermissionEnum::ScheduleDelete->value => PermissionEnum::ScheduleView->value,
        PermissionEnum::ReportView->value => PermissionEnum::DashboardView->value,
        PermissionEnum::ReportViewAll->value => PermissionEnum::ReportView->value,
        PermissionEnum::ReportCreate->value => PermissionEnum::ReportView->value,
        PermissionEnum::ReportUpdate->value => PermissionEnum::ReportView->value,
        PermissionEnum::ReportDelete->value => PermissionEnum::ReportView->value,
        PermissionEnum::MailView->value => PermissionEnum::DashboardView->value,
        PermissionEnum::MailSend->value => PermissionEnum::MailView->value,
        PermissionEnum::MailUpdate->value => PermissionEnum::MailView->value,
        PermissionEnum::DocumentView->value => PermissionEnum::DashboardView->value,
        PermissionEnum::SettingView->value => PermissionEnum::DashboardView->value,
        PermissionEnum::SettingUpdate->value => PermissionEnum::SettingView->value,
        PermissionEnum::RoleManage->value => PermissionEnum::DashboardView->value,
    ];

    /** @return array<string, array<string, string>> */
    public function permissionGroups(): array
    {
        $groups = [
            'Nhà thầu phụ' => [
                PermissionEnum::HandlerView->value => 'Xem nhà thầu phụ',
                PermissionEnum::HandlerCreate->value => 'Thêm nhà thầu phụ',
                PermissionEnum::HandlerUpdate->value => 'Sửa nhà thầu phụ',
                PermissionEnum::HandlerDelete->value => 'Xóa nhà thầu phụ',
            ],
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
                PermissionEnum::ReportViewAll->value => 'Xem tất cả báo cáo ngày',
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
            'Khóa học' => [
                PermissionEnum::CourseView->value => 'Xem khóa học và học viên',
                PermissionEnum::CourseManage->value => 'Tạo, sửa, xóa khóa học và xếp học viên',
            ],
            'Theo dõi báo giá' => [
                PermissionEnum::QuotationView->value => 'Xem báo giá',
                PermissionEnum::QuotationCreate->value => 'Tạo báo giá',
                PermissionEnum::QuotationUpdate->value => 'Cập nhật báo giá',
                PermissionEnum::QuotationSend->value => 'Gửi báo giá',
                PermissionEnum::QuotationConvert->value => 'Chuyển báo giá thành hợp đồng',
            ],
            'Hợp đồng' => [
                PermissionEnum::ContractViewAll->value => 'Xem toàn bộ hợp đồng',
                PermissionEnum::ContractExport->value => 'Xuất danh sách hợp đồng',
                PermissionEnum::ContractAssign->value => 'Giao việc hợp đồng',
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
            'Kế hoạch Marketing' => [
                PermissionEnum::MarketingPlanView->value => 'Xem kế hoạch Marketing',
                PermissionEnum::MarketingPlanCreate->value => 'Tạo kế hoạch Marketing',
                PermissionEnum::MarketingPlanUpdate->value => 'Cập nhật kế hoạch Marketing',
                PermissionEnum::MarketingPlanDelete->value => 'Xóa kế hoạch Marketing',
                PermissionEnum::MarketingPlanApprove->value => 'Phê duyệt kế hoạch Marketing',
            ],
        ];

        $catalogNames = array_merge(...array_map('array_keys', array_values($groups)));
        foreach (Permission::where('guard_name', 'web')->whereNotIn('name', $catalogNames)->get() as $permission) {
            $groups['Quyền khác'][$permission->name] = PermissionEnum::tryFrom($permission->name)?->label() ?? $permission->name;
        }

        return $groups;
    }

    /** @return Collection<int, Role> */
    public function getRoles(): Collection
    {
        return Role::query()
            ->with('department')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return Collection<int, Permission>
     */
    public function getPermissions(): Collection
    {
        return Permission::query()
            ->orderBy('id')
            ->get();
    }

    public function createRole(RoleDTO $dto): Role
    {
        return DB::transaction(function () use ($dto): Role {
            return Role::create([
                'name' => $dto->name,
                'description' => $dto->description,
                'department_id' => $dto->departmentId,
                'guard_name' => 'web',
            ]);
        });
    }

    public function updateRole(Role $role, RoleDTO $dto): Role
    {
        return DB::transaction(function () use ($role, $dto): Role {
            if (RoleEnum::isSystemRole($role->name) && $role->name !== $dto->name) {
                throw new \InvalidArgumentException('Không thể đổi tên vai trò hệ thống');
            }

            $role->update([
                'name' => $dto->name,
                'description' => $dto->description,
                'department_id' => $dto->departmentId,
            ]);

            return $role->refresh();
        });
    }

    public function deleteRole(Role $role): void
    {
        DB::transaction(function () use ($role): void {
            if (RoleEnum::isSystemRole($role->name)) {
                throw new \InvalidArgumentException('Không thể xóa vai trò hệ thống');
            }
            $role->delete();
        });
    }

    /**
     * @param  array<int, string>  $permissionNames
     */
    public function syncPermissions(Role $role, array $permissionNames): void
    {
        DB::transaction(function () use ($role, $permissionNames): void {
            // Super Admin should always have all permissions, protect it
            if ($role->name === RoleEnum::SuperAdmin->value) {
                $role->syncPermissions(Permission::all());

                return;
            }

            $role->syncPermissions($this->normalizePermissionNames($permissionNames));
        });
    }

    public function togglePermission(Role $role, string $permissionName): void
    {
        $permissions = $role->permissions->pluck('name')->all();

        if (in_array($permissionName, $permissions, true)) {
            $permissions = $this->removePermissionAndDependents($permissions, $permissionName);
        } else {
            $permissions[] = $permissionName;
        }

        $this->syncPermissions($role, $permissions);
    }

    /** @param list<string> $permissionNames */
    public function syncUserPermissions(User $user, array $permissionNames): void
    {
        abort_if($user->hasRole(RoleEnum::SuperAdmin->value), 403);

        DB::transaction(function () use ($user, $permissionNames): void {
            $permissions = $this->normalizePermissionNames($permissionNames);
            if ($permissions !== []) {
                $permissions[] = PermissionEnum::DashboardView->value;
            }

            $user->syncPermissions(array_values(array_unique($permissions)));
        });
    }

    /**
     * @param  array<int, string>  $permissionNames
     * @return list<string>
     */
    private function removePermissionAndDependents(array $permissionNames, string $permissionName): array
    {
        $permissions = array_values(array_diff($permissionNames, [$permissionName]));

        do {
            $changed = false;

            foreach (self::REQUIRED_PARENT_PERMISSIONS as $child => $parent) {
                if ($parent === $permissionName || (! in_array($parent, $permissions, true) && in_array($child, $permissions, true))) {
                    $before = $permissions;
                    $permissions = array_values(array_diff($permissions, [$child]));
                    $changed = $changed || $before !== $permissions;
                }
            }
        } while ($changed);

        return $permissions;
    }

    /**
     * @param  array<int, string>  $permissionNames
     * @return list<string>
     */
    private function normalizePermissionNames(array $permissionNames): array
    {
        $permissions = array_values(array_unique($permissionNames));

        do {
            $changed = false;

            foreach (self::REQUIRED_PARENT_PERMISSIONS as $child => $parent) {
                if (in_array($child, $permissions, true) && ! in_array($parent, $permissions, true)) {
                    $permissions[] = $parent;
                    $changed = true;
                }
            }
        } while ($changed);

        do {
            $changed = false;

            foreach (self::REQUIRED_PARENT_PERMISSIONS as $child => $parent) {
                if (! in_array($parent, $permissions, true) && in_array($child, $permissions, true)) {
                    $permissions = array_values(array_diff($permissions, [$child]));
                    $changed = true;
                }
            }
        } while ($changed);

        return array_values(array_unique($permissions));
    }
}
