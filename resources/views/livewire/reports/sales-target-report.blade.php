<div class="sales-page report-page py-4">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/sales.css') }}?v=1.3.0">
    @endpush

    <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-3 mb-4">
        <div>
            <div class="sales-eyebrow text-uppercase fw-bold mb-1">Kinh doanh & tài chính</div>
            <h1 class="h3 mb-1">Báo cáo doanh số cam kết</h1>
            <p class="sales-supporting-text mb-0">Theo dõi tiến độ hoàn thành chỉ tiêu doanh số so với cam kết đầu năm.</p>
        </div>
    </div>

    {{-- Bộ điều khiển --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex align-items-end flex-wrap gap-3">
                <div>
                    <label class="form-label fw-semibold mb-1 small text-muted text-uppercase">Năm</label>
                    <select wire:model.live="year" class="form-select form-select-sm" style="min-width:115px">
                        @foreach(range((int) now()->year, (int) now()->year - 4) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                @if($canChooseOwner)
                <div>
                    <label class="form-label fw-semibold mb-1 small text-muted text-uppercase">Nhân viên</label>
                    <select wire:model.live="ownerId" class="form-select form-select-sm" style="min-width:200px;max-width:280px">
                        <option value="">Tất cả nhân viên KD</option>
                        @foreach($salesUsers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <div>
                    <label class="form-label fw-semibold mb-1 small text-muted text-uppercase">Nhân viên</label>
                    <input type="text" class="form-control form-control-sm" style="min-width: 180px; max-width: 260px" value="{{ auth()->user()?->name }}" disabled>
                </div>
                @endif
                <div class="ms-auto">
                    <label class="form-label mb-1 small text-muted text-uppercase d-block">Chế độ xem</label>
                    <div class="btn-group" role="group">
                        <button type="button" wire:click="switchMode('year')"
                            class="btn btn-sm {{ $viewMode === 'year' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fi fi-rr-table-columns"></i><span class="d-none d-sm-inline ms-1">Theo năm</span>
                        </button>
                        <button type="button" wire:click="switchMode('month')"
                            class="btn btn-sm {{ $viewMode === 'month' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fi fi-rr-calendar"></i><span class="d-none d-sm-inline ms-1">Chi tiết tháng</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($viewMode === 'year')
    {{-- Tổng quan nhanh --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">Tổng cam kết {{ $year }}</p>
                    <div class="sales-kpi-value sales-money text-dark">{{ number_format($totals['target'], 0, ',', '.') }} đ</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">Tổng thực tế đã ký</p>
                    <div class="sales-kpi-value sales-money text-success">{{ number_format($totals['actual'], 0, ',', '.') }} đ</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">Doanh số tiềm năng</p>
                    <div class="sales-kpi-value sales-money text-warning">{{ number_format($totals['potential'], 0, ',', '.') }} đ</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">Chênh lệch so với cam kết</p>
                    <div class="sales-kpi-value sales-money {{ $this->totalDelta($totals) >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $this->totalDelta($totals) >= 0 ? '+' : '−' }}{{ number_format(abs($this->totalDelta($totals)), 0, ',', '.') }} đ
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">Mức độ hoàn thành năm</p>
                    <div class="sales-kpi-value sales-money {{ $this->pctTextClass($this->totalPct($totals)) }}">
                        {{ $this->totalPct($totals) !== null ? $this->totalPct($totals) . '%' : '—' }}
                    </div>
                    @if($this->totalPct($totals) !== null)
                        <div class="progress mt-2 h-6px" style="height: 6px;">
                            <div class="progress-bar {{ $this->monthMetrics($totals)['progressClass'] }}"
                                style="width:{{ $this->monthMetrics($totals)['progressWidth'] }}%"></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Bảng mục tiêu --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
            <h6 class="mb-0 fw-bold">Chi tiết cam kết theo tháng — {{ $year }}</h6>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">Đạt</span>
                <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1">Gần đạt</span>
                <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">Chưa đạt</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 sales-table">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-nowrap" style="width: 150px;">Tháng</th>
                            <th class="text-end text-nowrap" style="width: 150px;">Mục tiêu (đ)</th>
                            <th class="text-end text-nowrap" style="width: 250px;">Thực tế (Doanh số từ HĐ) (đ)</th>
                            <th class="text-end text-nowrap" style="width: 220px;">Doanh số chưa chắc chắn (đ)</th>
                            <th class="text-end text-nowrap" style="width: 150px;">Chênh lệch (đ)</th>
                            <th class="text-nowrap" style="min-width: 220px;">Tiến độ</th>
                            <th class="text-center text-nowrap" style="width: 130px;">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($months as $m => $data)
                            <tr class="{{ $year === now()->year && $m === now()->month ? 'table-primary bg-opacity-25' : ($this->monthMetrics($data)['target'] == 0 ? 'table-light' : '') }} cursor-pointer"
                                 wire:click="openDetail({{ $m }})">
                                <td class="ps-3 text-nowrap">
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-semibold text-dark">Tháng {{ $m }}</span>
                                            @if($year === now()->year && $m === now()->month)
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill" style="font-size: 10px; font-weight: 600; padding: 2px 6px; line-height: 1.2;">Hiện tại</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">Quý {{ (int) ceil($m / 3) }}</small>
                                    </div>
                                </td>
                                <td class="text-end fw-semibold {{ $this->monthMetrics($data)['target'] > 0 ? 'text-dark' : 'text-muted' }}">
                                    {{ $this->monthMetrics($data)['target'] > 0 ? number_format($this->monthMetrics($data)['target'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="text-end fw-semibold {{ $this->monthMetrics($data)['actual'] > 0 ? 'text-success' : 'text-muted' }}">
                                    {{ $this->monthMetrics($data)['actual'] > 0 ? number_format($this->monthMetrics($data)['actual'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="text-end text-warning fw-semibold">
                                    {{ $data['potential'] > 0 ? number_format($data['potential'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="text-end fw-semibold {{ $this->monthMetrics($data)['delta'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $this->monthMetrics($data)['target'] > 0 || $this->monthMetrics($data)['actual'] > 0 ? ($this->monthMetrics($data)['delta'] >= 0 ? '+' : '−') . number_format(abs($this->monthMetrics($data)['delta']), 0, ',', '.') : '—' }}
                                </td>
                                <td>
                                    @if($this->monthMetrics($data)['pct'] !== null)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1 h-8px" style="height: 8px;">
                                                <div class="progress-bar {{ $this->monthMetrics($data)['progressClass'] }}"
                                                    style="width: {{ $this->monthMetrics($data)['progressWidth'] }}%"></div>
                                            </div>
                                            <span class="text-muted small fw-semibold sales-number">{{ $this->monthMetrics($data)['pct'] }}%</span>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($this->monthMetrics($data)['pct'] !== null)
                                        <span class="badge {{ $this->pctBadgeClass($this->monthMetrics($data)['pct']) }}">
                                            {{ $this->monthMetrics($data)['pct'] >= 100 ? 'Đạt' : ($this->monthMetrics($data)['pct'] >= 70 ? 'Gần đạt' : 'Chưa đạt') }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($viewMode === 'month')
    {{-- Chi tiết tháng --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h6 class="mb-0 fw-bold">Chi tiết tháng {{ $viewMonth }}/{{ $year }}</h6>
            <div style="width: 140px;">
                <select wire:model.live="viewMonth" class="form-select form-select-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">Tháng {{ $m }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2-4 col-sm-6">
                    <div class="border rounded p-3 text-center bg-light">
                        <small class="text-muted d-block mb-1">Mục tiêu cam kết</small>
                        <div class="sales-kpi-value sales-money text-dark">{{ number_format($monthTarget, 0, ',', '.') }} đ</div>
                    </div>
                </div>
                <div class="col-md-2-4 col-sm-6">
                    <div class="border rounded p-3 text-center bg-light">
                        <small class="text-muted d-block mb-1">Thực tế đã ký</small>
                        <div class="sales-kpi-value sales-money text-success">{{ number_format($monthActual, 0, ',', '.') }} đ</div>
                    </div>
                </div>
                <div class="col-md-2-4 col-sm-6">
                    <div class="border rounded p-3 text-center bg-light">
                        <small class="text-muted d-block mb-1">Doanh số tiềm năng</small>
                        <div class="sales-kpi-value sales-money text-warning">{{ number_format($monthPotential, 0, ',', '.') }} đ</div>
                    </div>
                </div>
                <div class="col-md-2-4 col-sm-6">
                    <div class="border rounded p-3 text-center bg-light">
                        <small class="text-muted d-block mb-1">Còn thiếu</small>
                        <div class="sales-kpi-value sales-money text-danger">{{ number_format($monthRemain, 0, ',', '.') }} đ</div>
                    </div>
                </div>
                <div class="col-md-2-4 col-sm-12">
                    <div class="border rounded p-3 text-center bg-light">
                        <small class="text-muted d-block mb-1">Tỷ lệ hoàn thành</small>
                        <div class="sales-kpi-value sales-money {{ $this->pctTextClass($monthPct) }}">{{ $monthPct !== null ? $monthPct . '%' : '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Danh sách hợp đồng đã ký --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="mb-0 fw-bold text-success"><i class="fi fi-rr-document-signed me-2"></i>Hợp đồng đã ký trong tháng</h6>
        </div>
        <div class="card-body p-0">
            @if(empty($detail))
                <div class="text-center text-muted py-5">
                    Không có hợp đồng nào được ký trong tháng {{ $viewMonth }}/{{ $year }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 sales-table">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center w-50px ps-3">STT</th>
                                <th>Tên khách hàng</th>
                                <th>Dịch vụ</th>
                                <th class="text-end">Doanh số (đ)</th>
                                <th class="text-center w-110px">Loại</th>
                                <th class="text-center w-130px">Ngày ký</th>
                                <th class="pe-3">Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detail as $i => $row)
                                <tr>
                                    <td class="text-center text-muted ps-3">{{ $i + 1 }}</td>
                                    <td class="fw-semibold text-dark">{{ $row['customer'] }}</td>
                                    <td class="text-muted">{{ $row['type'] }}</td>
                                    <td class="text-end fw-semibold text-success">{{ number_format($row['value'], 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($row['is_renewal'])
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 10px; font-weight: 600; padding: 2px 6px;">Tái ký</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill" style="font-size: 10px; font-weight: 600; padding: 2px 6px;">HĐ mới</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-muted">
                                        {{ $row['date'] }}
                                    </td>
                                    <td class="text-muted pe-3 text-wrap">{{ $row['notes'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Danh sách báo giá tiềm năng --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="mb-0 fw-bold text-warning-emphasis"><i class="fi fi-rr-envelope-open-dollar me-2"></i>Báo giá tiềm năng dự kiến ký trong tháng</h6>
        </div>
        <div class="card-body p-0">
            @if(empty($potentialDetail))
                <div class="text-center text-muted py-5">
                    Không có báo giá tiềm năng nào trong tháng {{ $viewMonth }}/{{ $year }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 sales-table">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center w-50px ps-3">STT</th>
                                <th>Tên khách hàng</th>
                                <th>Dịch vụ</th>
                                <th class="text-end">Doanh số dự kiến (đ)</th>
                                <th class="text-center w-130px">Ngày dự kiến</th>
                                <th class="pe-3">Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($potentialDetail as $i => $row)
                                <tr>
                                    <td class="text-center text-muted ps-3">{{ $i + 1 }}</td>
                                    <td class="fw-semibold text-dark">{{ $row['company'] }}</td>
                                    <td class="text-muted">{{ $row['service'] }}</td>
                                    <td class="text-end fw-semibold text-warning-emphasis">{{ number_format($row['value'], 0, ',', '.') }}</td>
                                    <td class="text-center text-muted">
                                        {{ $row['date'] }}
                                    </td>
                                    <td class="text-muted pe-3 text-wrap">{{ $row['notes'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>
