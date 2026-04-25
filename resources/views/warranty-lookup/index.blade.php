@extends('layouts.shop')

@section('content')
    <div class="container mt-5 mb-5">
    <section class="mx-auto">
        <div class="rounded-2 bg-primary text-white p-5 text-center">
            <span class="badge bg-light text-primary" style="border: 1px solid rgba(255,255,255,0.3)">
                Tra cứu bảo hành
            </span>
            <h1 class="mt-4 fw-bold" style="font-size: 2rem;">Tra cứu thông tin bảo hành</h1>
            <p class="mx-auto mt-3" style="max-width: 42rem; font-size: 0.875rem; line-height: 1.75;">
                Nhập số điện thoại khách hàng hoặc mã serial number để kiểm tra tình trạng bảo hành, thời gian còn lại và thông tin mua hàng.
            </p>

            <form method="POST" action="{{ route('warranty-lookup.search') }}" class="mx-auto mt-4" style="max-width: 42rem;">
                @csrf
                <div class="d-flex gap-2">
                    <div class="d-flex justify-content-center align-items-center rounded-2" style="width: 56px; height: 56px; background-color: white; color: #2563eb; flex-shrink: 0;">
                        <svg class="" style="width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="search_query"
                        value="{{ $searchQuery }}"
                        placeholder="Ví dụ: 0900000000 hoặc PX-ABC123XYZ"
                        class="form-control form-control-sm"
                    >
                    <button type="submit" class="btn btn-dark btn-sm fw-bold">
                        Tìm kiếm
                    </button>
                </div>
            </form>

            <div class="mt-3 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.15em;">
                Tìm theo {{ $hasSearched ? ($searchType === 'phone' ? 'số điện thoại' : 'serial number') : 'số điện thoại hoặc serial number' }}
            </div>
        </div>

        @if ($hasSearched)
            <section class="mt-8">
                @if ($results->isNotEmpty())
                    <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Kết quả tra cứu</p>
                            <h2 class="mt-2 text-2xl font-bold text-gray-900">Tìm thấy {{ $results->count() }} bảo hành phù hợp</h2>
                        </div>
                        <p class="text-sm text-gray-500">Trạng thái hết hạn sẽ được cập nhật dựa trên ngày kết thúc.</p>
                    </div>

                    <div class="row row-cols-1 row-cols-lg-2 g-4">
                        @foreach ($results as $warranty)
                            @php
                                $status = strtolower((string) $warranty->status);
                                $badgeClasses = match ($status) {
                                    'active' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                    'claiming', 'claimed' => 'border-amber-200 bg-amber-50 text-amber-700',
                                    'expired' => 'border-red-200 bg-red-50 text-red-700',
                                    default => 'border-gray-200 bg-gray-50 text-gray-700',
                                };
                                $statusLabel = match ($status) {
                                    'active' => 'Active',
                                    'claiming', 'claimed' => 'Claiming',
                                    'expired' => 'Expired',
                                    default => ucfirst($status),
                                };
                                $remainingDays = $warranty->remaining_days;
                            @endphp

                            <article class="col">
            <div class="card rounded-2">
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <p class="text-uppercase" style="font-size: 0.75rem; color: #9ca3af;">Sản phẩm</p>
                                            <h3 class="mt-2 fw-bold" style="font-size: 1.5rem;">{{ $warranty->product_display_name }}</h3>
                                        </div>
                                        <span class="badge" style="border: 1px solid; {{ str_contains($badgeClasses, 'emerald') ? 'background-color: #ecfdf5; color: #047857; border-color: #a7f3d0;' : (str_contains($badgeClasses, 'red') ? 'background-color: #fef2f2; color: #991b1b; border-color: #fecaca;' : 'background-color: #f0f9ff; color: #0369a1; border-color: #bae6fd;') }} ">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>

                                <div class="mt-4 row gap-3 rounded-2 border border-1" style="border-color: #e5e7eb; background-color: #f9fafb; padding: 1.25rem;">
                                    <div class="col-md-6">
                                        <p class="text-uppercase" style="font-size: 0.75rem; color: #9ca3af;">Mã serial</p>
                                        <p class="mt-2 fw-bold" style="font-family: monospace; font-size: 1.125rem; color: #2563eb;">{{ $warranty->serial_display }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-uppercase" style="font-size: 0.75rem; color: #9ca3af;">Ngày mua</p>
                                        <p class="mt-2 fw-bold" style="color: #111827;">{{ $warranty->purchase_date_display ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-uppercase" style="font-size: 0.75rem; color: #9ca3af;">Thời hạn</p>
                                        <p class="mt-2 fw-bold text-sm text-muted">
                                            {{ $warranty->activated_at_display ?? 'N/A' }} - {{ $warranty->expires_at_display ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-uppercase" style="font-size: 0.75rem; color: #9ca3af;">Đơn hàng</p>
                                        <p class="mt-2 text-sm font-semibold text-gray-900">{{ $warranty->orderItem?->order?->order_number ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="mt-5 rounded-2xl px-5 py-4 {{ $status === 'expired' || ($remainingDays !== null && $remainingDays < 0) ? 'border border-red-200 bg-red-50' : 'border border-emerald-200 bg-emerald-50' }}">
                                    @if ($status === 'expired' || ($remainingDays !== null && $remainingDays < 0))
                                        <p class="text-lg font-bold text-red-700">Đã hết hạn</p>
                                        <p class="mt-1 text-sm text-red-600">Bảo hành của sản phẩm này đã quá thời gian hỗ trợ.</p>
                                    @else
                                        <p class="text-lg font-bold text-emerald-700">Còn {{ $remainingDays ?? 0 }} ngày bảo hành</p>
                                        <p class="mt-1 text-sm text-emerald-600">Sản phẩm vẫn nằm trong thời gian hỗ trợ bảo hành.</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center shadow-sm sm:px-10">
                        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 text-red-500">
                            <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                                <path d="M8.5 8.5l5 5"></path>
                                <path d="M13.5 8.5l-5 5"></path>
                            </svg>
                        </div>
                        <h2 class="mt-6 text-2xl font-bold text-gray-900">Không tìm thấy thông tin bảo hành</h2>
                        <p class="mx-auto mt-3 max-w-2xl text-sm leading-7 text-gray-500">
                            Không tìm thấy thông tin bảo hành, vui lòng kiểm tra lại mã hoặc số điện thoại.
                        </p>
                    </div>
                @endif
            </section>
        @endif
    </section>
@endsection