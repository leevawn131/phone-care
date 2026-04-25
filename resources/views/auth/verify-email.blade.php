@extends('layouts.shop')

@section('title', 'Xác minh email')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="card border-0 rounded-2 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="fw-bold mb-3" style="font-size: 1.5rem;">Xác minh email</h1>
                        <p class="text-muted" style="font-size: 0.875rem;">
                            {{ __('Cảm ơn bạn đã đăng ký. Trước khi bắt đầu, vui lòng xác minh địa chỉ email bằng cách nhấn vào liên kết chúng tôi vừa gửi. Nếu bạn chưa nhận được email, chúng tôi sẽ gửi lại cho bạn.') }}
                        </p>

                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success" role="alert">
                                {{ __('Liên kết xác minh mới đã được gửi đến địa chỉ email bạn đã đăng ký.') }}
                            </div>
                        @endif

                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-between mt-4">
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary fw-bold">
                                    {{ __('Gửi lại email xác minh') }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary">
                                    {{ __('Đăng xuất') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
