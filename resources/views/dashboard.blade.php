@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v=1.0.1">
    <link rel="stylesheet" href="{{ asset('css/sales.css') }}?v=1.2.0">
@endpush

@section('content')
    <div class="dashboard-page">
    <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between">
        <div class="clearfix">
            <h1 class="app-page-title">Dashboard</h1>
            <span class="sales-supporting-text">
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

    @if ($commerce !== null)
        <section class="sales-page mb-4" aria-labelledby="commerceDashboardTitle">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
                <div>
                    <div class="sales-eyebrow text-uppercase fw-bold mb-1">Kinh doanh & dòng tiền</div>
                    <h2 id="commerceDashboardTitle" class="h5 mb-1">
                        @if ($selectedMonth === null)
                            Tổng quan thương mại năm {{ $selectedYear }}
                        @else
                            Tổng quan thương mại tháng {{ $selectedMonth }} / {{ $selectedYear }}
                        @endif
                    </h2>
                    <div class="sales-supporting-text">Dữ liệu hợp đồng ký, KPI, tiền thực nhận và cơ hội đang theo dõi.</div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @can('quotation.view')
                        <a href="{{ route('quotations.index') }}" class="btn btn-light sales-action-button">Báo giá</a>
                    @endcan
                    @can('contract.view')
                        <a href="{{ route('contracts.index') }}" class="btn btn-light sales-action-button">Hợp đồng</a>
                    @endcan
                    @can('sales-report.view')
                        <a href="{{ route('business-reports.index') }}" class="btn btn-primary sales-action-button">Xem báo cáo</a>
                    @endcan
                </div>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="card border-0 shadow-sm mb-4">
                <div class="card-body py-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-3">
                            <label for="filterYear" class="form-label small fw-semibold mb-1">Năm</label>
                            <select id="filterYear" name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                                @foreach (range(now()->year - 2, now()->year + 1) as $y)
                                    <option value="{{ $y }}" @selected($selectedYear === $y)>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="filterMonth" class="form-label small fw-semibold mb-1">Tháng</label>
                            <select id="filterMonth" name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="all" @selected($selectedMonth === null)>Tất cả các tháng</option>
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" @selected($selectedMonth === $m)>Tháng {{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canChooseOwner)
                            <div class="col-12 col-md-4">
                                <label for="filterOwner" class="form-label small fw-semibold mb-1">Nhân viên kinh doanh</label>
                                <select id="filterOwner" name="owner_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="" @selected($selectedOwnerId === null)>Tất cả nhân viên</option>
                                    @foreach ($salesUsers as $salesUser)
                                        <option value="{{ $salesUser->id }}" @selected($selectedOwnerId === $salesUser->id)>{{ $salesUser->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-12 col-md-2 d-md-flex justify-content-end">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm w-100 w-md-auto py-1">Đặt lại bộ lọc</a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row g-3">
                <div class="col-6 col-xl-3">
                    <div class="card border-0 shadow-sm sales-kpi-card h-100">
                        <div class="card-body">
                            <div class="sales-kpi-label">Doanh số ký</div>
                            <div class="sales-kpi-value sales-money">{{ number_format($commerce['signed_value'], 0, ',', '.') }}₫</div>
                            <div class="sales-supporting-text small">{{ $commerce['contract_count'] }} hợp đồng trong kỳ</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card border-0 shadow-sm sales-kpi-card h-100">
                        <div class="card-body">
                            <div class="sales-kpi-label">Hoàn thành KPI</div>
                            <div class="sales-kpi-value">{{ number_format($commerce['target_percent'], 1, ',', '.') }}%</div>
                            <div class="sales-supporting-text small">Mục tiêu {{ number_format($commerce['target'], 0, ',', '.') }}₫</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card border-0 shadow-sm sales-kpi-card h-100">
                        <div class="card-body">
                            <div class="sales-kpi-label">Tiền thực nhận</div>
                            <div class="sales-kpi-value sales-money text-success">{{ number_format($commerce['collected'], 0, ',', '.') }}₫</div>
                            <div class="sales-supporting-text small">Còn phải thu {{ number_format($commerce['outstanding'], 0, ',', '.') }}₫</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card border-0 shadow-sm sales-kpi-card h-100">
                        <div class="card-body">
                            <div class="sales-kpi-label">Cơ hội</div>
                            <div class="sales-kpi-value sales-money">{{ number_format($commerce['pipeline'], 0, ',', '.') }}₫</div>
                            <div class="sales-supporting-text small">Tỷ lệ thắng {{ number_format($commerce['conversion_rate'], 1, ',', '.') }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-1">
                <!-- Cơ cấu hợp đồng theo dịch vụ -->
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h2 class="h6 mb-1">Cơ cấu hợp đồng theo dịch vụ</h2>
                            <div class="sales-supporting-text small">Phân bố doanh số ký theo từng loại dịch vụ.</div>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="w-100">
                                <div id="dashboardServiceStructureChart"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tỉ lệ doanh số theo nguồn thông tin -->
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h2 class="h6 mb-1">Tỉ lệ doanh số theo nguồn thông tin</h2>
                            <div class="sales-supporting-text small">Phân bổ đóng góp doanh số theo kênh thông tin tiếp cận.</div>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="w-100">
                                <div id="dashboardSalesSourceChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-1">
                <!-- Tỉ lệ chuyển đổi dịch vụ -->
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h2 class="h6 mb-1">Tỉ lệ chuyển đổi Dịch vụ: báo giá vs ký hợp đồng</h2>
                            <div class="sales-supporting-text small">So sánh số lượng Báo giá và Hợp đồng thực tế của mỗi dịch vụ.</div>
                        </div>
                        <div class="card-body">
                            <div class="w-100">
                                <div id="dashboardServiceConversionChart"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Phân tích theo Khu vực -->
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h2 class="h6 mb-1">Phân tích theo Khu vực</h2>
                            <div class="sales-supporting-text small">Báo giá, hợp đồng và doanh số phân bổ theo tỉnh/thành.</div>
                        </div>
                        <div class="card-body">
                            <div class="w-100">
                                <div id="dashboardRegionalChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

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
                        <small class="sales-supporting-text">Theo dõi lịch công tác và báo cáo nội bộ trong ngày.</small>
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
                                        <small class="sales-supporting-text">{{ $reportStatus['submitted'] }} đã nộp, {{ $reportStatus['missing'] }} chưa nộp</small>
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
                                            <i class="fi fi-rr-calendar"></i>
                                        </span>
                                        <span class="flex-grow-1 min-w-0">
                                            <span class="d-flex align-items-center justify-content-between gap-2">
                                                <strong class="text-truncate min-w-0">
                                                    @if ($schedule['is_private'])
                                                        <i class="fi fi-rr-lock me-1"></i>
                                                    @endif
                                                    {{ $schedule['title'] }}
                                                </strong>
                                                <small class="sales-supporting-text text-nowrap">{{ $schedule['time'] }}</small>
                                            </span>
                                            <small class="sales-supporting-text d-block text-truncate">
                                                {{ $schedule['creator'] }}{{ $schedule['location'] ? ' · '.$schedule['location'] : '' }}
                                            </small>
                                        </span>
                                    </a>
                                @empty
                                    <div class="text-center sales-supporting-text py-4">
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
                                                <strong class="text-truncate min-w-0">{{ $report['user'] }}</strong>
                                                <small class="sales-supporting-text text-nowrap">{{ $report['date'] }}</small>
                                            </span>
                                            <small class="sales-supporting-text d-block text-truncate">{{ $report['summary'] }}</small>
                                        </span>
                                    </a>
                                @empty
                                    <div class="text-center sales-supporting-text py-4">
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
                        @can('document.view')
                            <div class="col-6">
                                <a href="{{ route('document-regulations.index') }}" class="btn btn-light w-100 text-start waves-effect">
                                    <i class="fi fi-rr-document-signed text-info me-2"></i> Tài liệu
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
                                    <small class="sales-supporting-text">{{ $role['users_count'] }} người</small>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar" style="width: {{ $role['percent'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center sales-supporting-text py-4">
                                Chưa có vai trò nào.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endcan
        </div>
    </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const serviceStructureEl = document.querySelector("#dashboardServiceStructureChart");
            if (serviceStructureEl) {
                const data = @json($contractServicesStructure ?? []);
                const options = {
                    series: data.map(item => item.value || 0),
                    labels: data.map(item => item.label || ''),
                    chart: {
                        type: 'donut',
                        height: 320,
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    colors: [
                        '#0d6efd',
                        '#198754',
                        '#0dcaf0',
                        '#ffc107',
                        '#dc3545',
                        '#6f42c1',
                        '#fd7e14',
                        '#20c997'
                    ],
                    dataLabels: { enabled: false },
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        labels: { colors: '#212529' }
                    },
                    tooltip: {
                        y: {
                            formatter: (val) => new Intl.NumberFormat('vi-VN').format(val) + 'đ'
                        }
                    }
                };
                new ApexCharts(serviceStructureEl, options).render();
            }

            const salesSourceEl = document.querySelector("#dashboardSalesSourceChart");
            if (salesSourceEl) {
                const data = @json($salesBySource ?? []);
                const options = {
                    series: data.map(item => item.value || 0),
                    labels: data.map(item => item.label || ''),
                    chart: {
                        type: 'donut',
                        height: 320,
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    colors: [
                        '#0d6efd',
                        '#198754',
                        '#0dcaf0',
                        '#ffc107',
                        '#dc3545',
                        '#6f42c1',
                        '#fd7e14',
                        '#20c997'
                    ],
                    dataLabels: { enabled: false },
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        labels: { colors: '#212529' }
                    },
                    tooltip: {
                        y: {
                            formatter: (val) => new Intl.NumberFormat('vi-VN').format(val) + 'đ'
                        }
                    }
                };
                new ApexCharts(salesSourceEl, options).render();
            }

            const serviceConversionEl = document.querySelector("#dashboardServiceConversionChart");
            if (serviceConversionEl) {
                const data = @json($serviceConversionRates ?? []);
                const options = {
                    series: [
                        { name: 'Báo giá', data: data.map(item => item.quotations_count || 0) },
                        { name: 'Hợp đồng', data: data.map(item => item.contracts_count || 0) }
                    ],
                    chart: {
                        type: 'bar',
                        height: 320,
                        fontFamily: 'Plus Jakarta Sans, sans-serif',
                        toolbar: { show: false }
                    },
                    colors: ['#6c757d', '#198754'],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            dataLabels: { position: 'top' }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        offsetX: -6,
                        style: {
                            fontSize: '11px',
                            colors: ['#fff']
                        }
                    },
                    xaxis: {
                        categories: data.map(item => item.label || ''),
                        labels: {
                            formatter: (val) => Number.isInteger(val) ? val : '',
                            style: { colors: '#4b5563' }
                        }
                    },
                    yaxis: {
                        labels: { style: { colors: '#4b5563' } }
                    },
                    legend: {
                        position: 'top',
                        labels: { colors: '#212529' }
                    },
                    tooltip: {
                        shared: true,
                        intersect: false
                    }
                };
                new ApexCharts(serviceConversionEl, options).render();
            }

            const regionalEl = document.querySelector("#dashboardRegionalChart");
            if (regionalEl) {
                const data = @json($regionalBreakdown ?? []);
                const options = {
                    series: [
                        { name: 'Báo giá', type: 'column', data: data.map(item => item.quotations_count || 0) },
                        { name: 'Ký hợp đồng', type: 'column', data: data.map(item => item.contracts_count || 0) },
                        { name: 'Doanh số (HĐ)', type: 'column', data: data.map(item => item.sales_value || 0) }
                    ],
                    chart: {
                        type: 'line',
                        height: 320,
                        fontFamily: 'Plus Jakarta Sans, sans-serif',
                        toolbar: { show: false }
                    },
                    stroke: {
                        width: [0, 0, 0]
                    },
                    colors: ['#ffc107', '#0d6efd', '#198754'],
                    plotOptions: {
                        bar: {
                            columnWidth: '60%',
                            borderRadius: 3
                        }
                    },
                    xaxis: {
                        categories: data.map(item => item.province || 'Chưa xác định'),
                        labels: { style: { colors: '#4b5563' } }
                    },
                    yaxis: [
                        {
                            title: { text: 'Số lượng hồ sơ' },
                            labels: {
                                formatter: (val) => Number.isInteger(val) ? val : '',
                                style: { colors: '#4b5563' }
                            }
                        },
                        {
                            show: false,
                            title: { text: 'Số lượng hồ sơ' },
                            labels: {
                                formatter: (val) => Number.isInteger(val) ? val : '',
                                style: { colors: '#4b5563' }
                            }
                        },
                        {
                            opposite: true,
                            title: { text: 'Doanh số (VND)' },
                            labels: {
                                formatter: (val) => new Intl.NumberFormat('vi-VN').format(val) + 'đ',
                                style: { colors: '#4b5563' }
                            }
                        }
                    ],
                    legend: {
                        position: 'top',
                        labels: { colors: '#212529' }
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        y: {
                            formatter: (val, opts) => {
                                if (opts.seriesIndex === 2) {
                                    return new Intl.NumberFormat('vi-VN').format(val) + 'đ';
                                }
                                return val;
                              }
                        }
                    }
                };
                new ApexCharts(regionalEl, options).render();
            }
        });
    </script>
    @endpush
@endsection
