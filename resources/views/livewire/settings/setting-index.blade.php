<div>
    <div class="app-page-head d-flex align-items-center justify-content-between">
        <div class="clearfix">
            <h1 class="app-page-title">Cài đặt hệ thống</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Cài đặt
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    @if ($successMessage)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fi fi-rr-check me-2"></i> {{ $successMessage }}
            <button type="button" class="btn-close" wire:click="$set('successMessage', null)" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <ul class="nav nav-underline card-header-tabs" id="settingsTab" role="tablist" wire:ignore>
                        <li class="nav-item">
                            <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#general" role="tab">Tổng quan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#backup" role="tab">Sao lưu & Bộ nhớ đệm</a>
                        </li>
                    </ul>
                </div>
                
                <form wire:submit.prevent="save">
                    <div class="card-body p-4">
                        <div class="tab-content">
                            <!-- Tab: General -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel" wire:ignore.self>
                                <h5 class="fw-bold mb-4 text-dark"><i class="fi fi-rr-settings-sliders me-2 text-primary"></i>Cấu hình tổng quan</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Tên website <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="website_name" class="form-control @error('website_name') is-invalid @enderror" placeholder="Nhập tên website..." />
                                        @error('website_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Email liên hệ <span class="text-danger">*</span></label>
                                        <input type="email" wire:model="contact_email" class="form-control @error('contact_email') is-invalid @enderror" placeholder="Nhập email liên hệ..." />
                                        @error('contact_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Múi giờ <span class="text-danger">*</span></label>
                                        <select wire:model="timezone" class="form-select @error('timezone') is-invalid @enderror">
                                            <option value="Asia/Ho_Chi_Minh">Asia/Ho_Chi_Minh (Việt Nam)</option>
                                            <option value="UTC">UTC (Giờ phối hợp quốc tế)</option>
                                            <option value="America/New_York">America/New_York (Mỹ)</option>
                                            <option value="Europe/London">Europe/London (Anh)</option>
                                        </select>
                                        @error('timezone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Ngôn ngữ mặc định <span class="text-danger">*</span></label>
                                        <select wire:model="language" class="form-select @error('language') is-invalid @enderror">
                                            <option value="vi">Tiếng Việt</option>
                                            <option value="en">English</option>
                                            <option value="fr">French</option>
                                        </select>
                                        @error('language') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
 
                            <!-- Tab: Backup -->
                            <div class="tab-pane fade" id="backup" role="tabpanel" wire:ignore.self>
                                <h5 class="fw-bold mb-4 text-dark"><i class="fi fi-rr-database me-2 text-primary"></i>Sao lưu & Dữ liệu</h5>
                                <p class="text-muted small">Sao lưu dữ liệu định kỳ giúp bảo vệ hệ thống khỏi mất mát dữ liệu.</p>
                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    <button type="button" wire:click="backupNow" class="btn btn-primary btn-shadow waves-effect">
                                        <i class="fi fi-rr-cloud-upload me-1"></i> Sao lưu ngay lập tức
                                    </button>
                                    <button type="button" wire:click="exportData" class="btn btn-outline-secondary waves-effect">
                                        <i class="fi fi-rr-download me-1"></i> Xuất cấu hình (JSON)
                                    </button>
                                    <button type="button" wire:click="downloadSourceCode" class="btn btn-outline-success waves-effect">
                                        <i class="fi fi-rr-file-zip me-1"></i> Tải về Source Code (ZIP)
                                    </button>
                                    <button type="button" wire:click="clearCache" class="btn btn-outline-danger waves-effect">
                                        <i class="fi fi-rr-trash me-1"></i> Xóa toàn bộ Cache ứng dụng
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light p-4 text-end">
                        <button type="submit" class="btn btn-success btn-shadow px-4 waves-effect waves-light">
                            <i class="fi fi-rr-disk me-1"></i> Lưu toàn bộ cấu hình
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('swal:alert', (data) => {
        const payload = Array.isArray(data) ? data[0] : data;
        Swal.fire({
            icon: payload.icon || 'success',
            title: payload.title || 'Thông báo',
            text: payload.text || '',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            position: 'top-end',
            toast: true,
            background: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#1e293b' : '#ffffff',
            color: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#f8fafc' : '#0f172a',
            customClass: {
                popup: 'rounded-4 shadow-sm border-0'
            }
        });
    });
</script>
@endscript
