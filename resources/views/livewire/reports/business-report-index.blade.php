<div class="sales-page report-page py-4">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/sales.css') }}?v=1.3.0">
    @endpush

    <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-3 mb-4">
        <div>
            <div class="sales-eyebrow text-uppercase fw-bold mb-1">Kinh doanh & tài chính</div>
            <h1 class="h3 mb-1">Báo cáo doanh số</h1>
            <p class="sales-supporting-text mb-0">Theo dõi KPI, hợp đồng ký, dòng tiền, cơ hội và công nợ trên cùng một báo cáo.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-6 col-lg-3">
                    <label for="reportYear" class="form-label">Năm</label>
                    <select id="reportYear" class="form-select" wire:model.live="year">
                        @foreach (range((int) now()->year, (int) now()->year - 4) as $optionYear)
                            <option value="{{ $optionYear }}">{{ $optionYear }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-lg-3">
                    <label for="reportMonth" class="form-label">Tháng</label>
                    <select id="reportMonth" class="form-select" wire:model.live="month">
                        <option value="">Cả năm</option>
                        @foreach (range(1, 12) as $optionMonth)
                            <option value="{{ $optionMonth }}">Tháng {{ $optionMonth }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($canChooseOwner)
                    <div class="col-12 col-lg-4">
                        <label for="reportOwner" class="form-label">Nhân viên kinh doanh</label>
                        <select id="reportOwner" class="form-select" wire:model.live="ownerId">
                            <option value="">Toàn bộ phòng Kinh doanh</option>
                            @foreach ($salesUsers as $salesUser)
                                <option value="{{ $salesUser->id }}">{{ $salesUser->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>
    </div>



    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Doanh số ký</div>
                    <div class="sales-kpi-value sales-money">{{ number_format($summary['signed_value'], 0, ',', '.') }}₫</div>
                    <div class="sales-supporting-text small">{{ $summary['contract_count'] }} hợp đồng</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">KPI</div>
                    <div class="sales-kpi-value">{{ number_format($summary['target_percent'], 1, ',', '.') }}%</div>
                    <div class="sales-supporting-text small">{{ $summary['contract_count'] }}/{{ $summary['target_contract_count'] }} hợp đồng mục tiêu</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Tiền thực nhận</div>
                    <div class="sales-kpi-value sales-money text-dark">{{ number_format($summary['collected'], 0, ',', '.') }}₫</div>
                    <div class="sales-supporting-text small">Công nợ hiện tại {{ number_format($summary['outstanding'], 0, ',', '.') }}₫</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm sales-kpi-card h-100">
                <div class="card-body">
                    <div class="sales-kpi-label">Cơ hội đang theo dõi</div>
                    <div class="sales-kpi-value sales-money">{{ number_format($summary['pipeline'], 0, ',', '.') }}₫</div>
                    <div class="sales-supporting-text small">Tỷ lệ thắng {{ number_format($summary['conversion_rate'], 1, ',', '.') }}%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xxl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-1">KPI và doanh số theo tháng</h2>
                    <div class="sales-supporting-text small">Doanh số tính theo giá trị hợp đồng có ngày ký trong kỳ.</div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 sales-table">
                        <thead>
                            <tr>
                                <th>Tháng</th>
                                <th class="text-end">KPI</th>
                                <th class="text-end">Đã ký</th>
                                <th class="text-end">Thực nhận</th>
                                <th class="text-end">Hợp đồng</th>
                                <th style="min-width: 170px">Tiến độ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($monthly as $row)
                                <tr>
                                    <td class="fw-semibold">T{{ str_pad((string) $row['month'], 2, '0', STR_PAD_LEFT) }}</td>
                                    <td class="text-end sales-number">{{ number_format($row['target'], 0, ',', '.') }}₫</td>
                                    <td class="text-end sales-number fw-semibold">{{ number_format($row['signed'], 0, ',', '.') }}₫</td>
                                    <td class="text-end sales-number">{{ number_format($row['collected'], 0, ',', '.') }}₫</td>
                                    <td class="text-end">{{ $row['contracts'] }}/{{ $row['target_contracts'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1 report-progress" role="progressbar" aria-label="Tiến độ KPI tháng {{ $row['month'] }}" aria-valuenow="{{ min(100, $row['percent']) }}" aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar bg-success" style="width: {{ min(100, $row['percent']) }}%"></div>
                                            </div>
                                            <strong class="sales-number small">{{ number_format($row['percent'], 1, ',', '.') }}%</strong>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xxl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-1">Cơ cấu hợp đồng</h2>
                    <div class="sales-supporting-text small">Theo 5 lĩnh vực hoạt động.</div>
                </div>
                <div class="card-body">
                    @forelse ($contractTypes as $type)
                        <div class="report-type-row">
                            <div>
                                <div class="fw-semibold">{{ $type['label'] }}</div>
                                <div class="sales-supporting-text small">{{ $type['count'] }} hợp đồng</div>
                            </div>
                            <strong class="sales-number">{{ number_format($type['value'], 0, ',', '.') }}₫</strong>
                        </div>
                    @empty
                        <div class="sales-empty-state py-4">
                            <i class="fi fi-rr-chart-pie-alt fs-2" aria-hidden="true"></i>
                            <div class="fw-semibold mt-2">Chưa có hợp đồng trong kỳ</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if ($canChooseOwner)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h2 class="h6 mb-1">Xếp hạng kinh doanh</h2>
                <div class="sales-supporting-text small">So sánh doanh số ký và mức hoàn thành KPI.</div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 sales-table">
                    <thead>
                        <tr>
                            <th>Hạng</th>
                            <th>Nhân viên</th>
                            <th class="text-end">Doanh số</th>
                            <th class="text-end">Hợp đồng</th>
                            <th class="text-end">KPI</th>
                            <th class="text-end">Cơ hội</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ranking as $index => $person)
                            <tr>
                                <td><span class="sales-count-pill">{{ $index + 1 }}</span></td>
                                <td class="fw-semibold">{{ $person['name'] }}</td>
                                <td class="text-end sales-number fw-semibold">{{ number_format($person['signed_value'], 0, ',', '.') }}₫</td>
                                <td class="text-end">{{ $person['contract_count'] }}</td>
                                <td class="text-end sales-number">{{ number_format($person['target_percent'], 1, ',', '.') }}%</td>
                                <td class="text-end sales-number">{{ number_format($person['pipeline'], 0, ',', '.') }}₫</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4">Chưa có nhân viên Kinh doanh.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <!-- Cơ cấu hợp đồng theo dịch vụ -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm h-100" x-data="donutChart({{ json_encode($contractServicesStructure) }})">
                <div id="service-structure-bridge" style="display: none;" data-values="{{ json_encode($contractServicesStructure) }}" x-effect="updateData($el)"></div>
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-1">Cơ cấu hợp đồng theo dịch vụ</h2>
                    <div class="sales-supporting-text small">Phân bố doanh số ký theo từng loại dịch vụ.</div>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div wire:ignore class="w-100">
                        <div x-ref="container"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tỉ lệ doanh số theo nguồn thông tin -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm h-100" x-data="donutChart({{ json_encode($salesBySource) }})">
                <div id="sales-source-bridge" style="display: none;" data-values="{{ json_encode($salesBySource) }}" x-effect="updateData($el)"></div>
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-1">Tỉ lệ doanh số theo nguồn thông tin</h2>
                    <div class="sales-supporting-text small">Phân bổ đóng góp doanh số theo kênh thông tin tiếp cận.</div>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div wire:ignore class="w-100">
                        <div x-ref="container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Tỉ lệ chuyển đổi dịch vụ -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm h-100" x-data="conversionChart({{ json_encode($serviceConversionRates) }})">
                <div id="service-conversion-bridge" style="display: none;" data-values="{{ json_encode($serviceConversionRates) }}" x-effect="updateData($el)"></div>
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-1">Tỉ lệ chuyển đổi Dịch vụ: báo giá vs ký hợp đồng</h2>
                    <div class="sales-supporting-text small">So sánh số lượng Báo giá và Hợp đồng thực tế của mỗi dịch vụ.</div>
                </div>
                <div class="card-body">
                    <div wire:ignore class="w-100">
                        <div x-ref="container"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Phân tích theo Khu vực -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm h-100" x-data="regionalChart({{ json_encode($regionalBreakdown) }})">
                <div id="regional-breakdown-bridge" style="display: none;" data-values="{{ json_encode($regionalBreakdown) }}" x-effect="updateData($el)"></div>
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-1">Phân tích theo Khu vực</h2>
                    <div class="sales-supporting-text small">Báo giá, hợp đồng và doanh số phân bổ theo tỉnh/thành.</div>
                </div>
                <div class="card-body">
                    <div wire:ignore class="w-100">
                        <div x-ref="container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            if (window.greecoReportsAlpineInitialized) return;
            window.greecoReportsAlpineInitialized = true;

            Alpine.data('donutChart', (initialData, isCurrency = true) => ({
                chart: null,
                raw: initialData,
                init() {
                    const options = {
                        series: this.getSeries(),
                        labels: this.getLabels(),
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
                                formatter: (val) => {
                                    if (isCurrency) {
                                        return new Intl.NumberFormat('vi-VN').format(val) + 'đ';
                                    }
                                    return val;
                                }
                            }
                        }
                    };
                    this.chart = new ApexCharts(this.$refs.container, options);
                    this.chart.render();
                },
                updateData(el) {
                    this.raw = JSON.parse(el.dataset.values);
                    if (this.chart) {
                        this.chart.updateOptions({
                            series: this.getSeries(),
                            labels: this.getLabels()
                        });
                    }
                },
                getSeries() {
                    return this.raw.map(item => item.value || 0);
                },
                getLabels() {
                    return this.raw.map(item => item.label || '');
                }
            }));

            Alpine.data('conversionChart', (initialData) => ({
                chart: null,
                raw: initialData,
                init() {
                    const options = {
                        series: [
                            { name: 'Báo giá', data: this.getQuotations() },
                            { name: 'Hợp đồng', data: this.getContracts() }
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
                            categories: this.getCategories(),
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
                    this.chart = new ApexCharts(this.$refs.container, options);
                    this.chart.render();
                },
                updateData(el) {
                    this.raw = JSON.parse(el.dataset.values);
                    if (this.chart) {
                        this.chart.updateOptions({
                            series: [
                                { name: 'Báo giá', data: this.getQuotations() },
                                { name: 'Hợp đồng', data: this.getContracts() }
                            ],
                            xaxis: {
                                categories: this.getCategories()
                            }
                        });
                    }
                },
                getQuotations() {
                    return this.raw.map(item => item.quotations_count || 0);
                },
                getContracts() {
                    return this.raw.map(item => item.contracts_count || 0);
                },
                getCategories() {
                    return this.raw.map(item => item.label || '');
                }
            }));

            Alpine.data('regionalChart', (initialData) => ({
                chart: null,
                raw: initialData,
                init() {
                    const options = {
                        series: [
                            { name: 'Báo giá', type: 'column', data: this.getQuotations() },
                            { name: 'Ký hợp đồng', type: 'column', data: this.getContracts() },
                            { name: 'Doanh số (HĐ)', type: 'column', data: this.getSales() }
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
                            categories: this.getCategories(),
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
                    this.chart = new ApexCharts(this.$refs.container, options);
                    this.chart.render();
                },
                updateData(el) {
                    this.raw = JSON.parse(el.dataset.values);
                    if (this.chart) {
                        this.chart.updateOptions({
                            series: [
                                { name: 'Báo giá', type: 'column', data: this.getQuotations() },
                                { name: 'Ký hợp đồng', type: 'column', data: this.getContracts() },
                                { name: 'Doanh số (HĐ)', type: 'column', data: this.getSales() }
                            ],
                            xaxis: {
                                categories: this.getCategories()
                            }
                        });
                    }
                },
                getQuotations() {
                    return this.raw.map(item => item.quotations_count || 0);
                },
                getContracts() {
                    return this.raw.map(item => item.contracts_count || 0);
                },
                getSales() {
                    return this.raw.map(item => item.sales_value || 0);
                },
                getCategories() {
                    return this.raw.map(item => item.province || 'Chưa xác định');
                }
            }));
        });
    </script>
    @endpush

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <h2 class="h6 mb-1">Khoản phải thu quá hạn</h2>
            <div class="sales-supporting-text small">Danh sách cần Kinh doanh và Kế toán cùng xử lý.</div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 sales-table">
                <thead>
                    <tr>
                        <th>Hợp đồng</th>
                        <th>Khách hàng</th>
                        <th>Đợt thanh toán</th>
                        <th>Phụ trách</th>
                        <th>Hạn thanh toán</th>
                        <th class="text-end">Phải thu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($overdueSchedules as $schedule)
                        <tr>
                            <td><a href="{{ route('contracts.show', $schedule->contract) }}" class="fw-semibold">{{ $schedule->contract->contract_number ?: '#'.$schedule->contract->id }}</a></td>
                            <td>{{ $schedule->contract->customer->name }}</td>
                            <td>{{ $schedule->name }}</td>
                            <td>{{ $schedule->contract->owner?->name ?: 'Chưa phân công' }}</td>
                            <td class="text-danger fw-semibold">{{ $schedule->due_date?->format('d/m/Y') }}</td>
                            <td class="text-end sales-number fw-semibold">{{ number_format($schedule->amount, 0, ',', '.') }}₫</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4">Không có khoản phải thu quá hạn.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
