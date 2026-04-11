@extends('layout')

@section('title', 'Tra cứu bảo hành')

@section('content')
    <section class="mx-auto max-w-5xl">
        <div class="rounded-2xl bg-gradient-to-r from-blue-600 via-blue-600 to-sky-500 px-6 py-10 text-center text-white shadow-sm sm:px-10">
            <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.26em] text-blue-50">
                Tra cứu bảo hành
            </span>
            <h1 class="mt-5 text-3xl font-bold sm:text-5xl">Tra cứu thông tin bảo hành</h1>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-blue-50 sm:text-base">
                Nhập số điện thoại khách hàng hoặc mã serial number để kiểm tra tình trạng bảo hành, thời gian còn lại và thông tin mua hàng.
            </p>

            <form method="POST" action="{{ route('warranty-lookup.search') }}" class="mx-auto mt-8 max-w-3xl">
                @csrf
                <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/10 p-3 shadow-sm sm:flex-row sm:items-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white text-blue-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="search_query"
                        value="{{ $searchQuery }}"
                        placeholder="Ví dụ: 0900000000 hoặc PX-ABC123XYZ"
                        class="h-14 flex-1 rounded-xl border border-white/20 bg-white px-5 text-sm text-gray-700 placeholder:text-gray-400 focus:border-white focus:outline-none focus:ring-0"
                    >
                    <button type="submit" class="inline-flex h-14 items-center justify-center rounded-xl bg-blue-900 px-8 text-sm font-bold text-white transition hover:bg-slate-900">
                        Tìm kiếm
                    </button>
                </div>
            </form>

            <div class="mt-4 text-xs uppercase tracking-[0.22em] text-blue-100">
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

                    <div class="grid gap-6 lg:grid-cols-2">
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

                            <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Sản phẩm</p>
                                        <h3 class="mt-2 text-2xl font-bold text-gray-900">{{ $warranty->product_display_name }}</h3>
                                    </div>
                                    <span class="inline-flex rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] {{ $badgeClasses }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                <div class="mt-5 grid gap-4 rounded-2xl border border-gray-200 bg-gray-50 p-5 sm:grid-cols-2">
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.18em] text-gray-500">Mã serial</p>
                                        <p class="mt-2 font-mono text-lg font-semibold text-blue-600">{{ $warranty->serial_display }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.18em] text-gray-500">Ngày mua</p>
                                        <p class="mt-2 text-lg font-semibold text-gray-900">{{ $warranty->purchase_date_display ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.18em] text-gray-500">Thời hạn</p>
                                        <p class="mt-2 text-sm font-semibold text-gray-900">
                                            {{ $warranty->activated_at_display ?? 'N/A' }} - {{ $warranty->expires_at_display ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.18em] text-gray-500">Đơn hàng</p>
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