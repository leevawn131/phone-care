@extends('layouts.shop')

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
                    : 'https://placehold.co/600x600/e5e7eb/1f2937?text=Hinh+san+pham',
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
    <header class="bg-dark py-5 mb-5">
        <div class="container px-4 px-lg-5 my-4">
            <div class="text-center text-white">
                <p class="lead fw-normal text-white-50 mb-2">Storefront cho phụ kiện điện thoại</p>
                <h1 class="display-5 fw-bolder">Phụ kiện cho điện thoại của bạn</h1>
                <p class="lead fw-normal text-white-50 mb-0">Chọn nhanh ốp lưng, cáp sạc, tai nghe và các phụ kiện bán chạy. Mỗi sản phẩm đều hiển thị rõ thời hạn bảo hành để dễ quyết định hơn.</p>
            </div>
        </div>
    </header>

    <section class="container px-4 px-lg-5 mb-5">
        <div class="row g-3 row-cols-1 row-cols-md-3">
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <p class="text-uppercase text-muted small mb-2">Sản phẩm</p>
                        <h2 class="fw-bolder mb-0">{{ $realProducts->count() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <p class="text-uppercase text-muted small mb-2">Danh mục</p>
                        <h2 class="fw-bolder mb-0">{{ count($categories) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <p class="text-uppercase text-muted small mb-2">Bảo hành</p>
                        <h2 class="fw-bolder mb-0">6-24+</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container px-4 px-lg-5">
        <div class="row g-4">
            <aside class="col-lg-3">
                <form method="GET" action="{{ route('products.index') }}" class="card shadow-sm sticky-lg-top" style="top: 1rem;" x-data="{
                    priceValue: {{ $maxPrice === null ? (int) $maxPriceLimit : (int) $maxPrice }},
                    maxLimit: {{ (int) $maxPriceLimit }},
                    formatCurrency(value) {
                        return new Intl.NumberFormat('vi-VN').format(Number(value || 0)) + ' VND';
                    },
                }">
                    <div class="card-header bg-white d-flex align-items-center justify-content-between">
                        <span class="fw-semibold text-uppercase small">Bộ lọc</span>
                        <a href="{{ route('products.index', ['sort' => $currentSort]) }}" class="small text-decoration-none">Đặt lại</a>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="sort" value="{{ $currentSort }}">
                        <input type="hidden" name="min_price" value="0">
                        <input type="hidden" name="max_price" :value="priceValue">

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Khoảng giá</label>
                            <div class="d-flex justify-content-between small text-muted mb-2">
                                <span x-text="formatCurrency(priceValue)"></span>
                                <span x-text="formatCurrency(maxLimit)"></span>
                            </div>
                            <input type="range" min="0" :max="maxLimit" step="10000" x-model.number="priceValue" class="form-range">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Danh mục</label>
                            <div class="d-grid gap-2">
                                @foreach ($categories as $category)
                                    <label class="form-check border rounded px-3 py-2 mb-0">
                                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" @checked(in_array((string) $category->id, $selectedCategoryIds, true)) class="form-check-input">
                                        <span class="form-check-label ms-2">{{ $category->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Thương hiệu</label>
                            <div class="row g-2">
                                @foreach ($brands as $brand)
                                    <div class="col-6">
                                        <label class="form-check border rounded px-3 py-2 mb-0 h-100">
                                            <input type="checkbox" name="brands[]" value="{{ $brand->id }}" @checked(in_array((string) $brand->id, $selectedBrandIds, true)) class="form-check-input">
                                            <span class="form-check-label ms-2">{{ $brand->name }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Bảo hành</label>
                            <div class="d-grid gap-2">
                                @foreach ($warrantyOptions as $option)
                                    <label class="form-check border rounded px-3 py-2 mb-0">
                                        <input type="checkbox" name="warranties[]" value="{{ $option }}" @checked(in_array((string) $option, $selectedWarrantyMonths, true)) class="form-check-input">
                                        <span class="form-check-label ms-2">{{ $option === 0 ? 'Trọn đời' : $option.' tháng' }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dark w-100">Áp dụng</button>
                    </div>
                </form>
            </aside>

            <div class="col-lg-9">
                <div class="card shadow-sm mb-4">
                    <div class="card-body d-flex flex-column flex-lg-row gap-3 align-items-lg-center justify-content-between">
                        <div>
                            <p class="mb-1 fw-semibold">Danh sách sản phẩm</p>
                            <p class="mb-0 text-muted small">Hiển thị {{ $realProducts->count() }} sản phẩm phù hợp</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($sortOptions as $sortKey => $option)
                                <a href="{{ route('products.index', array_merge(request()->except(['sort', 'page']), ['sort' => $sortKey])) }}" class="btn btn-sm {{ $currentSort === $sortKey ? 'btn-dark' : 'btn-outline-dark' }}">
                                    {{ $option }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-md-2 row-cols-xl-4 justify-content-center">
                    @foreach ($realProducts as $item)
                        <div class="col mb-5">
                            <div class="card h-100 shadow-sm">
                                <a href="{{ $item['slug'] }}" class="position-relative text-decoration-none text-reset">
                                    <img class="card-img-top" src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="aspect-ratio: 1 / 1; object-fit: cover;" onerror="this.onerror=null;this.src='https://placehold.co/600x600/e5e7eb/1f2937?text=Hinh+san+pham';">
                                    <div class="position-absolute top-0 start-0 p-3 d-flex flex-column gap-2">
                                        @if (($item['saving_percent'] ?? 0) > 0)
                                            <span class="badge bg-danger">Tiết kiệm {{ $item['saving_percent'] }}%</span>
                                        @endif
                                        @if ($item['is_new'])
                                            <span class="badge bg-primary">Mới</span>
                                        @endif
                                    </div>
                                </a>
                                <div class="card-body p-4">
                                    <div class="text-center">
                                        <p class="text-muted small mb-1">{{ $item['category'] }} · {{ $item['brand'] }}</p>
                                        <h5 class="fw-bolder line-clamp-2-safe">{{ $item['name'] }}</h5>
                                        <div class="mt-3 mb-2">
                                            <span class="fw-bold text-dark fs-5">{{ number_format($item['price']) }} VND</span>
                                            @if (($item['original_price'] ?? 0) > $item['price'])
                                                <span class="text-muted text-decoration-line-through ms-2">{{ number_format($item['original_price']) }} VND</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted">{{ $item['warranty'] }}</div>
                                        <p class="text-muted small mt-3 mb-0">Hàng chính hãng, đổi mới nhanh khi lỗi do nhà sản xuất và được kích hoạt bảo hành theo serial riêng.</p>
                                    </div>
                                </div>
                                <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                    <div class="d-grid gap-2">
                                        <a href="{{ $item['slug'] }}" class="btn btn-outline-dark">Xem chi tiết</a>
                                        @if ($item['cart_url'] !== '#')
                                            <form method="POST" action="{{ $item['cart_url'] }}">
                                                @csrf
                                                @if (! empty($item['variant_id']))
                                                    <input type="hidden" name="variant_id" value="{{ $item['variant_id'] }}">
                                                @endif
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-dark w-100">Thêm vào giỏ</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if (isset($products) && method_exists($products, 'hasPages') && $products->hasPages())
                    <nav class="mt-4" aria-label="Phân trang sản phẩm">
                        <ul class="pagination justify-content-center flex-wrap gap-2">
                            @if ($products->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">Trang trước</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $products->previousPageUrl() }}">Trang trước</a></li>
                            @endif

                            @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                <li class="page-item {{ $page === $products->currentPage() ? 'active' : '' }}">
                                    @if ($page === $products->currentPage())
                                        <span class="page-link">{{ $page }}</span>
                                    @else
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    @endif
                                </li>
                            @endforeach

                            @if ($products->hasMorePages())
                                <li class="page-item"><a class="page-link" href="{{ $products->nextPageUrl() }}">Trang sau</a></li>
                            @else
                                <li class="page-item disabled"><span class="page-link">Trang sau</span></li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </section>
@endsection


