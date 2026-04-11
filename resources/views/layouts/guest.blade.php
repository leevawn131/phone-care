<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PhoneCare') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gray-50 text-gray-800 antialiased">
        @include('layouts.header')

        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-8 lg:grid-cols-[1fr,420px] lg:items-center">
                <section class="rounded-2xl bg-gradient-to-r from-blue-600 via-blue-600 to-sky-500 px-6 py-10 text-white shadow-sm sm:px-10">
                    <p class="text-sm font-medium text-blue-100">PhoneCare Account</p>
                    <h1 class="mt-3 text-3xl font-bold sm:text-4xl">Mua hàng, theo dõi đơn và bảo hành trong một nơi</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-blue-50">
                        Đăng nhập hoặc tạo tài khoản để lưu thông tin mua hàng, quản lý địa chỉ và theo dõi lịch sử đơn mua nhanh hơn.
                    </p>
                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl bg-white/10 px-4 py-3">
                            <p class="text-xs uppercase tracking-[0.18em] text-blue-100">Mua hàng</p>
                            <p class="mt-1 text-lg font-bold">Nhanh</p>
                        </div>
                        <div class="rounded-xl bg-white/10 px-4 py-3">
                            <p class="text-xs uppercase tracking-[0.18em] text-blue-100">Đơn mua</p>
                            <p class="mt-1 text-lg font-bold">Rõ ràng</p>
                        </div>
                        <div class="rounded-xl bg-white/10 px-4 py-3">
                            <p class="text-xs uppercase tracking-[0.18em] text-blue-100">Bảo hành</p>
                            <p class="mt-1 text-lg font-bold">Theo serial</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                    {{ $slot }}
                </section>
            </div>
        </main>
    </body>
</html>