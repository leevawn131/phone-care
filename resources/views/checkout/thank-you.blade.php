@extends('layout')

@section('title', 'Cảm ơn bạn đã đặt hàng')

@section('content')
    <section class="mx-auto max-w-3xl rounded-2xl border border-gray-200 bg-white px-6 py-12 text-center shadow-sm sm:px-10">
        <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-emerald-50 ring-1 ring-emerald-200">
            <svg class="h-11 w-11 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 6 9 17l-5-5"></path>
            </svg>
        </div>

        <p class="mt-6 text-xs font-semibold uppercase tracking-[0.24em] text-blue-600">Đơn hàng đã ghi nhận</p>
        <h1 class="mt-3 text-3xl font-bold text-gray-900 sm:text-4xl">Cảm ơn bạn đã đặt hàng</h1>
        <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-gray-500 sm:text-base">
            Đơn hàng của bạn đã được tạo thành công và đang chờ admin xác nhận. Khi đơn chuyển sang trạng thái <span class="font-semibold text-blue-600">completed</span>, hệ thống sẽ tự động sinh serial bảo hành cho từng sản phẩm.
        </p>

        <div class="mt-8 grid gap-4 rounded-2xl border border-gray-200 bg-gray-50 p-6 text-left sm:grid-cols-3">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Mã đơn hàng</p>
                <p class="mt-2 text-lg font-bold text-gray-900">{{ $order->order_number }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Trạng thái</p>
                <p class="mt-2 inline-flex rounded-full bg-blue-600 px-3 py-1 text-sm font-bold uppercase tracking-[0.16em] text-white">
                    {{ $order->status }}
                </p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Tổng tiền</p>
                <p class="mt-2 text-lg font-bold text-blue-600">{{ number_format($order->grand_total) }} VND</p>
            </div>
        </div>

        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-blue-700">
                Tiếp tục mua sắm
            </a>
            <a href="{{ route('cart.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 px-6 py-3 text-sm font-semibold text-gray-700 transition hover:border-blue-600 hover:text-blue-600">
                Quay lại giỏ hàng
            </a>
        </div>
    </section>
@endsection