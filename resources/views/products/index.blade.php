@extends('layout')

@php
    $dummyProducts = collect([
        [
            'name' => 'Ốp lưng chống sốc MagSafe cho iPhone 15 Pro Max',
            'category' => 'Ốp lưng',
            'brand' => 'Spigen',
            'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=900&q=80',
            'price' => 289000,
            'original_price' => 339000,
            'warranty' => 'Bảo hành 12 tháng',
            'is_new' => true,
            'slug' => '#',
            'cart_url' => '#',
        ],
        [
            'name' => 'Cáp sạc USB-C UGREEN 100W bọc dù 1.2m',
            'category' => 'Cáp sạc',
            'brand' => 'UGREEN',
            'image' => 'https://images.unsplash.com/photo-1587033411391-5d9e51cce126?auto=format&fit=crop&w=900&q=80',
            'price' => 199000,
            'original_price' => 249000,
            'warranty' => 'Bảo hành 24 tháng',
            'is_new' => false,
            'slug' => '#',
            'cart_url' => '#',
        ],
        [
            'name' => 'Củ sạc nhanh Anker Nano 30W chân Type-C',
            'category' => 'Củ sạc',
            'brand' => 'Anker',
            'image' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&w=900&q=80',
            'price' => 459000,
            'original_price' => 519000,
            'warranty' => 'Bảo hành 18 tháng',
            'is_new' => true,
            'slug' => '#',
            'cart_url' => '#',
        ],
        [
            'name' => 'Tai nghe Bluetooth SoundPEATS mini pin khỏe',
            'category' => 'Tai nghe',
            'brand' => 'SoundPEATS',
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=80',
            'price' => 790000,
            'original_price' => 890000,
            'warranty' => 'Bảo hành 12 tháng',
            'is_new' => false,
            'slug' => '#',
            'cart_url' => '#',
        ],
        [
            'name' => 'Kính cường lực full màn hình cho iPhone và Samsung',
            'category' => 'Kính cường lực',
            'brand' => 'Nillkin',
            'image' => 'https://images.unsplash.com/photo-1616423640778-28d1b53229bd?auto=format&fit=crop&w=900&q=80',
            'price' => 119000,
            'original_price' => 149000,
            'warranty' => 'Bảo hành 6 tháng',
            'is_new' => false,
            'slug' => '#',
            'cart_url' => '#',
        ],
        [
            'name' => 'Sạc dự phòng Baseus 20000mAh PD 30W',
            'category' => 'Sạc dự phòng',
            'brand' => 'Baseus',
            'image' => 'https://images.unsplash.com/photo-1609592806596-b43bada2f2dc?auto=format&fit=crop&w=900&q=80',
            'price' => 990000,
            'original_price' => 1190000,
            'warranty' => 'Bảo hành 24 tháng',
            'is_new' => true,
            'slug' => '#',
            'cart_url' => '#',
        ],
        [
            'name' => 'Giá đỡ điện thoại ô tô nam châm xoay 360 độ',
            'category' => 'Phụ kiện khác',
            'brand' => 'Baseus',
            'image' => 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?auto=format&fit=crop&w=900&q=80',
            'price' => 179000,
            'original_price' => 229000,
            'warranty' => 'Bảo hành trọn đời',
            'is_new' => true,
            'slug' => '#',
            'cart_url' => '#',
        ],
        [
            'name' => 'Dock sạc không dây 3 trong 1 cho iPhone và AirPods',
            'category' => 'Sạc không dây',
            'brand' => 'Belkin',
            'image' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?auto=format&fit=crop&w=900&q=80',
            'price' => 1490000,
            'original_price' => 1690000,
            'warranty' => 'Bảo hành 12 tháng',
            'is_new' => false,
            'slug' => '#',
            'cart_url' => '#',
        ],
    ]);

    $realProducts = isset($products) && $products->count()
        ? collect(method_exists($products, 'items') ? $products->items() : $products)->map(function ($product) {
            $variant = method_exists($product, 'getDefaultVariant') ? $product->getDefaultVariant() : null;
            $price = method_exists($product, 'getDisplayPrice')
                ? (int) $product->getDisplayPrice()
                : (int) ($variant->sale_price ?? $variant->price ?? 0);
            $originalPrice = method_exists($product, 'getOriginalPrice')
                ? (int) ($product->getOriginalPrice() ?? 0)
                : (int) ($variant->price ?? 0);
            $savingPercent = $originalPrice > $price
                ? (int) round((($originalPrice - $price) / $originalPrice) * 100)
                : 0;

            return [
                'name' => $product->name,
                'category' => $product->category?->name ?? 'Phụ kiện',
                'brand' => $product->brand?->name ?? 'Khác',
                'image' => method_exists($product, 'getPrimaryImageUrl')
                    ? $product->getPrimaryImageUrl()
                    : 'https://placehold.co/600x600/e5e7eb/1f2937?text=Accessory',
                'price' => $price,
                'original_price' => $originalPrice,
                'warranty' => 'Bảo hành '.($product->base_warranty_months ?: 12).' tháng',
                'is_new' => optional($product->created_at)->gt(now()->subDays(30)) ?? false,
                'slug' => route('products.show', $product->slug),
                'cart_url' => route('cart.add', $product->slug),
                'variant_id' => $variant?->id,
                'saving_percent' => $savingPercent,
            ];
        })
        : $dummyProducts->map(function ($item) {
            $item['saving_percent'] = (int) round((($item['original_price'] - $item['price']) / $item['original_price']) * 100);
            $item['variant_id'] = null;
            return $item;
        });

    $selectedCategoryIds = collect($selectedCategoryIds ?? [])->map(fn ($value) => (string) $value)->all();
    $selectedBrandIds = collect($selectedBrandIds ?? [])->map(fn ($value) => (string) $value)->all();
    $selectedWarrantyMonths = collect($selectedWarrantyMonths ?? [])->map(fn ($value) => (string) $value)->all();
    $sortOptions = [
        'discount' => 'Khuyến mãi tốt nhất',
        'price_asc' => 'Giá tăng dần',
        'price_desc' => 'Giá giảm dần',
        'newest' => 'Sản phẩm mới nhất',
        'popular' => 'Bán chạy nhất',
    ];
    $currentSort = $sort ?? request('sort', 'newest');
@endphp

@section('title', 'Phụ kiện điện thoại')

@push('styles')
    <style>
        .line-clamp-2-safe {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush

@section('content')
    <div class="mb-6 rounded-2xl bg-gradient-to-r from-blue-600 via-blue-600 to-sky-500 px-6 py-6 text-white shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-medium text-blue-100">Storefront cho phụ kiện điện thoại</p>
                <h1 class="mt-2 text-3xl font-bold sm:text-4xl">Phụ kiện cho điện thoại của bạn</h1>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-blue-50">
                    Chọn nhanh ốp lưng, cáp sạc, tai nghe và các phụ kiện bán chạy. Mỗi sản phẩm đều hiển thị rõ thời hạn bảo hành để dễ quyết định hơn.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-xl bg-white/10 px-4 py-3">
                    <p class="text-xs uppercase tracking-[0.22em] text-blue-100">Sản phẩm</p>
                    <p class="mt-1 text-2xl font-bold">{{ $realProducts->count() }}</p>
                </div>
                <div class="rounded-xl bg-white/10 px-4 py-3">
                    <p class="text-xs uppercase tracking-[0.22em] text-blue-100">Danh mục</p>
                    <p class="mt-1 text-2xl font-bold">{{ count($categories) }}</p>
                </div>
                <div class="rounded-xl bg-white/10 px-4 py-3">
                    <p class="text-xs uppercase tracking-[0.22em] text-blue-100">Bảo hành</p>
                    <p class="mt-1 text-2xl font-bold">6-24+</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[280px,1fr] xl:grid-cols-[300px,1fr]">
        <aside class="space-y-4">
            <form method="GET" action="{{ route('products.index') }}" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm" x-data="productFilters({
                priceValue: {{ $maxPrice === null ? (int) $maxPriceLimit : (int) $maxPrice }},
                maxLimit: {{ (int) $maxPriceLimit }},
            })">
                <input type="hidden" name="sort" value="{{ $currentSort }}">
                <input type="hidden" name="min_price" value="0">
                <input type="hidden" name="max_price" :value="priceValue">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-gray-800">Bộ lọc</h2>
                    <a href="{{ route('products.index', ['sort' => $currentSort]) }}" class="text-xs font-medium text-blue-600">Đặt lại</a>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <p class="text-sm font-semibold text-gray-900">Khoảng giá</p>
                    <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                        <span x-text="formatCurrency(priceValue)"></span>
                        <span x-text="formatCurrency(maxLimit)"></span>
                    </div>
                    <div class="mt-4 space-y-3">
                        <input type="range" min="0" :max="maxLimit" step="10000" x-model.number="priceValue" class="w-full accent-blue-600">
                    </div>
                </div>

                <div class="mt-5 border-t border-gray-100 pt-4">
                    <p class="text-sm font-semibold text-gray-900">Danh mục</p>
                    <div class="mt-3 space-y-3 text-sm text-gray-700">
                        @foreach ($categories as $category)
                            <label class="flex items-center gap-3">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}" @checked(in_array((string) $category->id, $selectedCategoryIds, true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                                <span>{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-5 border-t border-gray-100 pt-4">
                    <p class="text-sm font-semibold text-gray-900">Thương hiệu</p>
                    <div class="mt-3 grid grid-cols-2 gap-3 text-sm text-gray-700">
                        @foreach ($brands as $brand)
                            <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 hover:border-blue-600 hover:text-blue-600">
                                <input type="checkbox" name="brands[]" value="{{ $brand->id }}" @checked(in_array((string) $brand->id, $selectedBrandIds, true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                                <span>{{ $brand->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-5 border-t border-gray-100 pt-4">
                    <p class="text-sm font-semibold text-gray-900">Bảo hành</p>
                    <div class="mt-3 space-y-3 text-sm text-gray-700">
                        @foreach ($warrantyOptions as $option)
                            <label class="flex items-center gap-3">
                                <input type="checkbox" name="warranties[]" value="{{ $option }}" @checked(in_array((string) $option, $selectedWarrantyMonths, true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                                <span>{{ $option === 0 ? 'Trọn đời' : $option.' tháng' }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                        Áp dụng
                    </button>
                </div>
            </form>
        </aside>

        <section>
            <div class="mb-4 flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-900">Danh sách sản phẩm</p>
                    <p class="text-xs text-gray-500">Hiển thị {{ $realProducts->count() }} sản phẩm phù hợp</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($sortOptions as $sortKey => $option)
                        <a href="{{ route('products.index', array_merge(request()->except(['sort', 'page']), ['sort' => $sortKey])) }}" class="rounded-lg border px-3 py-2 text-sm font-medium transition {{ $currentSort === $sortKey ? 'border-blue-600 bg-blue-50 text-blue-600' : 'border-gray-200 bg-white text-gray-600 hover:border-blue-600 hover:text-blue-600' }}">
                            {{ $option }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($realProducts as $item)
                    <article class="group rounded-2xl border border-gray-200 bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <a href="{{ $item['slug'] }}" class="relative block overflow-hidden rounded-xl bg-gray-100">
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="aspect-square w-full object-cover" onerror="this.onerror=null;this.src='https://placehold.co/600x600/e5e7eb/1f2937?text=Accessory';">
                            <div class="absolute left-3 top-3 flex flex-col gap-2">
                                @if (($item['saving_percent'] ?? 0) > 0)
                                    <span class="rounded-lg bg-red-500 px-2 py-1 text-xs font-semibold text-white">Tiết kiệm {{ $item['saving_percent'] }}%</span>
                                @endif
                                @if ($item['is_new'])
                                    <span class="rounded-lg bg-blue-600 px-2 py-1 text-xs font-semibold text-white">Mới</span>
                                @endif
                            </div>
                        </a>

                        <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                            <span>{{ $item['category'] }}</span>
                            <span>{{ $item['brand'] }}</span>
                        </div>

                        <h3 class="mt-2 min-h-[3.5rem] text-sm font-semibold leading-6 text-gray-900 line-clamp-2-safe">
                            {{ $item['name'] }}
                        </h3>

                        <div class="mt-3 flex items-end gap-2">
                            <span class="text-xl font-bold text-blue-600">{{ number_format($item['price']) }} VND</span>
                            @if (($item['original_price'] ?? 0) > $item['price'])
                                <span class="text-sm text-gray-400 line-through">{{ number_format($item['original_price']) }} VND</span>
                            @endif
                        </div>

                        <div class="mt-3 space-y-2">
                            <div class="inline-flex rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                {{ $item['warranty'] }}
                            </div>
                            <p class="text-xs leading-5 text-gray-500">Hàng chính hãng, đổi mới nhanh khi lỗi do nhà sản xuất và được kích hoạt bảo hành theo serial riêng.</p>
                        </div>

                        <div class="mt-4">
                            @if ($item['cart_url'] !== '#')
                                <form method="POST" action="{{ $item['cart_url'] }}">
                                    @csrf
                                    @if (! empty($item['variant_id']))
                                        <input type="hidden" name="variant_id" value="{{ $item['variant_id'] }}">
                                    @endif
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="w-full rounded-xl border border-blue-600 bg-white px-4 py-2.5 text-sm font-semibold text-blue-600 transition hover:bg-blue-600 hover:text-white">
                                        Thêm vào giỏ
                                    </button>
                                </form>
                            @else
                                <button type="button" class="w-full rounded-xl border border-blue-600 bg-white px-4 py-2.5 text-sm font-semibold text-blue-600 transition hover:bg-blue-600 hover:text-white">
                                    Thêm vào giỏ
                                </button>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            @if (isset($products) && method_exists($products, 'hasPages') && $products->hasPages())
                <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    {{ $products->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productFilters', (config = {}) => ({
                priceValue: Number(config.priceValue ?? 0),
                maxLimit: Number(config.maxLimit ?? 2000000),
                formatCurrency(value) {
                    return new Intl.NumberFormat('vi-VN').format(Number(value || 0)) + ' VND';
                },
            }));
        });
    </script>
@endpush

