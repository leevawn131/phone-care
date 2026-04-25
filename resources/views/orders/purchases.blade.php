@extends('layouts.shop')

@section('content')
    <div class="container mt-5 mb-5">
    <section class="mx-auto">
        <div class="bg-primary text-white p-5 rounded-2 mb-4">
            <p class="text-uppercase" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.15em;">Lịch sử mua hàng</p>
            <h1 class="mt-2 fw-bold" style="font-size: 2rem;">Đơn mua của tôi</h1>
            <p class="mt-3" style="font-size: 0.875rem; line-height: 1.75;">Theo dõi nhanh các đơn hàng đã tạo, tổng tiền thanh toán và trạng thái xử lý ngay trong khu vực tài khoản.</p>
        </div>

        <div class="mt-4">
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
                <article class="card rounded-2 mb-3">
                <div class="card-body">
                    <div class="d-flex flex-column gap-3 flex-lg-row justify-content-lg-between align-items-lg-start">
                        <div>
                            <p class="text-uppercase" style="font-size: 0.75rem; color: #9ca3af;">Mã đơn hàng</p>
                            <p class="mt-2 fw-bold" style="font-size: 1.5rem; color: #111827;">{{ $order->order_number }}</p>
                            <p class="mt-2" style="font-size: 0.875rem; color: #6b7280;">Đặt ngày {{ optional($order->placed_at)->format('d/m/Y H:i') ?? optional($order->created_at)->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="badge" style="border: 1px solid; {{ str_contains($statusClasses, 'emerald') ? 'background-color: #ecfdf5; color: #047857; border-color: #a7f3d0;' : (str_contains($statusClasses, 'blue') ? 'background-color: #eff6ff; color: #0369a1; border-color: #bae6fd;' : (str_contains($statusClasses, 'red') ? 'background-color: #fef2f2; color: #991b1b; border-color: #fecaca;' : 'background-color: #fef3c7; color: #92400e; border-color: #fcd34d;')) }} ">{{ $order->status }}</span>
                    </div>

                    <div class="mt-4 row gap-3 rounded-2 border border-1" style="border-color: #e5e7eb; background-color: #f9fafb; padding: 1.25rem;">
                        <div class="col-md-4">
                            <p class="text-uppercase" style="font-size: 0.75rem; color: #9ca3af;">Người nhận</p>
                            <p class="mt-2 fw-bold" style="font-size: 0.875rem;">{{ $order->recipient_name }}</p>
                            <p class="mt-1" style="font-size: 0.875rem; color: #6b7280;">{{ $order->recipient_phone }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="text-uppercase" style="font-size: 0.75rem; color: #9ca3af;">Tổng tiền</p>
                            <p class="mt-2 fw-bold" style="font-size: 1.125rem; color: #2563eb;">{{ number_format($order->grand_total) }} VND</p>
                        </div>
                        <div class="col-md-4">
                            <p class="text-uppercase" style="font-size: 0.75rem; color: #9ca3af;">Số sản phẩm</p>
                            <p class="mt-2 fw-bold" style="font-size: 1.125rem;">{{ $order->items->sum('qty') }}</p>
                        </div>
                    </div>

                    <div class="mt-4 row gap-3 rounded-2 border border-1" style="border-color: #bfdbfe; background-color: #eff6ff; padding: 1rem; font-size: 0.875rem;">
                        <div class="col-md-6" style="color: #1e40af;">
                            Điểm đã dùng: <span class="fw-bold">{{ number_format((int) ($order->points_redeemed ?? 0)) }}</span>
                        </div>
                        <div class="col-md-6" style="color: #1e40af;">
                            Điểm đã cộng: <span class="fw-bold">{{ number_format((int) ($order->points_earned ?? 0)) }}</span>
                        </div>
                    </div>

                    @if ($order->items->isNotEmpty())
                        <div class="mt-4">
                            @foreach ($order->items as $item)
                                <div class="d-flex justify-content-between align-items-start gap-3 rounded-2 border border-1 bg-light p-3" style="border-color: #e5e7eb; font-size: 0.875rem;">
                                    <div>
                                        <p class="fw-bold" style="color: #111827;">{{ $item->product_name }}</p>
                                        <p class="mt-1" style="color: #6b7280;">{{ $item->variant_name ?: 'Phiên bản tiêu chuẩn' }} - SL {{ $item->qty }}</p>
                                    </div>
                                    <p class="fw-bold text-nowrap" style="color: #374151;">{{ number_format($item->line_total) }} VND</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                </article>
            @empty
                <div class="card rounded-2 border-dashed" style="border: 2px dashed #d1d5db;">
                <div class="card-body text-center p-5">
                    <h2 class="fw-bold" style="font-size: 1.5rem; color: #111827;">Bạn chưa có đơn mua nào</h2>
                    <p class="mt-3" style="max-width: 32rem; font-size: 0.875rem; line-height: 1.75; color: #6b7280; margin: 0 auto;">Khi bạn đặt hàng thành công, lịch sử mua sẽ hiển thị tại đây để theo dõi xử lý và thông tin thanh toán.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm fw-bold mt-4">Bắt đầu mua sắm</a>
                </div>
                </div>
            @endforelse
        </div>
    </section>
    </div>
@endsection