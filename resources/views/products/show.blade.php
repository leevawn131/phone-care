@extends('layouts.shop')

@section('title', $product->name)

@section('content')
    @php($defaultVariant = $product->getDefaultVariant())
    @php($displayPrice = $product->getDisplayPrice())
    @php($originalPrice = $product->getOriginalPrice())

    <header class="bg-dark py-5 mb-5">
        <div class="container px-4 px-lg-5 my-4">
            <div class="text-center text-white">
                <p class="lead fw-normal text-white-50 mb-2">Chi tiết sản phẩm</p>
                <h1 class="display-6 fw-bolder">{{ $product->name }}</h1>
            </div>
        </div>
    </header>

    <section class="container px-4 px-lg-5">
        <div class="row g-4 align-items-start">
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <img src="{{ $product->getPrimaryImageUrl() }}" alt="{{ $product->name }}" class="card-img-top" style="aspect-ratio: 1 / 1; object-fit: cover;" onerror="this.onerror=null;this.src='https://placehold.co/800x800/e5e7eb/1f2937?text=Hinh+san+pham';">
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge text-bg-light border">{{ $product->category?->name ?? 'Phụ kiện' }}</span>
                            @if ($product->brand)
                                <span class="badge text-bg-primary">{{ $product->brand->name }}</span>
                            @endif
                            <span class="badge text-bg-dark">Bảo hành {{ $product->base_warranty_months }} tháng</span>
                        </div>

                        <h2 class="fw-bolder">{{ $product->name }}</h2>
                        <p class="text-muted lead mt-3">
                            {{ $product->short_description ?: 'Phụ kiện được chọn lọc với thiết kế gọn gàng, độ hoàn thiện cao và thông tin bảo hành minh bạch cho người dùng.' }}
                        </p>

                        <div class="bg-light rounded-3 p-4 mt-4">
                            <p class="text-uppercase text-muted small mb-2">Giá bán</p>
                            <div class="d-flex align-items-end gap-3 flex-wrap">
                                <span class="fs-2 fw-bolder text-primary">{{ number_format($displayPrice) }} VND</span>
                                @if ($originalPrice && $originalPrice > $displayPrice)
                                    <span class="text-muted text-decoration-line-through fs-5">{{ number_format($originalPrice) }} VND</span>
                                @endif
                            </div>
                            <p class="mb-0 mt-2 text-muted">
                                {{ $defaultVariant && $defaultVariant->stock > 0 ? 'Còn '.$defaultVariant->stock.' sản phẩm trong kho.' : 'Hiện chưa có sẵn hàng cho sản phẩm này.' }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('cart.add', $product->slug) }}" class="mt-4">
                            @csrf

                            <div class="row g-3">
                                @if ($product->variants->count() > 1)
                                    <div class="col-md-7">
                                        <label for="variant_id" class="form-label fw-semibold">Chọn phiên bản</label>
                                        <select id="variant_id" name="variant_id" class="form-select">
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

                                <div class="col-md-5">
                                    <label for="quantity" class="form-label fw-semibold">Số lượng</label>
                                    <input id="quantity" type="number" name="quantity" min="1" max="20" value="1" class="form-control">
                                </div>

                                <div class="col-12">
                                    @if ($defaultVariant && $defaultVariant->stock > 0)
                                        <button type="submit" class="btn btn-dark btn-lg w-100">Thêm vào giỏ hàng</button>
                                    @else
                                        <button type="button" disabled class="btn btn-secondary btn-lg w-100">Tạm hết hàng</button>
                                    @endif
                                </div>
                            </div>
                        </form>

                        <div class="row g-3 mt-4">
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="text-uppercase text-muted small mb-1">Bảo hành</p>
                                    <p class="fw-bolder mb-0">{{ $product->base_warranty_months }} tháng</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="text-uppercase text-muted small mb-1">Serial</p>
                                    <p class="fw-bolder mb-0">Kích hoạt tự động</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="text-uppercase text-muted small mb-1">Thanh toán</p>
                                    <p class="fw-bolder mb-0">COD / Chuyển khoản</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container px-4 px-lg-5 mt-5">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm h-100">
                    <div class="card-body p-4 p-md-5">
                        <p class="text-uppercase text-muted small mb-3">Mô tả chi tiết</p>
                        <div class="lh-lg text-secondary">
                            {!! nl2br(e($product->description ?: $product->short_description ?: 'Sản phẩm được thiết kế cho nhu cầu sử dụng hàng ngày, cân bằng giữa thẩm mỹ, độ bền và trải nghiệm thực tế.')) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm h-100">
                    <div class="card-body p-4 p-md-5">
                        <p class="text-uppercase text-muted small mb-3">Phiên bản khả dụng</p>
                        <div class="d-grid gap-3">
                            @forelse ($product->variants as $variant)
                                <div class="border rounded-3 p-3">
                                    <div class="d-flex align-items-start justify-content-between gap-3">
                                        <div>
                                            <p class="fw-semibold mb-1">{{ $variant->variant_name ?: 'Phiên bản tiêu chuẩn' }}</p>
                                            <p class="text-muted small mb-0">SKU: {{ $variant->sku ?: 'N/A' }}</p>
                                        </div>
                                        <div class="text-end">
                                            <p class="fw-bold text-primary mb-0">{{ number_format($variant->sale_price ?? $variant->price) }} VND</p>
                                            <p class="text-muted small mb-0">Kho: {{ $variant->stock }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="border border-dashed rounded-3 p-3 text-muted">
                                    Chưa có phiên bản bán ra cho sản phẩm này.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section class="container px-4 px-lg-5 mt-5">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
                <div>
                    <p class="text-uppercase text-muted small mb-1">Gợi ý thêm</p>
                    <h3 class="fw-bolder mb-0">Sản phẩm liên quan</h3>
                </div>
                <a href="{{ route('products.index') }}" class="text-decoration-none fw-semibold">Xem toàn bộ</a>
            </div>

            <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-md-2 row-cols-xl-4 justify-content-center">
                @foreach ($relatedProducts as $relatedProduct)
                    @php($relatedVariant = $relatedProduct->getDefaultVariant())
                    <div class="col mb-5">
                        <div class="card h-100 shadow-sm">
                            <a href="{{ route('products.show', $relatedProduct->slug) }}">
                                <img class="card-img-top" src="{{ $relatedProduct->getPrimaryImageUrl() }}" alt="{{ $relatedProduct->name }}" style="aspect-ratio: 1 / 1; object-fit: cover;" onerror="this.onerror=null;this.src='https://placehold.co/600x600/e5e7eb/1f2937?text=Hinh+san+pham';">
                            </a>
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <p class="text-muted small mb-1">{{ $relatedProduct->category?->name ?? 'Phụ kiện' }}</p>
                                    <h5 class="fw-bolder">{{ $relatedProduct->name }}</h5>
                                    <div class="mt-3 fw-bold text-primary">{{ number_format($relatedProduct->getDisplayPrice()) }} VND</div>
                                </div>
                            </div>
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('products.show', $relatedProduct->slug) }}" class="btn btn-outline-dark">Xem chi tiết</a>
                                    @if ($relatedVariant && $relatedVariant->stock > 0)
                                        <form method="POST" action="{{ route('cart.add', $relatedProduct->slug) }}">
                                            @csrf
                                            <input type="hidden" name="variant_id" value="{{ $relatedVariant->id }}">
                                            <button type="submit" class="btn btn-dark w-100">Mua nhanh</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection