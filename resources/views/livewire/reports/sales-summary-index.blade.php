<div class="sales-page report-page py-4">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/sales.css') }}?v=1.3.0">
    @endpush

    <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-3 mb-4">
        <div>
            <div class="sales-eyebrow text-uppercase fw-bold mb-1">Kinh doanh & tài chính</div>
            <h1 class="h3 mb-1">Tổng kết doanh số theo hợp đồng</h1>
            <p class="sales-supporting-text mb-0">Theo dõi doanh số hợp đồng tái ký, doanh số hợp đồng mới và chi tiết danh sách hợp đồng đã ký trong kỳ.</p>
        </div>
    </div>

    {{-- Bộ lọc --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1 small text-muted text-uppercase">Năm</label>
                    <select wire:model.live="year" class="form-select form-select-sm" style="font-size: 0.85rem;">
                        @foreach(range((int) now()->year, (int) now()->year - 4) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                @if($canChooseOwner)
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1 small text-muted text-uppercase">Nhân viên</label>
                    <select wire:model.live="ownerId" class="form-select form-select-sm" style="font-size: 0.85rem;">
                        <option value="">Tất cả nhân viên</option>
                        @foreach($salesUsers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1 small text-muted text-uppercase">Nhân viên</label>
                    <input type="text" class="form-control form-control-sm" style="font-size: 0.85rem;" value="{{ auth()->user()?->name }}" disabled>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bảng tổng kết --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold">Tổng kết doanh số theo hợp đồng năm {{ $year }}</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 sales-table">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold ps-3">Chỉ tiêu</th>
                            @for($m = 1; $m <= $maxMonth; $m++)
                                <th class="text-end">Tháng {{ $m }}</th>
                            @endfor
                            <th class="text-end fw-bold pe-3">Tổng năm</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold text-success ps-3">DS Tái ký</td>
                            @for($m = 1; $m <= $maxMonth; $m++)
                                <td class="text-end {{ $salesSummary[$m]['renewal'] > 0 ? 'text-success' : 'text-muted' }}">
                                    @if($salesSummary[$m]['renewal'] > 0)
                                        <div class="fw-semibold">{{ number_format($salesSummary[$m]['renewal'], 0, ',', '.') }}</div>
                                        <div class="text-muted small">{{ $salesSummary[$m]['renewal_count'] }} HĐ</div>
                                    @else
                                        —
                                    @endif
                                </td>
                            @endfor
                            <td class="text-end fw-bold text-success pe-3">
                                <div>{{ number_format($totals['renewal'], 0, ',', '.') }} đ</div>
                                <div class="text-muted small fw-normal">{{ $totals['renewal_count'] }} HĐ</div>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-warning-emphasis ps-3" style="color: #fd7e14 !important;">DS HĐ mới</td>
                            @for($m = 1; $m <= $maxMonth; $m++)
                                <td class="text-end {{ $salesSummary[$m]['progressive'] > 0 ? 'text-warning-emphasis' : 'text-muted' }}" style="{{ $salesSummary[$m]['progressive'] > 0 ? 'color: #fd7e14 !important;' : '' }}">
                                    @if($salesSummary[$m]['progressive'] > 0)
                                        <div class="fw-semibold">{{ number_format($salesSummary[$m]['progressive'], 0, ',', '.') }}</div>
                                        <div class="text-muted small">{{ $salesSummary[$m]['progressive_count'] }} HĐ</div>
                                    @else
                                        —
                                    @endif
                                </td>
                            @endfor
                            <td class="text-end fw-bold pe-3" style="color: #fd7e14 !important;">
                                <div>{{ number_format($totals['progressive'], 0, ',', '.') }} đ</div>
                                <div class="text-muted small fw-normal">{{ $totals['progressive_count'] }} HĐ</div>
                            </td>
                        </tr>
                        <tr class="table-secondary">
                            <td class="fw-bold ps-3">Tổng theo hợp đồng</td>
                            @for($m = 1; $m <= $maxMonth; $m++)
                                <td class="text-end fw-bold {{ $salesSummary[$m]['contract_total'] > 0 ? 'text-dark' : 'text-muted' }}">
                                    @if($salesSummary[$m]['contract_total'] > 0)
                                        <div>{{ number_format($salesSummary[$m]['contract_total'], 0, ',', '.') }}</div>
                                        <div class="text-muted small fw-normal">{{ $salesSummary[$m]['renewal_count'] + $salesSummary[$m]['progressive_count'] }} HĐ</div>
                                    @else
                                        —
                                    @endif
                                </td>
                            @endfor
                            <td class="text-end fw-bold pe-3 text-dark">
                                <div>{{ number_format($totals['contract_total'], 0, ',', '.') }} đ</div>
                                <div class="text-muted small fw-normal">{{ $totals['renewal_count'] + $totals['progressive_count'] }} HĐ</div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="{{ $maxMonth + 1 }}" class="text-end">Tổng doanh số theo hợp đồng năm {{ $year }}</td>
                            <td class="text-end pe-3 text-dark">{{ number_format($totals['grand'], 0, ',', '.') }} đ</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Bộ lọc tháng + Chi tiết hợp đồng --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h6 class="mb-0 fw-bold">
                Chi tiết doanh số
                @if($month !== '0')
                    tháng {{ $month }}/{{ $year }}
                @else
                    — chọn tháng để xem
                @endif
            </h6>
            <div class="w-min-160px">
                <select wire:model.live="month" class="form-select form-select-sm" style="font-size: 0.85rem; width: auto; min-width: 140px;">
                    <option value="0">-- Chọn tháng --</option>
                    @for($m = 1; $m <= $maxMonth; $m++)
                        <option value="{{ $m }}">Tháng {{ $m }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="card-body p-0">
            @if($month === '0')
                <div class="text-center text-muted py-5">
                    <i class="fi fi-rr-calendar d-block mb-3" style="font-size: 3rem;"></i>
                    Chọn tháng để xem danh sách hợp đồng
                </div>
            @elseif($detail->isEmpty())
                <div class="text-center text-muted py-5">
                    Không có hợp đồng nào trong tháng {{ $month }}/{{ $year }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 sales-table">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center w-50px ps-3">STT</th>
                                <th>Tên khách hàng</th>
                                <th>Loại hợp đồng</th>
                                <th class="text-end">Doanh số (đ)</th>
                                <th class="text-center w-110px">Loại</th>
                                <th class="text-center w-130px pe-3">Ngày ký</th>
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
                                    <td class="text-center text-muted pe-3">
                                        {{ $row['date'] ? \Carbon\Carbon::parse($row['date'])->format('d/m/Y') : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="3" class="text-end ps-3">Tổng tháng {{ $month }}</td>
                                <td class="text-end text-success">{{ number_format($detail->sum('value'), 0, ',', '.') }} đ</td>
                                <td colspan="2" class="text-center text-muted pe-3 fw-normal">{{ $detail->count() }} hợp đồng</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
