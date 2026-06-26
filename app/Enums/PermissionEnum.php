<?php

declare(strict_types=1);

namespace App\Enums;

enum PermissionEnum: string
{
    case DashboardView = 'dashboard.view';
    case UserView = 'user.view';
    case UserCreate = 'user.create';
    case UserUpdate = 'user.update';
    case UserDelete = 'user.delete';
    case ScheduleView = 'schedule.view';
    case ScheduleCreate = 'schedule.create';
    case ScheduleUpdate = 'schedule.update';
    case ScheduleDelete = 'schedule.delete';
    case SettingView = 'setting.view';
    case SettingUpdate = 'setting.update';
    case MailView = 'mail.view';
    case MailSend = 'mail.send';
    case MailUpdate = 'mail.update';
    case DocumentView = 'document.view';
    case RoleManage = 'role.manage';
    case ReportView = 'report.view';
    case ReportCreate = 'report.create';
    case ReportUpdate = 'report.update';
    case ReportDelete = 'report.delete';

    public function label(): string
    {
        return match ($this) {
            self::DashboardView => 'Dashboard View',
            self::UserView => 'User View',
            self::UserCreate => 'User Create',
            self::UserUpdate => 'User Update',
            self::UserDelete => 'User Delete',
            self::ScheduleView => 'Xem lịch công tác',
            self::ScheduleCreate => 'Tạo lịch công tác',
            self::ScheduleUpdate => 'Cập nhật lịch công tác',
            self::ScheduleDelete => 'Xóa lịch công tác',
            self::SettingView => 'Xem cài đặt hệ thống',
            self::SettingUpdate => 'Cập nhật cài đặt hệ thống',
            self::MailView => 'Xem hộp thư nội bộ',
            self::MailSend => 'Gửi email nội bộ',
            self::MailUpdate => 'Cập nhật cấu hình email',
            self::DocumentView => 'Xem quy định tài liệu',
            self::RoleManage => 'Quản lý vai trò và phân quyền',
            self::ReportView => 'Xem báo cáo ngày',
            self::ReportCreate => 'Tạo báo cáo ngày',
            self::ReportUpdate => 'Sửa báo cáo ngày',
            self::ReportDelete => 'Xóa báo cáo ngày',
        };
    }
}
