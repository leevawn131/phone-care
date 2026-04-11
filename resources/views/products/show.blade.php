@extends('layout')

@section('title', $product->name)

@section('content')
    @php($defaultVariant = $product->getDefaultVariant())
    @php($displayPrice = $product->getDisplayPrice())
    @php($originalPrice = $product->getOriginalPrice())

    <section class="grid gap-8 lg:grid-cols-[1.05fr,0.95fr]">
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="overflow-hidden rounded-2xl bg-gray-100">
                <img src="{{ $product->getPrimaryImageUrl() }}" alt="{{ $product->name }}" class="aspect-square w-full object-cover" onerror="this.onerror=null;this.src='https://placehold.co/800x800/e5e7eb/1f2937?text=Accessory';">
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-wrap gap-2">
                <span class="rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-gray-700">
                    {{ $product->category?->name ?? 'Phụ kiện' }}
                </span>
                @if ($product->brand)
                    <span class="rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-700">
                        {{ $product->brand->name }}
                    </span>
                @endif
                <span class="rounded-full bg-blue-600 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-white">
                    Bảo hành {{ $product->base_warranty_months }} tháng
                </span>
            </div>

            <h1 class="mt-5 text-3xl font-bold text-gray-900 sm:text-4xl">{{ $product->name }}</h1>
            <p class="mt-4 text-sm leading-7 text-gray-500">
                {{ $product->short_description ?: 'Phụ kiện được chọn lọc với thiết kế gọn gàng, độ hoàn thiện cao và thông tin bảo hành minh bạch cho người dùng.' }}
            </p>

            <div class="mt-6 rounded-2xl border border-blue-100 bg-blue-50 p-5">
                <p class="text-sm uppercase tracking-[0.2em] text-blue-700">Giá bán</p>
                <div class="mt-3 flex items-end gap-3">
                    <span class="text-4xl font-bold text-blue-600">{{ number_format($displayPrice) }} VND</span>
                    @if ($originalPrice && $originalPrice > $displayPrice)
                        <span class="mb-1 text-base text-gray-400 line-through">{{ number_format($originalPrice) }} VND</span>
                    @endif
                </div>
                <p class="mt-3 text-sm text-blue-700/80">
                    {{ $defaultVariant && $defaultVariant->stock > 0 ? 'Còn '.$defaultVariant->stock.' sản phẩm trong kho.' : 'Hiện chưa có sẵn hàng cho sản phẩm này.' }}
                </p>
            </div>

            <form method="POST" action="{{ route('cart.add', $product->slug) }}" class="mt-6 space-y-5">
                @csrf

                @if ($product->variants->count() > 1)
                    <div>
                        <label for="variant_id" class="mb-2 block text-sm font-semibold text-gray-700">Chọn phiên bản</label>
                        <select id="variant_id" name="variant_id" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 focus:border-blue-600 focus:outline-none focus:ring-0">
                            @foreach ($product->variants as $variant)
                                <option value="{{ $variant->id }}">
                                    {{ $variant->variant_name ?: 'Tiêu chuẩn' }} - {{ number_format($variant->sale_price ?? $variant->price) }} VND
                                </option>
                            @endforeach
                        </select>
                    </div>
                @elseif ($defaultVariant)
                    <input type="hidden" name="variant_id" value="{{ $defaultVariant->id }}">
                @endif

                <div class="grid gap-4 sm:grid-cols-[180px,1fr]">
                    <div>
                        <label for="quantity" class="mb-2 block text-sm font-semibold text-gray-700">Số lượng</label>
                        <input id="quantity" type="number" name="quantity" min="1" max="20" value="1" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 focus:border-blue-600 focus:outline-none focus:ring-0">
                    </div>
                    <div class="self-end">
                        @if ($defaultVariant && $defaultVariant->stock > 0)
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-700">
                                Thêm vào giỏ hàng
                            </button>
                        @else
                            <button type="button" disabled class="inline-flex w-full items-center justify-center rounded-xl bg-gray-200 px-5 py-3 text-sm font-bold text-gray-500">
                                Tạm hết hàng
                            </button>
                        @endif
                    </div>
                </div>
            </form>

            <div class="mt-8 grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Bảo hành</p>
                    <p class="mt-2 text-lg font-bold text-gray-900">{{ $product->base_warranty_months }} tháng</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Serial</p>
                    <p class="mt-2 text-lg font-bold text-gray-900">Kích hoạt tự động</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Thanh toán</p>
                    <p class="mt-2 text-lg font-bold text-gray-900">COD / Chuyển khoản</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-10 grid gap-6 lg:grid-cols-[1.1fr,0.9fr]">
        <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Mô tả chi tiết</p>
            <div class="mt-4 text-sm leading-8 text-gray-600">
                {!! nl2br(e($product->description ?: $product->short_description ?: 'Sản phẩm được thiết kế cho nhu cầu sử dụng hàng ngày, cân bằng giữa thẩm mỹ, độ bền và trải nghiệm thực tế.')) !!}
            </div>
        </article>

        <aside class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Phiên bản khả dụng</p>
            <div class="mt-4 space-y-3">
                @forelse ($product->variants as $variant)
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $variant->variant_name ?: 'Phiên bản tiêu chuẩn' }}</p>
                                <p class="mt-1 text-sm text-gray-500">SKU: {{ $variant->sku ?: 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-blue-600">{{ number_format($variant->sale_price ?? $variant->price) }} VND</p>
                                <p class="text-xs text-gray-500">Kho: {{ $variant->stock }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-4 text-sm text-gray-500">
                        Chưa có phiên bản bán ra cho sản phẩm này.
                    </div>
                @endforelse
            </div>
        </aside>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section class="mt-10">
            <div class="mb-6 flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Gợi ý thêm</p>
                    <h2 class="mt-2 text-2xl font-bold text-gray-900">Sản phẩm liên quan</h2>
                </div>
                <a href="{{ route('products.index') }}" class="text-sm font-semibold text-blue-600 transition hover:text-blue-700">Xem toàn bộ</a>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($relatedProducts as $relatedProduct)
                    @php($relatedVariant = $relatedProduct->getDefaultVariant())
                    <article class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md">
                        <a href="{{ route('products.show', $relatedProduct->slug) }}" class="block aspect-square overflow-hidden bg-gray-100">
                            <img src="{{ $relatedProduct->getPrimaryImageUrl() }}" alt="{{ $relatedProduct->name }}" class="h-full w-full object-cover transition duration-500 hover:scale-105" onerror="this.onerror=null;this.src='https://placehold.co/600x600/e5e7eb/1f2937?text=Accessory';">
                        </a>
                        <div class="p-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-gray-500">{{ $relatedProduct->category?->name ?? 'Phụ kiện' }}</p>
                            <h3 class="mt-2 min-h-[3.5rem] text-lg font-bold text-gray-900">
                                <a href="{{ route('products.show', $relatedProduct->slug) }}">{{ $relatedProduct->name }}</a>
                            </h3>
                            <div class="mt-4 flex items-center justify-between gap-3">
                                <span class="text-lg font-bold text-blue-600">{{ number_format($relatedProduct->getDisplayPrice()) }} VND</span>
                                @if ($relatedVariant && $relatedVariant->stock > 0)
                                    <form method="POST" action="{{ route('cart.add', $relatedProduct->slug) }}">
                                        @csrf
                                        <input type="hidden" name="variant_id" value="{{ $relatedVariant->id }}">
                                        <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-blue-700">
                                            Mua nhanh
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
@endsection