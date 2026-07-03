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

            {{-- ── Admin Management Group ──────────────────────────────────────── --}}
            @if (auth()->user()?->can('user.view') || auth()->user()?->can('role.manage') || auth()->user()?->can('setting.view'))
                <li class="menu-heading mt-3">
                    <span class="menu-label">Quản trị hệ thống</span>
                </li>

                @if (auth()->user()?->can('user.view'))
                    <li class="menu-item">
                        <a class="menu-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                            <i class="fi fi-rr-users"></i>
                            <span class="menu-label">Người dùng</span>
                        </a>
                    </li>
                @endif

                @if (auth()->user()?->can('role.manage'))
                    <li class="menu-item">
                        <a class="menu-link {{ request()->routeIs('roles-permissions.*') ? 'active' : '' }}" href="{{ route('roles-permissions.index') }}">
                            <i class="fi fi-rr-shield-check"></i>
                            <span class="menu-label">Vai trò & Quyền</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a class="menu-link {{ request()->routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}">
                            <i class="fi fi-rr-bank"></i>
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
