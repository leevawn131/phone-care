@php
    $headerCartItems = collect(session('cart', []))->values();
    $headerCartCount = (int) $headerCartItems->sum('quantity');
    $headerUser = auth()->user();
    $headerDisplayName = $headerUser?->name ?: 'Tài khoản';
    $headerAvatar = mb_strtoupper(mb_substr($headerDisplayName, 0, 1));
@endphp
<header class="sticky top-0 z-50 border-b border-blue-100 bg-white/95 backdrop-blur-sm">
    <div class="border-b border-blue-100 bg-blue-50">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-2 text-[13px] text-blue-900 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('products.index') }}" class="transition hover:text-blue-600">Kênh người bán</a>
                <span class="hidden h-3.5 w-px bg-blue-200 md:block"></span>
                <a href="{{ route('products.index') }}" class="transition hover:text-blue-600">Tải ứng dụng</a>
                <span class="hidden h-3.5 w-px bg-blue-200 md:block"></span>
                <div class="flex items-center gap-2">
                    <a href="https://facebook.com" target="_blank" rel="noreferrer" class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white text-blue-600 shadow-sm transition hover:bg-blue-600 hover:text-white" aria-label="Facebook">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M13.5 21v-7h2.3l.3-2.7h-2.6V9.6c0-.8.2-1.3 1.4-1.3H16V5.9c-.2 0-.9-.1-1.8-.1-1.8 0-3.1 1.1-3.1 3.2v2.3H9v2.7h2.3v7h2.2Z" />
                        </svg>
                    </a>
                    <a href="https://instagram.com" target="_blank" rel="noreferrer" class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white text-pink-500 shadow-sm transition hover:bg-pink-500 hover:text-white" aria-label="Instagram">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7.8 3h8.4A4.8 4.8 0 0 1 21 7.8v8.4a4.8 4.8 0 0 1-4.8 4.8H7.8A4.8 4.8 0 0 1 3 16.2V7.8A4.8 4.8 0 0 1 7.8 3Zm0 1.8A3 3 0 0 0 4.8 7.8v8.4a3 3 0 0 0 3 3h8.4a3 3 0 0 0 3-3V7.8a3 3 0 0 0-3-3H7.8Zm8.85 1.35a.9.9 0 1 1 0 1.8.9.9 0 0 1 0-1.8ZM12 7.8A4.2 4.2 0 1 1 7.8 12 4.2 4.2 0 0 1 12 7.8Zm0 1.8A2.4 2.4 0 1 0 14.4 12 2.4 2.4 0 0 0 12 9.6Z" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('warranty-lookup.index') }}" class="text-sm transition hover:text-blue-600">Trợ giúp</a>

                @auth
                    <div class="relative z-50" x-data="{ openAccountMenu: false }" @keydown.escape.window="openAccountMenu = false">
                        <button
                            type="button"
                            @click="openAccountMenu = !openAccountMenu"
                            class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-white px-2 py-1.5 text-sm font-medium text-gray-700 shadow-sm transition hover:border-blue-300 hover:text-blue-600"
                        >
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">{{ $headerAvatar }}</span>
                            <span class="max-w-[140px] truncate">{{ $headerDisplayName }}</span>
                            <svg class="h-4 w-4 text-gray-400 transition" :class="openAccountMenu ? 'rotate-180 text-blue-600' : ''" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div
                            x-show="openAccountMenu"
                            @click.outside="openAccountMenu = false"
                            style="display: none;"
                            class="absolute right-0 top-full z-[70] mt-3 w-56 rounded-xl border border-gray-200 bg-white p-2 shadow-lg"
                        >
                            <a href="{{ route('profile.edit') }}" class="block rounded-lg px-4 py-2.5 text-sm text-gray-700 transition hover:bg-blue-50 hover:text-blue-600">Tài khoản của tôi</a>
                            <a href="{{ route('orders.purchases') }}" class="block rounded-lg px-4 py-2.5 text-sm text-gray-700 transition hover:bg-blue-50 hover:text-blue-600">Đơn mua</a>
                            @if ($headerUser?->isAdmin())
                                <a href="{{ url('/admin') }}" class="block rounded-lg px-4 py-2.5 text-sm text-gray-700 transition hover:bg-blue-50 hover:text-blue-600">Filament Admin</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full rounded-lg px-4 py-2.5 text-left text-sm text-red-500 transition hover:bg-red-50">Đăng xuất</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 text-sm">
                        <a href="{{ route('register') }}" class="transition hover:text-blue-600">Đăng ký</a>
                        <span class="text-blue-200">|</span>
                        <a href="{{ route('login') }}" class="font-medium transition hover:text-blue-600">Đăng nhập</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('products.index') }}" class="shrink-0">
                    <p class="text-2xl font-extrabold tracking-tight text-blue-600">PhoneCare</p>
                    <p class="text-xs uppercase tracking-[0.22em] text-gray-400">Accessories Store</p>
                </a>

                <form action="{{ route('products.index') }}" method="GET" class="hidden flex-1 md:block">
                    <div class="flex overflow-hidden rounded-xl border border-blue-200 bg-gray-50 shadow-sm">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm phụ kiện, ốp lưng, cáp sạc..." class="h-12 w-full border-0 bg-transparent px-4 text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-0">
                        <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 px-5 text-sm font-semibold text-white transition hover:bg-blue-700">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7"></circle>
                                <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-3.5-3.5"></path>
                            </svg>
                            Tìm
                        </button>
                    </div>
                </form>

                <div class="relative ml-auto">
                    <a href="{{ route('cart.index') }}" class="relative inline-flex h-12 w-12 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-700 shadow-sm transition hover:border-blue-600 hover:text-blue-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2 10h10l2-7H7" />
                            <circle cx="10" cy="19" r="1" />
                            <circle cx="17" cy="19" r="1" />
                        </svg>
                        <span class="absolute -right-1.5 -top-1.5 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[11px] font-bold text-white">{{ $headerCartCount }}</span>
                    </a>
                </div>
            </div>

            <form action="{{ route('products.index') }}" method="GET" class="md:hidden">
                <div class="flex overflow-hidden rounded-xl border border-blue-200 bg-gray-50 shadow-sm">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm phụ kiện, ốp lưng, cáp sạc..." class="h-11 w-full border-0 bg-transparent px-4 text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-0">
                    <button type="submit" class="inline-flex items-center justify-center bg-blue-600 px-4 text-white transition hover:bg-blue-700" aria-label="Tìm kiếm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-3.5-3.5"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</header>