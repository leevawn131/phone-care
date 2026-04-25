@php
    $cartCount = (int) $cartItems->sum('quantity');
@endphp
@extends('layouts.shop')

@section('content')
    <div class="container mt-5 mb-5">
        @if (session('success'))
            <div class="alert alert-success mb-4" role="alert">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mb-4" role="alert">{{ session('error') }}</div>
        @endif

        <div class="bg-dark text-white p-4 rounded-2 mb-4">
            <div class="row align-items-end">
                <div class="col-lg-7">
                    <p class="text-uppercase" style="font-size: 0.875rem; opacity: 0.9;">Session cart for phone accessories</p>
                    <h1 class="fw-bold" style="font-size: 2rem; margin: 0.5rem 0 0 0;">Giỏ hàng của bạn</h1>
                    <p class="mt-2" style="font-size: 0.875rem; opacity: 0.95; max-width: 42rem;">Kiểm tra lại ốp lưng, cáp sạc, tai nghe và phụ kiện trước khi chuyển sang bước thanh toán. Màu sắc được đồng bộ với trang chủ để trải nghiệm thống nhất hơn.</p>
                </div>
                <div class="col-lg-5">
                    <div class="row row-cols-2 row-cols-md-3 g-2">
                        <div class="col text-center rounded-2" style="background-color: rgba(255,255,255,0.1); padding: 0.75rem;">
                            <p class="text-uppercase" style="font-size: 0.75rem; opacity: 0.9;">Sản phẩm</p>
                            <p class="fw-bold mt-1" style="font-size: 1.5rem;">{{ $cartCount }}</p>
                        </div>
                        <div class="col text-center rounded-2" style="background-color: rgba(255,255,255,0.1); padding: 0.75rem;">
                            <p class="text-uppercase" style="font-size: 0.75rem; opacity: 0.9;">Tạm tính</p>
                            <p class="fw-bold mt-1" style="font-size: 1.5rem;">{{ number_format($cartTotal) }}</p>
                        </div>
                        <div class="col text-center rounded-2 col-md-12" style="background-color: rgba(255,255,255,0.1); padding: 0.75rem;">
                            <p class="text-uppercase" style="font-size: 0.75rem; opacity: 0.9;">Trạng thái đơn</p>
                            <p class="fw-bold mt-1">Pending</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($cartItems->isEmpty())
            <section class="card border-0 rounded-2" style="border: 2px dashed #bae6fd !important;">
                <div class="card-body text-center p-5">
                    <div class="d-flex justify-content-center align-items-center rounded-circle mx-auto" style="width: 80px; height: 80px; background-color: #eff6ff; color: #2563eb;">
                        <svg class="h-9 w-9" style="width: 36px; height: 36px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="20" r="1"></circle>
                            <circle cx="18" cy="20" r="1"></circle>
                            <path d="M3 4h2l2.4 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.5L22 7H7"></path>
                        </svg>
                    </div>
                    <h2 class="fw-bold mt-4" style="font-size: 1.5rem; color: #111827;">Giỏ hàng đang trống</h2>
                    <p class="mx-auto mt-3" style="max-width: 32rem; font-size: 0.875rem; line-height: 1.75; color: #6b7280;">Bạn chưa thêm sản phẩm nào. Quay lại cửa hàng để chọn phụ kiện phù hợp và thông tin bảo hành rõ ràng cho từng món hàng.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm fw-bold mt-4">
                        Tiếp tục mua sắm
                    </a>
                </div>
            </section>
        @else
            <div class="row g-4">
                <div class="col-lg-8">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        @foreach ($cartItems as $item)
                            <article class="card border-0 rounded-2">
                                <div class="card-body p-3 p-sm-4">
                                    <div class="row g-4">
                                        <div class="col-sm-auto">
                                            <a href="{{ route('products.show', $item['slug']) }}" class="d-block" style="width: 140px; height: 140px; overflow: hidden; border-radius: 0.5rem; background-color: #f3f4f6;">
                                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null;this.src='https://placehold.co/320x320/e5e7eb/1f2937?text=Hinh+san+pham';">
                                            </a>
                                        </div>
                                        <div class="col">
                                            <div class="d-flex flex-column gap-3">
                                                <div class="d-flex flex-column gap-3 flex-sm-row justify-content-sm-between align-items-sm-start">
                                                    <div>
                                                        <p class="text-uppercase" style="font-size: 0.75rem; color: #9ca3af; margin: 0;\">{{ $item['variant_name'] ?: 'Phiên bản tiêu chuẩn' }}</p>
                                                        <h2 class="mt-2 fw-bold" style="font-size: 1.125rem; color: #111827; margin: 0;">
                                                            <a href="{{ route('products.show', $item['slug']) }}" class="text-decoration-none text-dark hover:text-primary">{{ $item['name'] }}</a>
                                                        </h2>
                                                        <div class="mt-3 d-flex flex-wrap gap-2" style="font-size: 0.75rem;">
                                                            <span class="badge rounded-pill border border-1" style="border-color: #e5e7eb !important; background-color: white; color: #6b7280;">SKU: {{ $item['sku'] ?: 'N/A' }}</span>
                                                            <span class="badge rounded-pill" style="background-color: #eff6ff; color: #2563eb;">Bảo hành {{ $item['warranty_months'] }} tháng</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-sm-end">
                                                        <p class="fw-bold" style="font-size: 1.125rem; color: #2563eb; margin: 0;\">{{ number_format($item['price']) }} VND</p>
                                                        <p class="text-muted" style="font-size: 0.875rem;">Tạm tính {{ number_format($item['price'] * $item['quantity']) }} VND</p>
                                                    </div>
                                                </div>

                                                <div class="d-flex flex-column gap-2 flex-lg-row justify-content-lg-between align-items-lg-center">
                                                    <div>
                                                        <p class="fw-bold" style="font-size: 0.875rem; color: #374151; margin: 0 0 0.5rem 0;\">Số lượng</p>
                                                        <div class="d-flex align-items-center gap-2">
                                                            @if ($item['quantity'] > 1)
                                                                <form method="POST" action="{{ route('cart.update', $item['id']) }}" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                                                    <button type="submit" class="btn btn-sm border" style="width: 44px; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 1.125rem; font-weight: 600;">−</button>
                                                                </form>
                                                            @else
                                                                <button type="button" disabled class="btn btn-sm border" style="width: 44px; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 1.125rem; font-weight: 600; color: #d1d5db; border-color: #f3f4f6 !important;">−</button>
                                                            @endif

                                                            <div class="d-flex align-items-center justify-content-center border rounded" style="width: 44px; height: 44px; font-weight: 600; min-width: 56px;">
                                                                {{ $item['quantity'] }}
                                                            </div>

                                                            <form method="POST" action="{{ route('cart.update', $item['id']) }}" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                                                <button type="submit" class="btn btn-sm border" style="width: 44px; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 1.125rem; font-weight: 600;">+</button>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <form method="POST" action="{{ route('cart.remove', $item['id']) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm" style="border: 1px solid #fecaca; background-color: #fef2f2; color: #dc2626; padding: 0.5rem 1rem;">
                                                            Xóa sản phẩm
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 rounded-2 sticky-top" style="top: 24px;">
                        <div class="card-body p-4 p-sm-5">
                            <p class="text-uppercase fw-bold" style="font-size: 0.75rem; color: #9ca3af; margin: 0 0 1.5rem 0; letter-spacing: 0.15em;\">Tóm tắt đơn hàng</p>
                            <div style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.875rem;">
                                <div class="d-flex justify-content-between">
                                    <span>Số lượng sản phẩm</span>
                                    <span class="fw-bold" style="color: #111827;">{{ $cartCount }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Tạm tính</span>
                                    <span class="fw-bold" style="color: #111827;">{{ number_format($cartTotal) }} VND</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Phí vận chuyển</span>
                                    <span class="fw-bold" style="color: #111827;">Tính tại checkout</span>
                                </div>
                                <div style="border-top: 1px solid #f3f4f6; padding-top: 1rem; margin-top: 1rem;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold" style="color: #111827;\">Tổng thanh toán</span>
                                        <span class="fw-bold" style="font-size: 1.5rem; color: #2563eb;\">{{ number_format($cartTotal) }} VND</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 p-3 rounded-2" style="background-color: #eff6ff; border: 1px solid #bae6fd; font-size: 0.875rem; line-height: 1.75; color: #1e40af;">
                                Đơn hàng sẽ được tạo với trạng thái <span class="fw-bold">pending</span>. Khi admin chuyển sang <span class="fw-bold">completed</span>, hệ thống sẽ tự sinh serial và bản ghi bảo hành.
                            </div>

                            <div class="mt-4 d-flex flex-column gap-2">
                                <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-sm fw-bold w-100">
                                    Tiến hành đặt hàng
                                </a>
                                <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm fw-bold w-100">
                                    Tiếp tục mua sắm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                        </div>
                    </div>

        @endif
    </div>
@endsection