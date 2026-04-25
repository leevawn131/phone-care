@extends('layouts.shop')

@section('title', 'Xác nhận mật khẩu')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 rounded-2 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="fw-bold mb-3" style="font-size: 1.5rem;">Xác nhận mật khẩu</h1>
                        <p class="text-muted" style="font-size: 0.875rem;">
                            {{ __('Đây là khu vực bảo mật của hệ thống. Vui lòng xác nhận mật khẩu trước khi tiếp tục.') }}
                        </p>

                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

                            <div>
                                <label for="password" class="form-label fw-bold" style="font-size: 0.875rem;">Mật khẩu</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary fw-bold">
                                    {{ __('Xác nhận') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
