@extends('layouts.guest')

@section('content')
    <div class="page-layout">
        <div class="auth-wrapper min-vh-100 px-2" style="background-image: linear-gradient(90deg, rgba(49, 106, 255, .88), rgba(111, 66, 193, .72)), url('{{ asset('images/preview.png') }}'); background-size: cover; background-position: center;">
            <div class="row g-0 min-vh-100">
                <div class="col-xl-5 col-lg-6 ms-auto px-sm-4 align-self-center py-4">
                    <div class="card card-body p-4 p-sm-5 maxw-450px m-auto rounded-4 auth-card">
                        <div class="mb-4 text-center">
                            <a href="{{ route('login') }}" aria-label="GREECO logo">
                                <img class="visible-light" src="{{ asset('images/logo-text.png') }}" alt="GREECO" style="height: 64px; width: auto; object-fit: contain;">
                                <img class="visible-dark" src="{{ asset('images/logo-text-white.png') }}" alt="GREECO" style="height: 64px; width: auto; object-fit: contain;">
                            </a>
                        </div>

                        <div class="text-center mb-4">
                            <h5 class="mb-1">Chào mừng bạn trở lại</h5>
                            <p>Đăng nhập để truy cập hệ thống quản trị nội bộ.</p>
                        </div>

                        <form method="POST" action="{{ route('login.store') }}">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label" for="username">Tên đăng nhập</label>
                                <input
                                    type="text"
                                    class="form-control @error('username') is-invalid @enderror"
                                    id="username"
                                    name="username"
                                    value="{{ old('username') }}"
                                    placeholder="superadmin"
                                    required
                                    autofocus
                                >
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label" for="password">Mật khẩu</label>
                                <input
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password"
                                    placeholder="••••••••"
                                    required
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                                    <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary waves-effect waves-light w-100">
                                Đăng nhập
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
