@extends('layouts.shop')

@section('content')
    <div class="container mt-5 mb-5">
    <section class="mx-auto" style="max-width: 42rem;">
        <div class="mx-auto d-flex justify-content-center align-items-center rounded-circle" style="width: 96px; height: 96px; background-color: #ecfdf5; border: 1px solid #d1fae5;">
            <svg class="" style="width: 44px; height: 44px; color: #059669;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 6 9 17l-5-5"></path>
            </svg>
        </div>

        <p class="mt-4 text-uppercase" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.15em; color: #2563eb;">Đơn hàng đã ghi nhận</p>
        <h1 class="mt-3 mb-4 fw-bold" style="font-size: 2rem;">Cảm ơn bạn đã đặt hàng</h1>
        <p class="mx-auto" style="max-width: 42rem; font-size: 0.875rem; line-height: 1.75; color: #6b7280;">
            Đơn hàng của bạn đã được tạo thành công và đang chờ admin xác nhận. Khi đơn chuyển sang trạng thái <span class="font-semibold text-blue-600">completed</span>, hệ thống sẽ tự động sinh serial bảo hành cho từng sản phẩm.
        </p>

        <div class="mt-4 row gap-3 rounded-2 border border-1" style="border-color: #e5e7eb; background-color: #f9fafb; padding: 1.5rem;">
            <div class="col-md-4">
                <p class="text-uppercase" style="font-size: 0.75rem; color: #9ca3af;">Mã đơn hàng</p>
                <p class="mt-2 fw-bold" style="font-size: 1.125rem; color: #111827;">{{ $order->order_number }}</p>
            </div>
            <div class="col-md-4">
                <p class="text-uppercase" style="font-size: 0.75rem; color: #9ca3af;">Trạng thái</p>
                <p class="mt-2 d-inline-flex rounded-pill" style="background-color: #2563eb; color: white; padding: 0.25rem 0.75rem; font-size: 0.875rem; font-weight: 600;">
                    {{ $order->status }}
                </p>
            </div>
            <div class="col-md-4">
                <p class="text-uppercase" style="font-size: 0.75rem; color: #9ca3af;">Tổng tiền</p>
                <p class="mt-2 fw-bold" style="font-size: 1.125rem; color: #2563eb;">{{ number_format($order->grand_total) }} VND</p>
            </div>
        </div>

        <div class="mt-4 d-flex flex-column gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm fw-bold w-100">
                Tiếp tục mua sắm
            </a>
            <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-sm fw-bold w-100">
                Quay lại giỏ hàng
            </a>
        </div>
    </section>
    </div>
@endsection