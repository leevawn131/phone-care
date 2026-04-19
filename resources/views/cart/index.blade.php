@php
    $cartCount = (int) $cartItems->sum('quantity');
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giỏ hàng phụ kiện điện thoại</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-800">
    @include('layouts.header')

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
        @endif

        <div class="mb-6 rounded-lg bg-gradient-to-r from-blue-600 via-blue-600 to-sky-500 px-6 py-6 text-white shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-100">Session cart for phone accessories</p>
                    <h1 class="mt-1 text-3xl font-bold">Giỏ hàng của bạn</h1>
                    <p class="mt-2 max-w-2xl text-sm text-blue-50">Kiểm tra lại ốp lưng, cáp sạc, tai nghe và phụ kiện trước khi chuyển sang bước thanh toán. Màu sắc được đồng bộ với trang chủ để trải nghiệm thống nhất hơn.</p>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div class="rounded-lg bg-white/10 px-4 py-3 text-center">
                        <p class="text-xs uppercase tracking-[0.2em] text-blue-100">Sản phẩm</p>
                        <p class="mt-1 text-2xl font-bold">{{ $cartCount }}</p>
                    </div>
                    <div class="rounded-lg bg-white/10 px-4 py-3 text-center">
                        <p class="text-xs uppercase tracking-[0.2em] text-blue-100">Tạm tính</p>
                        <p class="mt-1 text-2xl font-bold">{{ number_format($cartTotal) }}</p>
                    </div>
                    <div class="rounded-lg bg-white/10 px-4 py-3 text-center sm:col-span-1 col-span-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-blue-100">Trạng thái đơn</p>
                        <p class="mt-1 text-lg font-bold">Pending</p>
                    </div>
                </div>
            </div>
        </div>

        @if ($cartItems->isEmpty())
            <section class="rounded-lg border border-dashed border-blue-200 bg-white px-6 py-16 text-center shadow-sm">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                    <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="20" r="1"></circle>
                        <circle cx="18" cy="20" r="1"></circle>
                        <path d="M3 4h2l2.4 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.5L22 7H7"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-2xl font-bold text-gray-900">Giỏ hàng đang trống</h2>
                <p class="mx-auto mt-3 max-w-lg text-sm leading-7 text-gray-500">Bạn chưa thêm sản phẩm nào. Quay lại cửa hàng để chọn phụ kiện phù hợp và thông tin bảo hành rõ ràng cho từng món hàng.</p>
                <a href="{{ route('products.index') }}" class="mt-8 inline-flex items-center rounded-lg bg-blue-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-blue-700">
                    Tiếp tục mua sắm
                </a>
            </section>
        @else
            <section class="grid gap-6 xl:grid-cols-[1.55fr,0.85fr]">
                <div class="space-y-4">
                    @foreach ($cartItems as $item)
                        <article class="grid gap-5 rounded-lg border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md sm:grid-cols-[140px,1fr]">
                            <a href="{{ route('products.show', $item['slug']) }}" class="overflow-hidden rounded-lg bg-gray-100">
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='https://placehold.co/320x320/e5e7eb/1f2937?text=Hinh+san+pham';">
                            </a>

                            <div class="flex flex-col gap-4">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.22em] text-gray-400">{{ $item['variant_name'] ?: 'Phiên bản tiêu chuẩn' }}</p>
                                        <h2 class="mt-2 text-xl font-bold text-gray-900">
                                            <a href="{{ route('products.show', $item['slug']) }}" class="transition hover:text-blue-600">{{ $item['name'] }}</a>
                                        </h2>
                                        <div class="mt-3 flex flex-wrap gap-2 text-xs text-gray-500">
                                            <span class="rounded-full border border-gray-200 px-3 py-1">SKU: {{ $item['sku'] ?: 'N/A' }}</span>
                                            <span class="rounded-full bg-blue-50 px-3 py-1 font-medium text-blue-700">Bảo hành {{ $item['warranty_months'] }} tháng</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-blue-600">{{ number_format($item['price']) }} VND</p>
                                        <p class="text-sm text-gray-400">Tạm tính {{ number_format($item['price'] * $item['quantity']) }} VND</p>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-700">Số lượng</p>
                                        <div class="mt-2 flex items-center gap-2">
                                            @if ($item['quantity'] > 1)
                                                <form method="POST" action="{{ route('cart.update', $item['id']) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                                    <button type="submit" class="flex h-11 w-11 items-center justify-center rounded-lg border border-gray-200 text-lg font-semibold text-gray-600 transition hover:border-blue-600 hover:text-blue-600">-</button>
                                                </form>
                                            @else
                                                <button type="button" disabled class="flex h-11 w-11 items-center justify-center rounded-lg border border-gray-100 text-lg font-semibold text-gray-300">-</button>
                                            @endif

                                            <div class="flex h-11 min-w-[56px] items-center justify-center rounded-lg border border-gray-200 bg-white px-4 text-sm font-semibold text-gray-900">
                                                {{ $item['quantity'] }}
                                            </div>

                                            <form method="POST" action="{{ route('cart.update', $item['id']) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                                <button type="submit" class="flex h-11 w-11 items-center justify-center rounded-lg border border-gray-200 text-lg font-semibold text-gray-600 transition hover:border-blue-600 hover:text-blue-600">+</button>
                                            </form>
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('cart.remove', $item['id']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-100">
                                            Xóa sản phẩm
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm sm:p-8 xl:sticky xl:top-6 xl:self-start">
                    <p class="text-xs font-semibold uppercase tracking-[0.26em] text-gray-500">Tóm tắt đơn hàng</p>
                    <div class="mt-6 space-y-4 text-sm text-gray-600">
                        <div class="flex items-center justify-between">
                            <span>Số lượng sản phẩm</span>
                            <span class="font-semibold text-gray-900">{{ $cartCount }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Tạm tính</span>
                            <span class="font-semibold text-gray-900">{{ number_format($cartTotal) }} VND</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Phí vận chuyển</span>
                            <span class="font-semibold text-gray-900">Tính tại checkout</span>
                        </div>
                        <div class="border-t border-gray-100 pt-4">
                            <div class="flex items-center justify-between">
                                <span class="text-base font-semibold text-gray-900">Tổng thanh toán</span>
                                <span class="text-2xl font-bold text-blue-600">{{ number_format($cartTotal) }} VND</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 rounded-lg border border-blue-100 bg-blue-50 p-4 text-sm leading-7 text-blue-800">
                        Đơn hàng sẽ được tạo với trạng thái <span class="font-semibold">pending</span>. Khi admin chuyển sang <span class="font-semibold">completed</span>, hệ thống sẽ tự sinh serial và bản ghi bảo hành.
                    </div>

                    <div class="mt-6 space-y-3">
                        <a href="{{ route('checkout.index') }}" class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-700">
                            Tiến hành đặt hàng
                        </a>
                        <a href="{{ route('products.index') }}" class="inline-flex w-full items-center justify-center rounded-lg border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:border-blue-600 hover:text-blue-600">
                            Tiếp tục mua sắm
                        </a>
                    </div>
                </aside>
            </section>
        @endif
    </main>
</body>
</html>