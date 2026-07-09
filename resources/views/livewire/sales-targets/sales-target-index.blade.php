<div class="sales-page py-4">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/sales.css') }}?v=1.3.0">
    @endpush

    @php
        $isSales = auth()->user()?->can('sales-target.manage')
            && auth()->user()?->can('business-dashboard.view');
    @endphp

    <form class="mb-4" wire:submit.prevent="saveAnnualTargets">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap align-items-end gap-3">
                    {{-- Bộ lọc Năm --}}
                    <div>
                        <label for="targetCommitmentYear" class="form-label fw-semibold mb-1 small text-muted text-uppercase">Năm</label>
                        <select id="targetCommitmentYear" class="form-select form-select-sm" style="min-width: 100px" wire:model.live="year">
                            @foreach (range((int) now()->year, (int) now()->year - 4) as $optionYear)
                                <option value="{{ $optionYear }}">{{ $optionYear }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Bộ lọc Nhân viên --}}
                    @if ($canChooseOwner)
                        <div>
                            <label for="targetUser" class="form-label fw-semibold mb-1 small text-muted text-uppercase">Nhân viên</label>
                            <select id="targetUser" class="form-select form-select-sm @error('targetUserId') is-invalid @enderror" style="min-width: 200px; max-width: 280px" wire:model.live="targetUserId">
                                @foreach ($salesUsers as $salesUser)
                                    <option value="{{ $salesUser->id }}">{{ $salesUser->name }}</option>
                                @endforeach
                            </select>
                            @error('targetUserId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    @else
                        <div>
                            <label for="targetUserReadonly" class="form-label fw-semibold mb-1 small text-muted text-uppercase">Nhân viên</label>
                            <input id="targetUserReadonly" type="text" class="form-control form-control-sm" style="min-width: 180px; max-width: 260px" value="{{ auth()->user()?->name }}" disabled>
                        </div>
                    @endif

                    {{-- Chế độ xem + Lưu: đẩy sang phải --}}
                    <div class="ms-auto d-flex align-items-end gap-2">
                        <div>
                            <label class="form-label fw-semibold mb-1 small text-muted text-uppercase d-block">Chế độ xem</label>
                            <div class="btn-group" role="group" aria-label="Chế độ xem mục tiêu">
                                <button type="button" class="btn btn-sm {{ $month === '' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('month', '')">
                                    <i class="fi fi-rr-table-columns me-1" aria-hidden="true"></i>
                                    Theo năm
                                </button>
                                <button type="button" class="btn btn-sm {{ $month !== '' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('month', '{{ now()->month }}')">
                                    <i class="fi fi-rr-calendar me-1" aria-hidden="true"></i>
                                    Chi tiết tháng
                                </button>
                            </div>
                        </div>

                        @if ($isSales)
                            <button type="submit" class="btn btn-sm btn-success" wire:loading.attr="disabled" wire:target="saveAnnualTargets">
                                <span wire:loading.remove wire:target="saveAnnualTargets">
                                    <i class="fi fi-rr-disk me-1" aria-hidden="true"></i>
                                    Lưu cam kết
                                </span>
                                <span wire:loading wire:target="saveAnnualTargets">
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    Đang lưu...
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== CHI TIẾT THÁNG ========== --}}
        @if ($month !== '' && $selectedMonthRow)
            {{-- Summary card --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
                    <h2 class="h6 mb-0 fw-bold">
                        <i class="fi fi-rr-calendar-check me-2 text-primary"></i>
                        Chi tiết tháng {{ str_pad($month, 2, '0', STR_PAD_LEFT) }}/{{ $year }}
                    </h2>
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 fw-semibold small text-muted text-uppercase">Tháng:</label>
                        <select class="form-select form-select-sm" style="width: auto" wire:model.live="month">
                            @for ($mi = 1; $mi <= 12; $mi++)
                                <option value="{{ $mi }}">Tháng {{ str_pad($mi, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-center text-uppercase small fw-bold text-nowrap">
                                    <th class="ps-3 text-start">Nhân viên</th>
                                    <th>Doanh số mục tiêu</th>
                                    <th>Thực tế đã ký</th>
                                    <th>Còn thiếu</th>
                                    <th>Tỷ lệ hoàn thành</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center text-nowrap">
                                    <td class="ps-3 fw-bold text-start">
                                        {{ $canChooseOwner
                                            ? ($salesUsers->firstWhere('id', $targetUserId)?->name ?? '—')
                                            : auth()->user()?->name }}
                                    </td>
                                    <td class="sales-number">
                                        {{ $selectedMonthRow['target'] > 0 ? number_format($selectedMonthRow['target'], 0, ',', '.') . ' đ' : '—' }}
                                    </td>
                                    <td class="text-success fw-semibold sales-number">
                                        {{ $selectedMonthRow['signed'] > 0 ? number_format($selectedMonthRow['signed'], 0, ',', '.') . ' đ' : '—' }}
                                    </td>
                                    <td class="fw-semibold sales-number {{ $selectedMonthRow['difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        @if ($selectedMonthRow['target'] > 0 || $selectedMonthRow['signed'] > 0)
                                            {{ $selectedMonthRow['difference'] >= 0 ? '+' : '' }}{{ number_format($selectedMonthRow['difference'], 0, ',', '.') }} đ
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if ($selectedMonthRow['target'] > 0)
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                <div class="progress flex-grow-1 report-progress" style="min-width: 80px">
                                                    <div class="progress-bar
                                                        {{ $selectedMonthRow['status'] === 'met' ? 'bg-success' : ($selectedMonthRow['status'] === 'near' ? 'bg-warning' : 'bg-danger') }}"
                                                        style="width: {{ min(100, $selectedMonthRow['percent']) }}%">
                                                    </div>
                                                </div>
                                                <span class="fw-bold sales-number small {{ $selectedMonthRow['status'] === 'met' ? 'text-success' : ($selectedMonthRow['status'] === 'near' ? 'text-warning' : 'text-danger') }}">
                                                    {{ number_format($selectedMonthRow['percent'], 1, ',', '.') }}%
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Contracts detail table --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h3 class="h6 mb-0 fw-bold">
                        <i class="fi fi-rr-document-signed me-2 text-success"></i>
                        Hợp đồng đã ký trong tháng {{ str_pad($month, 2, '0', STR_PAD_LEFT) }}/{{ $year }}
                    </h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 sales-table">
                        <thead>
                            <tr class="text-uppercase small">
                                <th class="ps-3">Tên công ty</th>
                                <th>Dịch vụ</th>
                                <th class="text-end">Giá trị HĐ (đ)</th>
                                <th>Phương thức TT</th>
                                <th class="pe-3">Ghi chú / Tình hình</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($monthContracts as $contract)
                                <tr>
                                    <td class="ps-3 fw-semibold">{{ $contract['customer'] }}</td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ $contract['service'] }}
                                        </span>
                                    </td>
                                    <td class="text-end text-success fw-semibold sales-number">
                                        {{ number_format($contract['value'], 0, ',', '.') }}
                                    </td>
                                    <td class="small text-muted">{{ $contract['payment_method'] }}</td>
                                    <td class="pe-3 small text-muted sales-working-situation">{{ $contract['notes'] ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fi fi-rr-document d-block mb-2" style="font-size: 2rem"></i>
                                        Không có hợp đồng nào được ký trong tháng này.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if (!empty($monthContracts))
                            <tfoot class="table-secondary fw-bold">
                                <tr>
                                    <td colspan="2" class="ps-3">Tổng tháng {{ $month }}/{{ $year }}</td>
                                    <td class="text-end text-success sales-number">
                                        {{ number_format(array_sum(array_column($monthContracts, 'value')), 0, ',', '.') }} đ
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        {{-- ========== THEO NĂM ========== --}}
        @else
            {{-- KPI cards --}}
            <div class="row g-3 mb-3">
                <div class="col-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="sales-kpi-label">Tổng cam kết {{ $year }}</div>
                            <div class="sales-kpi-value sales-money">{{ number_format($targetCommitments['total_target'], 0, ',', '.') }} đ</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="sales-kpi-label">Tổng thực tế đã ký</div>
                            <div class="sales-kpi-value sales-money text-success">{{ number_format($targetCommitments['total_signed'], 0, ',', '.') }} đ</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="sales-kpi-label">Còn thiếu để đạt năm</div>
                            <div class="sales-kpi-value sales-money {{ $targetCommitments['total_difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format(abs($targetCommitments['total_difference']), 0, ',', '.') }} đ
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="sales-kpi-label">Mức độ hoàn thành năm</div>
                            <div class="sales-kpi-value sales-money {{ $targetCommitments['total_percent'] >= 100 ? 'text-success' : 'text-danger' }}">{{ number_format($targetCommitments['total_percent'], 1, ',', '.') }}%</div>
                            <div class="progress report-progress" role="progressbar" aria-label="Mức độ hoàn thành năm" aria-valuenow="{{ min(100, $targetCommitments['total_percent']) }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar {{ $targetCommitments['total_percent'] >= 100 ? 'bg-success' : 'bg-danger' }}" style="width: {{ min(100, $targetCommitments['total_percent']) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Annual table --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-2 py-3">
                    <div>
                        <h1 class="h6 mb-1">Chi tiết cam kết theo tháng — {{ $year }}</h1>
                        <div class="sales-supporting-text small">Nhập cam kết từng tháng và theo dõi thực tế đã ký, chênh lệch, tỷ lệ đạt.</div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-success-subtle text-success fw-bold">Đạt</span>
                        <span class="badge bg-warning-subtle text-warning fw-bold">Gần đạt</span>
                        <span class="badge bg-danger-subtle text-danger fw-bold">Chưa đạt</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 sales-table">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Tháng</th>
                                <th class="text-end" style="width: 250px;">Cam kết (đ)</th>
                                <th class="text-end" style="width: 200px;">Thực tế đã ký (đ)</th>
                                <th class="text-end" style="width: 180px;">Chênh lệch (đ)</th>
                                <th style="width: 140px;">% Đạt</th>
                                <th>Trạng thái</th>
                                <th class="text-center" style="width: 100px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($targetCommitments['rows'] as $row)
                                <tr @class(['table-primary' => $row['is_current']])>
                                    <td>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-semibold text-dark">Tháng {{ $row['month'] }}</span>
                                                @if ($row['is_current'])
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-inline-flex align-items-center gap-1" style="font-size: 9px; font-weight: 700; padding: 2px 6px; text-transform: uppercase; letter-spacing: 0.05em; line-height: 1; border-radius: 4px;">
                                                        <span class="d-inline-block rounded-circle bg-primary" style="width: 5px; height: 5px;"></span>
                                                        Hiện tại
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="small text-muted">Quý {{ $row['quarter'] }}</div>
                                        </div>
                                    </td>
                                    <td class="text-end" style="width: 250px;">
                                        @if ($isSales)
                                            <x-currency-input
                                                class="ms-auto w-100"
                                                id="targetAmountMonth{{ $row['month'] }}"
                                                wire="targetAmounts.{{ $row['month'] }}"
                                                :error="'targetAmounts.'.$row['month']"
                                            />
                                        @else
                                            <span class="fw-semibold sales-number">{{ $row['target'] > 0 ? number_format($row['target'], 0, ',', '.') . ' đ' : '—' }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end sales-number fw-semibold text-success" style="width: 200px;">
                                        {{ $row['signed'] > 0 ? number_format($row['signed'], 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="text-end sales-number fw-semibold {{ $row['difference'] >= 0 ? 'text-success' : 'text-danger' }}" style="width: 180px;">
                                        {{ $row['difference'] >= 0 ? '+' : '-' }}{{ number_format(abs($row['difference']), 0, ',', '.') }}
                                    </td>
                                    <td style="width: 140px;">
                                        <div class="d-flex flex-column gap-1">
                                            <strong class="sales-number small text-end {{ $row['status'] === 'met' ? 'text-success' : ($row['status'] === 'near' ? 'text-warning' : 'text-danger') }}">{{ number_format($row['percent'], 1, ',', '.') }}%</strong>
                                            <div class="progress report-progress" role="progressbar" aria-label="Tỷ lệ đạt tháng {{ $row['month'] }}" aria-valuenow="{{ min(100, $row['percent']) }}" aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar {{ $row['status'] === 'met' ? 'bg-success' : ($row['status'] === 'near' ? 'bg-warning' : 'bg-danger') }}" style="width: {{ min(100, $row['percent']) }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge fw-bold {{ $row['status'] === 'met' ? 'bg-success-subtle text-success' : ($row['status'] === 'near' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }}">{{ $row['status_label'] }}</span>
                                    </td>
                                    <td class="text-center" style="width: 100px;">
                                        <button type="button" class="btn btn-sm btn-link sales-icon-button text-primary" wire:click="$set('month', '{{ $row['month'] }}')" aria-label="Xem chi tiết tháng {{ $row['month'] }}">
                                            <i class="fi fi-rr-eye" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-secondary">
                            <tr>
                                <th style="width: 120px;">Tổng năm {{ $year }}</th>
                                <th class="text-end sales-number" style="width: 250px;">{{ number_format($targetCommitments['total_target'], 0, ',', '.') }} đ</th>
                                <th class="text-end sales-number text-success" style="width: 200px;">{{ number_format($targetCommitments['total_signed'], 0, ',', '.') }} đ</th>
                                <th class="text-end sales-number {{ $targetCommitments['total_difference'] >= 0 ? 'text-success' : 'text-danger' }}" style="width: 180px;">
                                    {{ $targetCommitments['total_difference'] >= 0 ? '+' : '-' }}{{ number_format(abs($targetCommitments['total_difference']), 0, ',', '.') }}
                                </th>
                                <th class="sales-number" style="width: 140px;">{{ number_format($targetCommitments['total_percent'], 1, ',', '.') }}%</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif
    </form>
</div>
