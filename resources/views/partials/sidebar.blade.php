<aside class="app-menubar" id="appMenubar">
    <div class="app-navbar-brand greeco-sidebar-brand">
        <a class="navbar-brand-mini visible-light" href="{{ route('dashboard') }}">
            <img class="greeco-logo-text" src="{{ asset('images/logo-text.png') }}" alt="GREECO logo">
        </a>
        <a class="navbar-brand-mini visible-dark" href="{{ route('dashboard') }}">
            <img class="greeco-logo-text" src="{{ asset('images/logo-text-white.png') }}" alt="GREECO logo">
        </a>
    </div>

    <nav class="app-navbar" data-simplebar>
        <ul class="menubar">
            <li class="menu-heading">
                <span class="menu-label">Chức năng chính</span>
            </li>

            @if (auth()->user()?->can('dashboard.view'))
                <li class="menu-item">
                    <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fi fi-rr-apps"></i>
                        <span class="menu-label">Dashboard</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()?->can('schedule.view'))
                <li class="menu-item">
                    <a class="menu-link {{ request()->routeIs('duty-schedules.*') ? 'active' : '' }}" href="{{ route('duty-schedules.index') }}">
                        <i class="fi fi-rr-calendar"></i>
                        <span class="menu-label">Lịch công tác</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()?->can('report.view'))
                <li class="menu-item">
                    <a class="menu-link {{ request()->routeIs('daily-reports.*') ? 'active' : '' }}" href="{{ route('daily-reports.index') }}">
                        <i class="fi fi-rr-document"></i>
                        <span class="menu-label">Báo cáo Ngày</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()?->can('document.view'))
                <li class="menu-item">
                    <a class="menu-link {{ request()->routeIs('document-regulations.*') ? 'active' : '' }}" href="{{ route('document-regulations.index') }}">
                        <i class="fi fi-rr-document-signed"></i>
                        <span class="menu-label">Quy định Tài liệu</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()?->can('mail.view'))
                <li class="menu-item">
                    <a class="menu-link {{ request()->routeIs('mail.*') ? 'active' : '' }}" href="{{ route('mail.index') }}">
                        <i class="fi fi-rr-envelope"></i>
                        <span class="menu-label">Hộp thư</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()?->can('customer.view') || auth()->user()?->can('quotation.view') || auth()->user()?->can('contract.view') || auth()->user()?->hasRole(\App\Enums\RoleEnum::Sales->value) || auth()->user()?->can('commission.view'))
                <li class="menu-heading">
                    <span class="menu-label">Kinh doanh</span>
                </li>
            @endif

            @if (auth()->user()?->can('customer.view'))
                <li class="menu-item">
                    <a class="menu-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                        <i class="fi fi-rr-users-alt"></i>
                        <span class="menu-label">Khách hàng</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()?->can('quotation.view'))
                <li class="menu-item">
                    <a class="menu-link {{ request()->routeIs('quotations.*') ? 'active' : '' }}" href="{{ route('quotations.index') }}">
                        <i class="fi fi-rr-document-signed"></i>
                        <span class="menu-label">Theo dõi báo giá</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()?->can('contract.view'))
                <li class="menu-item">
                    <a class="menu-link {{ request()->routeIs('contracts.*') ? 'active' : '' }}" href="{{ route('contracts.index') }}">
                        <i class="fi fi-rr-briefcase"></i>
                        <span class="menu-label">Hợp đồng</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()?->can('sales-target.manage') && auth()->user()?->can('business-dashboard.view'))
                <li class="menu-item">
                    <a class="menu-link {{ request()->routeIs('sales-targets.index') ? 'active' : '' }}" href="{{ route('sales-targets.index') }}">
                        <i class="fi fi-rr-target"></i>
                        <span class="menu-label">Đăng ký doanh số cam kết</span>
                    </a>
                </li>
            @endif

            {{-- ── Báo cáo & Thống kê ──────────────────────────────────────── --}}
            @php
                $canSeeReportsHeading = auth()->user()?->can('sales-report.view');
            @endphp

            @if ($canSeeReportsHeading)
                <li class="menu-heading">
                    <span class="menu-label">Báo cáo & Thống kê</span>
                </li>

                @if (auth()->user()?->can('sales-report.view'))
                    <li class="menu-item">
                        <a class="menu-link {{ request()->routeIs('sales-summaries.*') ? 'active' : '' }}" href="{{ route('sales-summaries.index') }}">
                            <i class="fi fi-rr-chart-histogram"></i>
                            <span class="menu-label">Bảng tổng kết doanh số</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a class="menu-link {{ request()->routeIs('sales-targets.report') ? 'active' : '' }}" href="{{ route('sales-targets.report') }}">
                            <i class="fi fi-rr-chart-line-up"></i>
                            <span class="menu-label">Báo cáo doanh số cam kết</span>
                        </a>
                    </li>
                @endif
            @endif

            @if (auth()->user()?->can('commission.view'))
                <li class="menu-item">
                    <a class="menu-link {{ request()->routeIs('commissions.*') ? 'active' : '' }}" href="{{ route('commissions.index') }}">
                        <i class="fi fi-rr-receipt"></i>
                        <span class="menu-label">Hoa hồng</span>
                    </a>
                </li>
            @endif

            {{-- ── Admin Management Group ──────────────────────────────────────── --}}
            @if (auth()->user()?->can('user.view') || auth()->user()?->can('role.manage') || auth()->user()?->can('setting.view'))
                @php
                    $isManagementActive = request()->routeIs('users.*') || 
                                          request()->routeIs('roles-permissions.*') || 
                                          request()->routeIs('departments.*') || 
                                          request()->routeIs('settings.*');
                @endphp
                <li class="menu-item menu-arrow {{ $isManagementActive ? 'open' : '' }}" data-keep-open="{{ $isManagementActive ? 'true' : 'false' }}">
                    <a class="menu-link {{ $isManagementActive ? 'open' : '' }}" href="javascript:void(0);">
                        <i class="fi fi-rr-settings-sliders"></i>
                        <span class="menu-label">Quản trị hệ thống</span>
                    </a>
                    <ul class="menu-inner" style="{{ $isManagementActive ? 'display: block;' : 'display: none;' }}">
                        @if (auth()->user()?->can('user.view'))
                            <li class="menu-item">
                                <a class="menu-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                    <i class="fi fi-rr-user"></i>
                                    <span class="menu-label">Người dùng</span>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()?->can('role.manage'))
                            <li class="menu-item">
                                <a class="menu-link {{ request()->routeIs('roles-permissions.*') ? 'active' : '' }}" href="{{ route('roles-permissions.index') }}">
                                    <i class="fi fi-rr-key"></i>
                                    <span class="menu-label">Vai trò & Quyền</span>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="menu-link {{ request()->routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}">
                                    <i class="fi fi-rr-building"></i>
                                    <span class="menu-label">Phòng ban</span>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()?->can('setting.view'))
                            <li class="menu-item">
                                <a class="menu-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                                    <i class="fi fi-rr-settings"></i>
                                    <span class="menu-label">Cài đặt</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
        </ul>
    </nav>

    <div class="app-footer">
        @if (auth()->user()?->can('user.create'))
            <button type="button" class="btn btn-primary waves-effect btn-shadow btn-app-nav w-100" onclick="window.location='{{ route('users.index') }}'">
                <i class="fi fi-rr-plus me-1"></i> <span class="nav-text">Thêm người dùng</span>
            </button>
        @else
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light waves-effect btn-shadow btn-app-nav w-100">
                <i class="fi fi-rr-home me-1"></i> <span class="nav-text">Dashboard</span>
            </a>
        @endif
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const initClickMenu = () => {
            const menuItems = document.querySelectorAll('.menu-item.menu-arrow');
            menuItems.forEach((adminMenuItem) => {
                const menuLink = adminMenuItem.querySelector('.menu-link');
                const subMenu = adminMenuItem.querySelector('.menu-inner');

                if (menuLink && subMenu) {
                    // Clone link to strip any old event listeners on Livewire re-renders
                    const newMenuLink = menuLink.cloneNode(true);
                    menuLink.parentNode.replaceChild(newMenuLink, menuLink);

                    newMenuLink.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();

                        const isOpen = adminMenuItem.classList.contains('open');

                        if (isOpen) {
                            adminMenuItem.classList.remove('open');
                            newMenuLink.classList.remove('open');
                            jQuery(subMenu).stop(true, true).slideUp(200);
                        } else {
                            // Close other open submenus first for accordion effect
                            menuItems.forEach((otherItem) => {
                                if (otherItem !== adminMenuItem && otherItem.classList.contains('open')) {
                                    otherItem.classList.remove('open');
                                    const otherLink = otherItem.querySelector('.menu-link');
                                    const otherSub = otherItem.querySelector('.menu-inner');
                                    if (otherLink) otherLink.classList.remove('open');
                                    if (otherSub) jQuery(otherSub).stop(true, true).slideUp(200);
                                }
                            });

                            adminMenuItem.classList.add('open');
                            newMenuLink.classList.add('open');
                            jQuery(subMenu).stop(true, true).slideDown(200);
                        }
                    });
                }
            });
        };

        initClickMenu();
        
        // Also register for Livewire navigation if used
        document.addEventListener('livewire:navigated', initClickMenu);
    });
</script>
