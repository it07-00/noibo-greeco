<div class="sales-page py-4">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/sales.css') }}?v=1.1.0">
    @endpush

    <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('commissions.index') }}" class="small sales-supporting-text text-decoration-none">
                <i class="fi fi-rr-arrow-left me-1" aria-hidden="true"></i>
                Danh sách yêu cầu hoa hồng
            </a>
            <h1 class="h3 mb-1 mt-2">{{ $requestId ? 'Cập nhật yêu cầu chi hoa hồng' : 'Tạo yêu cầu chi hoa hồng' }}</h1>
            <p class="sales-supporting-text mb-0">Kinh doanh lập yêu cầu, Kế toán duyệt và xác nhận chi.</p>
        </div>
        <a href="{{ route('commissions.index') }}" class="btn btn-outline-secondary sales-action-button">
            <i class="fi fi-rr-list me-1" aria-hidden="true"></i>
            Danh sách
        </a>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm mb-4 sales-card-auto">
                <div class="card-header bg-white d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 py-3">
                    <div>
                        <h2 class="h6 mb-1 text-primary">Thông tin người nhận & tài khoản</h2>
                    </div>
                    @if ($savedAccounts->isNotEmpty())
                        <div class="d-flex align-items-center gap-2">
                            <label for="savedAccount" class="form-label mb-0 fw-semibold text-nowrap">Chọn nhanh:</label>
                            <select id="savedAccount" class="form-select" wire:model.live="selectedSavedAccountId">
                                <option value="">-- Chọn tài khoản đã lưu --</option>
                                @foreach ($savedAccounts as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->receiver_name }} ({{ $account->bank_number ?: $account->bank_account }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="receiverName" class="form-label">Người nhận hoa hồng <span class="text-danger">*</span></label>
                            <input id="receiverName" class="form-control @error('receiverName') is-invalid @enderror" wire:model.blur="receiverName" placeholder="HỌ VÀ TÊN NGƯỜI NHẬN">
                            @error('receiverName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="receiverPhone" class="form-label">Số điện thoại</label>
                            <input id="receiverPhone" class="form-control" wire:model="receiverPhone" placeholder="Số điện thoại">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="bankAccount" class="form-label">Ngân hàng khác (không tạo QR)</label>
                            <input id="bankAccount" class="form-control" wire:model="bankAccount" placeholder="Ví dụ: Techcombank - HN">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="bankCode" class="form-label">Ngân hàng</label>
                            <select id="bankCode" class="form-select @error('bankCode') is-invalid @enderror" wire:model.live="bankCode">
                                <option value="">-- Chọn ngân hàng --</option>
                                @foreach ($banks as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('bankCode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="bankNumber" class="form-label">Số tài khoản nhận</label>
                            <input id="bankNumber" class="form-control @error('bankNumber') is-invalid @enderror" wire:model.live="bankNumber" placeholder="Số tài khoản">
                            @error('bankNumber') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm sales-card-auto">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-0 text-primary">Thông tin yêu cầu chi hoa hồng</h2>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="contractType" class="form-label">Loại hợp đồng <span class="text-danger">*</span></label>
                            <select id="contractType" class="form-select @error('contractType') is-invalid @enderror" wire:model.live="contractType">
                                <option value="">Chọn loại hợp đồng</option>
                                @foreach ($contractTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('contractType') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="contractId" class="form-label">Số hợp đồng <span class="text-danger">*</span></label>
                            <select id="contractId" class="form-select @error('contractId') is-invalid @enderror" wire:model.live="contractId" @disabled($contractType === '')>
                                <option value="">{{ $contractType === '' ? 'Vui lòng chọn loại HĐ trước' : 'Chọn số hợp đồng' }}</option>
                                @foreach ($contracts as $contract)
                                    <option value="{{ $contract->id }}">
                                        {{ $contract->contract_number ?: '#'.$contract->id }} - {{ $contract->customer?->name ?: 'Chưa có khách hàng' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('contractId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <x-currency-input class="col-12 col-md-6" id="commissionAmount" wire="amount" label="Số tiền hoa hồng" :required="true" error="amount" suffix="VNĐ" />
                        <div class="col-12 col-md-6">
                            <label for="referrerInfo" class="form-label">Khách hàng hoặc giới thiệu</label>
                            <input id="referrerInfo" class="form-control" wire:model="referrerInfo" placeholder="Khách hàng hoặc giới thiệu">
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label">Yêu cầu và lưu ý</label>
                            <textarea id="notes" rows="4" class="form-control" wire:model="notes" placeholder="Tình hình làm việc"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary sales-action-button" wire:click="save">
                        <i class="fi fi-rr-disk me-1" aria-hidden="true"></i>
                        Lưu
                    </button>
                    <button type="button" class="btn btn-success sales-action-button" wire:click="save(true)">
                        Lưu tại trang
                    </button>
                    <button type="button" class="btn btn-secondary sales-action-button" wire:click="$refresh">
                        <i class="fi fi-rr-refresh me-1" aria-hidden="true"></i>
                        Làm lại
                    </button>
                    <a href="{{ route('commissions.index') }}" class="btn btn-danger sales-action-button">Thoát</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm position-sticky sales-card-auto" style="top: 24px;">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-0 text-primary">Thanh toán & QR Code</h2>
                </div>
                <div class="card-body">
                    <div class="bg-light border rounded d-flex align-items-center justify-content-center text-center p-3 mb-3" style="min-height: 360px;">
                        @if ($bankCode && $bankNumber)
                            <img src="{{ $this->getVietQrUrl() }}" class="img-fluid rounded border bg-white" style="max-width: 330px;" alt="QR Code">
                        @else
                            <div class="sales-supporting-text">
                                <i class="fi fi-rr-qr-scan d-block fs-1 mb-2" aria-hidden="true"></i>
                                Nhập ngân hàng và số tài khoản để tự động tạo QR.
                            </div>
                        @endif
                    </div>

                    <div class="bg-light border rounded p-3">
                        <div class="d-flex justify-content-between gap-3 mb-2">
                            <span class="sales-supporting-text">Người nhận</span>
                            <strong class="text-end">{{ $receiverName ?: 'Chưa nhập' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between gap-3 mb-2">
                            <span class="sales-supporting-text">Ngân hàng</span>
                            <strong>{{ $bankCode ?: 'Chưa chọn' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between gap-3 mb-2">
                            <span class="sales-supporting-text">Số tài khoản</span>
                            <strong>{{ $bankNumber ?: 'Chưa nhập' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between gap-3 pt-2 border-top">
                            <span class="sales-supporting-text">Số tiền</span>
                            <strong class="text-danger">{{ number_format((int) ($amount ?: 0), 0, ',', '.') }}₫</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
