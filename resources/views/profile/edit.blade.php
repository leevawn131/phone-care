@extends('layouts.shop')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 rounded-2 mb-4">
                    <div class="card-body">
                        <div class="rounded-2 p-3 mb-4" style="background-color: #eff6ff; border: 1px solid #bae6fd;">
                            <p class="text-uppercase fw-bold" style="font-size: 0.75rem; color: #2563eb; margin: 0;">Điểm tích lũy</p>
                            <p class="fw-bold mt-1" style="font-size: 2rem; color: #1e40af; margin: 0;">{{ number_format((int) ($user->loyalty_points ?? 0)) }} điểm</p>
                            <p class="text-muted mt-2" style="font-size: 0.875rem; margin: 0;">Điểm được cộng khi đơn hàng hoàn tất và có thể dùng tại bước checkout để giảm giá.</p>
                        </div>
                    </div>
                </div>

                <div class="card border-0 rounded-2 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <p class="fw-bold m-0">Thông tin hồ sơ</p>
                    </div>
                    <div class="card-body">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="card border-0 rounded-2 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <p class="fw-bold m-0">Địa chỉ nhận hàng</p>
                    </div>
                    <div class="card-body">
                        @include('profile.partials.update-delivery-address-form')
                    </div>
                </div>

                <div class="card border-0 rounded-2 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <p class="fw-bold m-0">Đổi mật khẩu</p>
                    </div>
                    <div class="card-body">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="card border-0 rounded-2 border-danger">
                    <div class="card-header bg-transparent border-bottom border-danger py-3">
                        <p class="fw-bold m-0">Xóa tài khoản</p>
                    </div>
                    <div class="card-body">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
