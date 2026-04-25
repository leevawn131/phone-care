@extends('layouts.shop')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 rounded-2 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="fw-bold mb-1" style="font-size: 1.75rem;">Đăng ký tài khoản</h1>
                        <p class="text-muted mb-4" style="font-size: 0.875rem;">Tạo tài khoản mới để bắt đầu mua sắm</p>

                        <form method="POST" action="{{ route('register') }}" class="space-y-4">
                            @csrf

                            <!-- Full Name -->
                            <div>
                                <label for="name" class="form-label fw-bold" style="font-size: 0.875rem;">Họ và tên</label>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                                @error('name')
                                    <div class="invalid-feedback d-block" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="form-label fw-bold" style="font-size: 0.875rem;">Email</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="username">
                                @error('email')
                                    <div class="invalid-feedback d-block" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="form-label fw-bold" style="font-size: 0.875rem;">Mật khẩu</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                @error('password')
                                    <div class="invalid-feedback d-block" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Mật khẩu phải có ít nhất 8 ký tự</small>
                            </div>

                            <!-- Password Confirmation -->
                            <div>
                                <label for="password_confirmation" class="form-label fw-bold" style="font-size: 0.875rem;">Xác nhận mật khẩu</label>
                                <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password">
                                @error('password_confirmation')
                                    <div class="invalid-feedback d-block" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Terms -->
                            <div class="form-check" style="margin-top: 1.5rem;">
                                <input id="terms" type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" name="terms">
                                <label for="terms" class="form-check-label" style="font-size: 0.875rem;">
                                    Tôi đồng ý với
                                    <a href="#" class="text-primary text-decoration-none">Điều khoản dịch vụ</a>
                                    và
                                    <a href="#" class="text-primary text-decoration-none">Chính sách bảo mật</a>
                                </label>
                                @error('terms')
                                    <div class="invalid-feedback d-block" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary fw-bold w-100 mt-4">
                                Đăng ký
                            </button>
                        </form>

                        <!-- Login Link -->
                        <div class="mt-4 text-center" style="border-top: 1px solid #e5e7eb; padding-top: 1.5rem;">
                            <p class="text-muted m-0" style="font-size: 0.875rem;">
                                Đã có tài khoản?
                                <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-none">Đăng nhập</a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Info Section -->
                <div class="card border-0 rounded-2 mt-4" style="background-color: #ecfdf5; border: 1px solid #a7f3d0;">
                    <div class="card-body p-3">
                        <p class="fw-bold m-0" style="font-size: 0.875rem; color: #047857;">Tại sao nên đăng ký?</p>
                        <ul class="m-0 mt-2 ps-3" style="font-size: 0.875rem; color: #065f46; line-height: 1.75;">
                            <li>Dễ dàng quản lý các đơn hàng</li>
                            <li>Theo dõi bảo hành theo serial number</li>
                            <li>Tích lũy điểm mua hàng</li>
                            <li>Nhận thông báo khuyến mãi độc quyền</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
