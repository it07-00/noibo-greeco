@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v=1.0.1">
@endpush

@section('content')
    <div class="dashboard-page">
    <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between">
        <div class="clearfix">
            <h1 class="app-page-title">Dashboard</h1>
            <span class="text-muted">
                <i class="fi fi-rr-calendar me-1"></i>{{ now()->format('d/m/Y') }}
            </span>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            @can('schedule.create')
                <a href="{{ route('duty-schedules.index') }}" class="btn btn-outline-primary waves-effect">
                    <i class="fi fi-rr-calendar-plus me-1"></i> Tạo lịch công tác
                </a>
            @endcan
            @can('report.create')
                <a href="{{ route('daily-reports.index') }}" class="btn btn-primary waves-effect waves-light">
                    <i class="fi fi-rr-document-signed me-1"></i> Viết báo cáo
                </a>
            @endcan
        </div>
    </div>

    <div class="row g-3 dashboard-stat-row">
        @can('user.view')
            <div class="col-6 col-md-4 col-xl">
                <div class="card dashboard-stat-card bg-secondary bg-opacity-05 shadow-none border-0 h-100 transition-hover">
                    <div class="card-body">
                        <div class="avatar bg-secondary shadow-secondary rounded-circle text-white mb-3">
                            <i class="fi fi-sr-users"></i>
                        </div>
                        <h3 class="mb-1">{{ $stats['users'] }}</h3>
                        <h6 class="mb-0">Thành viên</h6>
                        <small class="fw-medium text-success">
                            <i class="fi fi-rr-arrow-small-up scale-3x"></i> +{{ $stats['new_users_month'] }} trong tháng
                        </small>
                    </div>
                </div>
            </div>
        @endcan

        @can('role.manage')
            <div class="col-6 col-md-4 col-xl">
                <div class="card dashboard-stat-card bg-primary bg-opacity-05 shadow-none border-0 h-100 transition-hover">
                    <div class="card-body">
                        <div class="avatar bg-primary shadow-primary rounded-circle text-white mb-3">
                            <i class="fi fi-rr-shield-check"></i>
                        </div>
                        <h3 class="mb-1">{{ $stats['roles'] }}</h3>
                        <h6 class="mb-0">Vai trò</h6>
                        <small class="fw-medium text-primary">{{ $stats['permissions'] }} quyền hệ thống</small>
                    </div>
                </div>
            </div>
        @endcan

        @can('schedule.view')
            <div class="col-6 col-md-4 col-xl">
                <div class="card dashboard-stat-card bg-warning bg-opacity-05 shadow-none border-0 h-100 transition-hover">
                    <div class="card-body">
                        <div class="avatar bg-warning shadow-warning rounded-circle text-white mb-3">
                            <i class="fi fi-rr-calendar-clock"></i>
                        </div>
                        <h3 class="mb-1">{{ $stats['schedules_today'] }}</h3>
                        <h6 class="mb-0">Lịch hôm nay</h6>
                        <small class="fw-medium text-warning">{{ $stats['schedules_week'] }} lịch trong tuần</small>
                    </div>
                </div>
            </div>
        @endcan

        @can('report.view')
            <div class="col-6 col-md-4 col-xl">
                <div class="card dashboard-stat-card bg-info bg-opacity-05 shadow-none border-0 h-100 transition-hover">
                    <div class="card-body">
                        <div class="avatar bg-info shadow-info rounded-circle text-white mb-3">
                            <i class="fi fi-rr-document-signed"></i>
                        </div>
                        <h3 class="mb-1">{{ $stats['reports_today'] }}</h3>
                        <h6 class="mb-0">Báo cáo hôm nay</h6>
                        <small class="fw-medium text-info">{{ $stats['reports_month'] }} báo cáo trong tháng</small>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    <div class="row g-4 mt-1 dashboard-main-row">
        <div class="col-xxl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 pb-0 d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Tổng quan vận hành</h5>
                        <small class="text-muted">Theo dõi lịch công tác và báo cáo nội bộ trong ngày.</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">GREECO Office</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @can('schedule.view')
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light bg-opacity-50 h-100">
                                    <div class="avatar bg-warning rounded-circle text-white">
                                        <i class="fi fi-rr-calendar-day"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <h6 class="mb-0">Lịch tuần này</h6>
                                            <strong>{{ $stats['schedules_week'] }}</strong>
                                        </div>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-warning" style="width: {{ min($stats['schedules_week'] * 10, 100) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endcan

                        @can('report.view')
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light bg-opacity-50 h-100">
                                    <div class="avatar bg-success rounded-circle text-white">
                                        <i class="fi fi-rr-chart-histogram"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <h6 class="mb-0">Tỷ lệ báo cáo hôm nay</h6>
                                            <strong>{{ $reportStatus['percent'] }}%</strong>
                                        </div>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-success" style="width: {{ $reportStatus['percent'] }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $reportStatus['submitted'] }} đã nộp, {{ $reportStatus['missing'] }} chưa nộp</small>
                                    </div>
                                </div>
                            </div>
                        @endcan
                    </div>

                    <div class="row g-4 mt-1">
                        @can('schedule.view')
                            <div class="col-lg-6">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="mb-0">Lịch công tác sắp tới</h6>
                                    <a href="{{ route('duty-schedules.index') }}" class="small fw-semibold">Xem tất cả</a>
                                </div>

                                @forelse ($recentSchedules as $schedule)
                                    <a href="{{ route('duty-schedules.index') }}" class="d-flex align-items-start gap-3 p-3 rounded-3 bg-light bg-opacity-50 mb-2 text-body transition-hover">
                                        <span class="avatar avatar-sm bg-{{ $schedule['color'] }}-subtle text-{{ $schedule['color'] }} rounded-circle">
                                            <i class="fi fi-rr-calendar-event"></i>
                                        </span>
                                        <span class="flex-grow-1 min-w-0">
                                            <span class="d-flex align-items-center justify-content-between gap-2">
                                                <strong class="text-truncate">
                                                    @if ($schedule['is_private'])
                                                        <i class="fi fi-rr-lock me-1"></i>
                                                    @endif
                                                    {{ $schedule['title'] }}
                                                </strong>
                                                <small class="text-muted text-nowrap">{{ $schedule['time'] }}</small>
                                            </span>
                                            <small class="text-muted d-block text-truncate">
                                                {{ $schedule['creator'] }}{{ $schedule['location'] ? ' · '.$schedule['location'] : '' }}
                                            </small>
                                        </span>
                                    </a>
                                @empty
                                    <div class="text-center text-muted py-4">
                                        <i class="fi fi-rr-calendar-slash display-6 d-block mb-2"></i>
                                        Chưa có lịch công tác sắp tới.
                                    </div>
                                @endforelse
                            </div>
                        @endcan

                        @can('report.view')
                            <div class="col-lg-6">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="mb-0">Báo cáo mới nhất</h6>
                                    <a href="{{ route('daily-reports.index') }}" class="small fw-semibold text-success">Xem tất cả</a>
                                </div>

                                @forelse ($recentReports as $report)
                                    <a href="{{ route('daily-reports.index') }}" class="d-flex align-items-start gap-3 p-3 rounded-3 bg-light bg-opacity-50 mb-2 text-body transition-hover">
                                        <span class="avatar avatar-sm bg-success-subtle text-success rounded-circle">
                                            <i class="fi fi-rr-document-signed"></i>
                                        </span>
                                        <span class="flex-grow-1 min-w-0">
                                            <span class="d-flex align-items-center justify-content-between gap-2">
                                                <strong class="text-truncate">{{ $report['user'] }}</strong>
                                                <small class="text-muted text-nowrap">{{ $report['date'] }}</small>
                                            </span>
                                            <small class="text-muted d-block text-truncate">{{ $report['summary'] }}</small>
                                        </span>
                                    </a>
                                @empty
                                    <div class="text-center text-muted py-4">
                                        <i class="fi fi-rr-document display-6 d-block mb-2"></i>
                                        Chưa có báo cáo nào.
                                    </div>
                                @endforelse
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4">
            <div class="card dashboard-side-card border-0 shadow-sm mb-4">
                <div class="card-header border-0 pb-0">
                    <h5 class="card-title mb-0">Thao tác nhanh</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @can('user.view')
                            <div class="col-6">
                                <a href="{{ route('users.index') }}" class="btn btn-light w-100 text-start waves-effect">
                                    <i class="fi fi-rr-users text-primary me-2"></i> Nhân sự
                                </a>
                            </div>
                        @endcan
                        @can('schedule.view')
                            <div class="col-6">
                                <a href="{{ route('duty-schedules.index') }}" class="btn btn-light w-100 text-start waves-effect">
                                    <i class="fi fi-rr-calendar text-warning me-2"></i> Lịch
                                </a>
                            </div>
                        @endcan
                        @can('report.view')
                            <div class="col-6">
                                <a href="{{ route('daily-reports.index') }}" class="btn btn-light w-100 text-start waves-effect">
                                    <i class="fi fi-rr-document text-success me-2"></i> Báo cáo
                                </a>
                            </div>
                        @endcan
                        @can('role.manage')
                            <div class="col-6">
                                <a href="{{ route('roles-permissions.index') }}" class="btn btn-light w-100 text-start waves-effect">
                                    <i class="fi fi-rr-shield-check text-danger me-2"></i> Quyền
                                </a>
                            </div>
                        @endcan
                        @can('setting.view')
                            <div class="col-12">
                                <a href="{{ route('settings.index') }}" class="btn btn-light w-100 text-start waves-effect">
                                    <i class="fi fi-rr-settings text-info me-2"></i> Thiết lập hệ thống
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>

            @can('role.manage')
                <div class="card dashboard-side-card border-0 shadow-sm">
                    <div class="card-header border-0 pb-0 d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">Phân bố vai trò</h5>
                        <span class="badge bg-primary-subtle text-primary">{{ $stats['users'] }} users</span>
                    </div>
                    <div class="card-body">
                        @forelse ($roleDistribution as $role)
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fw-semibold">{{ $role['name'] }}</span>
                                    <small class="text-muted">{{ $role['users_count'] }} người</small>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar" style="width: {{ $role['percent'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                Chưa có vai trò nào.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endcan
        </div>
    </div>
    </div>
@endsection
