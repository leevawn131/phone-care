@extends('layouts.shop')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 rounded-2 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="fw-bold mb-1" style="font-size: 1.75rem;">Đăng nhập</h1>
                        <p class="text-muted mb-4" style="font-size: 0.875rem;">Đăng nhập để quản lý đơn hàng và bảo hành của bạn</p>

                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="space-y-4">
                            @csrf

                            <!-- Email / Phone -->
                            <div>
                                <label for="email" class="form-label fw-bold" style="font-size: 0.875rem;">Email / Số điện thoại</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                                @error('email')
                                    <div class="invalid-feedback d-block" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="form-label fw-bold" style="font-size: 0.875rem;">Mật khẩu</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                @error('password')
                                    <div class="invalid-feedback d-block" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="form-check">
                                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                                <label for="remember_me" class="form-check-label" style="font-size: 0.875rem;">Ghi nhớ đăng nhập</label>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                @if (Route::has('password.request'))
                                    <a class="text-primary text-decoration-none" style="font-size: 0.875rem;" href="{{ route('password.request') }}">
                                        Quên mật khẩu?
                                    </a>
                                @endif

                                <button type="submit" class="btn btn-primary fw-bold" style="padding-left: 2rem; padding-right: 2rem;">
                                    Đăng nhập
                                </button>
                            </div>
                        </form>

                        <!-- Register Link -->
                        <div class="mt-4 text-center" style="border-top: 1px solid #e5e7eb; padding-top: 1.5rem;">
                            <p class="text-muted m-0" style="font-size: 0.875rem;">
                                Chưa có tài khoản?
                                <a href="{{ route('register') }}" class="fw-bold text-primary text-decoration-none">Đăng ký ngay</a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Info Section -->
                <div class="card border-0 rounded-2 mt-4" style="background-color: #eff6ff; border: 1px solid #bae6fd;">
                    <div class="card-body p-3">
                        <p class="fw-bold m-0" style="font-size: 0.875rem; color: #2563eb;">Lợi ích khi đăng nhập</p>
                        <ul class="m-0 mt-2 ps-3" style="font-size: 0.875rem; color: #1e40af; line-height: 1.75;">
                            <li>Theo dõi đơn hàng và trạng thái bảo hành</li>
                            <li>Quản lý địa chỉ nhận hàng</li>
                            <li>Tích lũy điểm mua hàng để giảm giá</li>
                            <li>Tra cứu bảo hành nhanh chóng</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
