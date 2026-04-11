<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PhoneCare')</title>
    <meta name="description" content="Website bán phụ kiện điện thoại và quản lý bảo hành theo serial number.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50 text-gray-800">
    @include('layouts.header')

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
        @if (session('success'))
            <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm">
                <p class="font-semibold">Vui lòng kiểm tra lại thông tin:</p>
                <ul class="mt-2 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-blue-100 bg-white">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1.4fr,1fr,1fr] lg:px-8">
            <div>
                <p class="text-2xl font-extrabold tracking-tight text-blue-600">PhoneCare</p>
                <p class="mt-3 max-w-xl text-sm leading-7 text-gray-500">
                    Chuyên phụ kiện điện thoại chính hãng, giao diện mua hàng gọn gàng và bảo hành kích hoạt tự động theo serial number sau khi đơn hoàn tất.
                </p>
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-gray-900">Danh mục</p>
                <ul class="mt-3 space-y-2 text-sm text-gray-500">
                    <li>Ốp lưng, cáp sạc, củ sạc</li>
                    <li>Tai nghe, sạc dự phòng</li>
                    <li>Tra cứu bảo hành online</li>
                </ul>
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-gray-900">Hỗ trợ</p>
                <ul class="mt-3 space-y-2 text-sm text-gray-500">
                    <li>Mua hàng và theo dõi đơn</li>
                    <li>Kích hoạt bảo hành tự động</li>
                    <li>Giao nhanh, thanh toán linh hoạt</li>
                </ul>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>