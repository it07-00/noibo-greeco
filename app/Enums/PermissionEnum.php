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
    case DocumentManage = 'document.manage';
    case RoleManage = 'role.manage';
    case ReportView = 'report.view';
    case ReportCreate = 'report.create';
    case ReportUpdate = 'report.update';
    case ReportDelete = 'report.delete';
    case ScheduleViewPrivate = 'schedule.view-private';
    case CustomerView = 'customer.view';
    case CustomerManage = 'customer.manage';
    case QuotationView = 'quotation.view';
    case QuotationCreate = 'quotation.create';
    case QuotationUpdate = 'quotation.update';
    case QuotationSend = 'quotation.send';
    case QuotationConvert = 'quotation.convert';
    case ContractView = 'contract.view';
    case ContractCreate = 'contract.create';
    case ContractUpdate = 'contract.update';
    case ContractApprove = 'contract.approve';
    case ContractActivate = 'contract.activate';
    case ContractComplete = 'contract.complete';
    case ContractCancel = 'contract.cancel';
    case PaymentScheduleView = 'payment-schedule.view';
    case PaymentScheduleManage = 'payment-schedule.manage';
    case PaymentScheduleConfirm = 'payment-schedule.confirm';
    case PaymentRecord = 'payment.record';
    case PaymentAdjust = 'payment.adjust';
    case ContractDocumentView = 'contract-document.view';
    case ContractDocumentSubmit = 'contract-document.submit';
    case ContractDocumentReview = 'contract-document.review';
    case BusinessDashboardView = 'business-dashboard.view';
    case AccountingDashboardView = 'accounting-dashboard.view';
    case ManagementDashboardView = 'management-dashboard.view';
    case SalesReportView = 'sales-report.view';
    case SalesTargetManage = 'sales-target.manage';
    case CommissionView = 'commission.view';
    case CommissionCreate = 'commission.create';
    case CommissionUpdate = 'commission.update';
    case CommissionDelete = 'commission.delete';
    case CommissionApprove = 'commission.approve';
    case CommissionPay = 'commission.pay';
    case SalesReportViewAll = 'sales-report.view-all';
    case CashFlowView = 'cash-flow.view';
    case CashFlowExport = 'cash-flow.export';

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
            self::ScheduleViewPrivate => 'Xem lịch công tác riêng tư',
            self::SettingView => 'Xem cài đặt hệ thống',
            self::SettingUpdate => 'Cập nhật cài đặt hệ thống',
            self::MailView => 'Xem hộp thư nội bộ',
            self::MailSend => 'Gửi email nội bộ',
            self::MailUpdate => 'Cập nhật cấu hình email',
            self::DocumentView => 'Xem quy định tài liệu',
            self::DocumentManage => 'Quản lý quy định tài liệu',
            self::RoleManage => 'Quản lý vai trò và phân quyền',
            self::ReportView => 'Xem báo cáo ngày',
            self::ReportCreate => 'Tạo báo cáo ngày',
            self::ReportUpdate => 'Sửa báo cáo ngày',
            self::ReportDelete => 'Xóa báo cáo ngày',
            self::CustomerView => 'Xem khách hàng',
            self::CustomerManage => 'Quản lý khách hàng',
            self::QuotationView => 'Xem báo giá',
            self::QuotationCreate => 'Tạo báo giá',
            self::QuotationUpdate => 'Cập nhật báo giá',
            self::QuotationSend => 'Gửi báo giá',
            self::QuotationConvert => 'Chuyển báo giá thành hợp đồng',
            self::ContractView => 'Xem hợp đồng',
            self::ContractCreate => 'Tạo hợp đồng',
            self::ContractUpdate => 'Cập nhật hợp đồng',
            self::ContractApprove => 'Phê duyệt hợp đồng',
            self::ContractActivate => 'Kích hoạt hợp đồng',
            self::ContractComplete => 'Hoàn thành hợp đồng',
            self::ContractCancel => 'Hủy hợp đồng',
            self::PaymentScheduleView => 'Xem lịch thanh toán',
            self::PaymentScheduleManage => 'Quản lý lịch thanh toán',
            self::PaymentScheduleConfirm => 'Xác nhận lịch thanh toán',
            self::PaymentRecord => 'Ghi nhận thanh toán',
            self::PaymentAdjust => 'Điều chỉnh thanh toán',
            self::ContractDocumentView => 'Xem chứng từ hợp đồng',
            self::ContractDocumentSubmit => 'Gửi chứng từ hợp đồng',
            self::ContractDocumentReview => 'Kiểm tra chứng từ hợp đồng',
            self::BusinessDashboardView => 'Xem dashboard kinh doanh',
            self::AccountingDashboardView => 'Xem dashboard kế toán',
            self::ManagementDashboardView => 'Xem dashboard quản trị',
            self::SalesReportView => 'Xem báo cáo kinh doanh',
            self::SalesTargetManage => 'Thiết lập KPI kinh doanh',
            self::CommissionView => 'Xem yêu cầu chi hoa hồng',
            self::CommissionCreate => 'Tạo yêu cầu chi hoa hồng',
            self::CommissionUpdate => 'Cập nhật yêu cầu chi hoa hồng',
            self::CommissionDelete => 'Xóa yêu cầu chi hoa hồng',
            self::CommissionApprove => 'Duyệt yêu cầu chi hoa hồng',
            self::CommissionPay => 'Xác nhận đã chi hoa hồng',
            self::SalesReportViewAll => 'Xem toàn bộ báo cáo kinh doanh phòng',
            self::CashFlowView => 'Xem báo cáo dòng tiền',
            self::CashFlowExport => 'Xuất Excel báo cáo dòng tiền',
        };
    }
}
