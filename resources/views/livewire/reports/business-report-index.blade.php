<div class="sales-page report-page py-4">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/sales.css') }}?v=1.2.0">
    @endpush

    <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-3 mb-4">
        <div>
            <div class="sales-eyebrow text-uppercase fw-bold mb-1">Kinh doanh & tài chính</div>
            <h1 class="h3 mb-1">Báo cáo doanh số</h1>
            <p class="sales-supporting-text mb-0">Theo dõi KPI, hợp đồng ký, dòng tiền, cơ hội và công nợ trên cùng một báo cáo.</p>
        </div>
        @if ($canSetTarget)
            <button type="button" class="btn btn-primary sales-primary-action" wire:click="openTarget">
                <i class="fi fi-rr-target me-2" aria-hidden="true"></i>
                Đặt KPI
            </button>
        @endif
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
                    <div class="sales-kpi-value sales-money text-success">{{ number_format($summary['collected'], 0, ',', '.') }}₫</div>
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
                                    <td class="text-end sales-number text-success">{{ number_format($row['collected'], 0, ',', '.') }}₫</td>
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

    <div class="card border-0 shadow-sm">
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

    <div wire:ignore.self class="modal fade" id="salesTargetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" wire:submit.prevent="saveTarget">
                <div class="modal-header">
                    <div>
                        <h2 class="h5 modal-title mb-1">Thiết lập KPI</h2>
                        <div class="sales-supporting-text small">Mục tiêu doanh số và số hợp đồng theo tháng.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        @if ($canChooseOwner)
                            <div class="col-12">
                                <label for="targetUser" class="form-label">Nhân viên</label>
                                <select id="targetUser" class="form-select @error('targetUserId') is-invalid @enderror" wire:model="targetUserId">
                                    @foreach ($salesUsers as $salesUser)
                                        <option value="{{ $salesUser->id }}">{{ $salesUser->name }}</option>
                                    @endforeach
                                </select>
                                @error('targetUserId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endif
                        <div class="col-5">
                            <label for="targetMonth" class="form-label">Tháng</label>
                            <select id="targetMonth" class="form-select" wire:model="targetMonth">
                                @foreach (range(1, 12) as $optionMonth)
                                    <option value="{{ $optionMonth }}">Tháng {{ $optionMonth }}</option>
                                @endforeach
                            </select>
                        </div>
                        <x-currency-input class="col-7" id="targetAmount" wire="targetAmount" label="Doanh số mục tiêu" error="targetAmount" :suffix="false" />
                        <div class="col-12">
                            <label for="targetCount" class="form-label">Số hợp đồng mục tiêu</label>
                            <input id="targetCount" type="number" min="0" class="form-control @error('targetContractCount') is-invalid @enderror" wire:model="targetContractCount">
                            @error('targetContractCount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label for="targetNotes" class="form-label">Ghi chú</label>
                            <textarea id="targetNotes" rows="3" class="form-control" wire:model="targetNotes"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Lưu KPI</button>
                </div>
            </form>
        </div>
    </div>
</div>
