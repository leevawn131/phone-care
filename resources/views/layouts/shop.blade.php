<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'PhoneCare'))</title>
    <meta name="description" content="Website bán phụ kiện điện thoại và quản lý bảo hành theo serial number.">
    <link rel="icon" type="image/x-icon" href="{{ asset('template/assets/favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('template/css/styles.css') }}" rel="stylesheet">
    @vite(['resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand fw-bold text-primary" href="{{ route('products.index') }}">PhoneCare</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#shopNavbar" aria-controls="shopNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="shopNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('warranty-lookup.*') ? 'active' : '' }}" href="{{ route('warranty-lookup.index') }}">Tra cứu bảo hành</a>
                    </li>
                </ul>
                <div class="d-flex gap-2">
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Hồ sơ</a></li>
                                <li><a class="dropdown-item" href="{{ route('orders.purchases') }}"><i class="bi bi-bag me-2"></i>Đơn mua</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-dark">Đăng nhập</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-dark">Đăng ký</a>
                        @endif
                    @endauth
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-dark">
                        <i class="bi-cart-fill me-1"></i>
                        Giỏ hàng
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        @if (session('success'))
            <div class="container px-4 px-lg-5 mt-4">
                <div class="alert alert-success mb-0" role="alert">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="container px-4 px-lg-5 mt-4">
                <div class="alert alert-danger mb-0" role="alert">{{ session('error') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="container px-4 px-lg-5 mt-4">
                <div class="alert alert-danger mb-0" role="alert">
                    <p class="fw-semibold mb-2">Vui lòng kiểm tra lại thông tin:</p>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="py-5 bg-dark mt-5">
        <div class="container px-4 px-lg-5"><p class="m-0 text-center text-white">Copyright &copy; PhoneCare 2026</p></div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('template/js/scripts.js') }}"></script>
    @stack('scripts')
</body>
</html>