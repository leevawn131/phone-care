@extends('layout')

@section('title', 'Đơn mua của tôi')

@section('content')
    <section class="mx-auto max-w-5xl">
        <div class="rounded-2xl bg-gradient-to-r from-blue-600 via-blue-600 to-sky-500 px-6 py-10 text-white shadow-sm sm:px-10">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-blue-100">Lịch sử mua hàng</p>
            <h1 class="mt-3 text-3xl font-bold sm:text-4xl">Đơn mua của tôi</h1>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-blue-50">Theo dõi nhanh các đơn hàng đã tạo, tổng tiền thanh toán và trạng thái xử lý ngay trong khu vực tài khoản.</p>
        </div>

        <div class="mt-8 space-y-4">
            @forelse ($orders as $order)
                @php
                    $status = strtolower((string) $order->status);
                    $statusClasses = match ($status) {
                        'completed' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                        'processing' => 'bg-blue-50 text-blue-700 border border-blue-200',
                        'cancelled' => 'bg-red-50 text-red-700 border border-red-200',
                        default => 'bg-amber-50 text-amber-700 border border-amber-200',
                    };
                @endphp
                <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Mã đơn hàng</p>
                            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $order->order_number }}</p>
                            <p class="mt-2 text-sm text-gray-500">Đặt ngày {{ optional($order->placed_at)->format('d/m/Y H:i') ?? optional($order->created_at)->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] {{ $statusClasses }}">{{ $order->status }}</span>
                    </div>

                    <div class="mt-5 grid gap-4 rounded-2xl border border-gray-200 bg-gray-50 p-5 sm:grid-cols-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.18em] text-gray-500">Người nhận</p>
                            <p class="mt-2 text-sm font-semibold text-gray-900">{{ $order->recipient_name }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ $order->recipient_phone }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.18em] text-gray-500">Tổng tiền</p>
                            <p class="mt-2 text-lg font-bold text-blue-600">{{ number_format($order->grand_total) }} VND</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.18em] text-gray-500">Số sản phẩm</p>
                            <p class="mt-2 text-lg font-bold text-gray-900">{{ $order->items->sum('qty') }}</p>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-3 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm sm:grid-cols-2">
                        <p class="text-blue-800">
                            Điểm đã dùng: <span class="font-bold">{{ number_format((int) ($order->points_redeemed ?? 0)) }}</span>
                        </p>
                        <p class="text-blue-800 sm:text-right">
                            Điểm đã cộng: <span class="font-bold">{{ number_format((int) ($order->points_earned ?? 0)) }}</span>
                        </p>
                    </div>

                    @if ($order->items->isNotEmpty())
                        <div class="mt-5 space-y-3">
                            @foreach ($order->items as $item)
                                <div class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $item->product_name }}</p>
                                        <p class="mt-1 text-gray-500">{{ $item->variant_name ?: 'Phiên bản tiêu chuẩn' }} - SL {{ $item->qty }}</p>
                                    </div>
                                    <p class="font-semibold text-gray-800">{{ number_format($item->line_total) }} VND</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center shadow-sm">
                    <h2 class="text-2xl font-bold text-gray-900">Bạn chưa có đơn mua nào</h2>
                    <p class="mx-auto mt-3 max-w-lg text-sm leading-7 text-gray-500">Khi bạn đặt hàng thành công, lịch sử mua sẽ hiển thị tại đây để theo dõi xử lý và thông tin thanh toán.</p>
                    <a href="{{ route('products.index') }}" class="mt-8 inline-flex items-center rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-blue-700">Bắt đầu mua sắm</a>
                </div>
            @endforelse
        </div>
    </section>
@endsection