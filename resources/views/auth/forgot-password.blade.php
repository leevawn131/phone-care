@extends('layouts.shop')

@section('title', 'Quên mật khẩu')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 rounded-2 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="fw-bold mb-3" style="font-size: 1.5rem;">Quên mật khẩu</h1>
                        <p class="text-muted" style="font-size: 0.875rem;">
                            {{ __('Quên mật khẩu? Không sao. Hãy nhập địa chỉ email của bạn, chúng tôi sẽ gửi liên kết để bạn đặt lại mật khẩu mới.') }}
                        </p>

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div>
                                <label for="email" class="form-label fw-bold" style="font-size: 0.875rem;">Email</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary fw-bold">
                                    {{ __('Gửi liên kết đặt lại mật khẩu') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
