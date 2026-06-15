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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        /* Fix conflict between Bootstrap and precompiled Tailwind .collapse utility */
        .collapse {
            visibility: visible !important;
        }
    </style>
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
                </ul>
                <div class="d-flex gap-2">
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Hồ sơ</a></li>
                                <li><a class="dropdown-item" href="{{ route('warranty-lookup.index') }}"><i class="bi bi-shield-check me-2"></i>Tra cứu bảo hành</a></li>
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

    <!-- Chatbot AI Floating Widget -->
    <style>
        @keyframes phonecare-pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(13, 110, 253, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); }
        }
        .phonecare-typing-dot {
            width: 6px;
            height: 6px;
            background-color: #6b7280;
            border-radius: 50%;
            display: inline-block;
            animation: phonecare-typingBounce 1.4s infinite both;
        }
        @keyframes phonecare-typingBounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }
        .phonecare-toggle-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.4) !important;
        }
        .phonecare-hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .phonecare-hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
        }
        @media (max-width: 450px) {
            .phonecare-chat-window {
                width: calc(100% - 32px) !important;
                right: 16px !important;
                left: 16px !important;
                bottom: 88px !important;
                height: 450px !important;
            }
            .phonecare-toggle-btn {
                bottom: 16px !important;
                right: 16px !important;
            }
        }
    </style>

    <div x-data="phonecareChatbot()" class="phonecare-chatbot-container">
        <!-- Floating Toggle Button -->
        <button @click="toggleChat()" class="btn btn-primary rounded-circle shadow-lg position-fixed phonecare-toggle-btn" style="bottom: 24px; right: 24px; width: 60px; height: 60px; z-index: 1050; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; animation: phonecare-pulse 2s infinite;">
            <i x-show="!open" class="bi bi-chat-dots-fill fs-3 text-white"></i>
            <i x-show="open" class="bi bi-x-lg fs-3 text-white" style="display: none;"></i>
        </button>

        <!-- Chat Window -->
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-10 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-10 scale-95"
             class="card position-fixed shadow-lg phonecare-chat-window" 
             style="bottom: 96px; right: 24px; width: 380px; height: 500px; z-index: 1050; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; border: 1px solid rgba(0,0,0,0.08); background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); display: none;">
            
            <!-- Header -->
            <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between py-3 border-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-success rounded-circle" style="width: 10px; height: 10px;"></div>
                    <span class="fw-bold">Trợ lý ảo PhoneCare</span>
                </div>
                <button @click="toggleChat()" class="btn-close btn-close-white btn-sm" aria-label="Close"></button>
            </div>

            <!-- Messages Body -->
            <div x-ref="messageBox" class="card-body p-3 overflow-y-auto" style="flex: 1; background-color: #f8fafc;">
                <div class="d-flex flex-column gap-3">
                    <!-- Welcome Message -->
                    <template x-if="messages.length === 0">
                        <div class="d-flex align-items-start gap-2">
                            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;">
                                <i class="bi bi-robot"></i>
                            </div>
                            <div class="bg-white p-3 rounded-3 shadow-sm" style="max-width: 85%; border-radius: 4px 16px 16px 16px !important;">
                                <p class="mb-0 text-secondary" style="font-size: 0.9rem; line-height: 1.4;">Xin chào! Tôi là trợ lý ảo của PhoneCare. Tôi có thể tư vấn sản phẩm và giải đáp thông tin cho bạn. Bạn cần tìm phụ kiện gì hôm nay?</p>
                            </div>
                        </div>
                    </template>

                    <!-- Message List -->
                    <template x-for="msg in messages" :key="msg.id">
                        <div class="d-flex align-items-start gap-2" :class="msg.is_bot ? '' : 'justify-content-end'">
                            <!-- Bot Icon -->
                            <template x-if="msg.is_bot">
                                <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;">
                                    <i class="bi bi-robot"></i>
                                </div>
                            </template>

                            <div class="d-flex flex-column gap-2" style="max-width: 80%;">
                                <!-- Message Bubble -->
                                <div class="p-3 shadow-sm" 
                                     :class="msg.is_bot ? 'bg-white text-secondary' : 'bg-primary text-white'"
                                     :style="msg.is_bot ? 'border-radius: 4px 16px 16px 16px !important;' : 'border-radius: 16px 16px 4px 16px !important;'">
                                    <p class="mb-0 text-break" style="font-size: 0.9rem; line-height: 1.4;" x-text="msg.message"></p>
                                </div>

                                <!-- Product Suggestions -->
                                <template x-if="msg.products && msg.products.length > 0">
                                    <div class="d-flex flex-column gap-2 mt-1">
                                        <p class="text-uppercase text-muted fw-bold mb-0" style="font-size: 0.7rem; letter-spacing: 0.5px;">Sản phẩm gợi ý:</p>
                                        <template x-for="prod in msg.products" :key="prod.url">
                                            <a :href="prod.url" class="card text-decoration-none border-0 shadow-sm p-2 phonecare-hover-lift" style="background: #ffffff; border-radius: 8px;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <img :src="prod.image" :alt="prod.name" class="rounded" style="width: 45px; height: 45px; object-fit: cover;" onerror="this.onerror=null;this.src='https://placehold.co/100x100/e5e7eb/1f2937?text=Product';">
                                                    <div style="flex: 1; min-width: 0;">
                                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.8rem;" x-text="prod.name"></div>
                                                        <div class="text-muted" style="font-size: 0.75rem;" x-text="prod.brand"></div>
                                                    </div>
                                                    <div class="fw-bold text-primary" style="font-size: 0.8rem; white-space: nowrap;" x-text="prod.price"></div>
                                                </div>
                                            </a>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Typing Indicator -->
                    <template x-if="loading">
                        <div class="d-flex align-items-start gap-2">
                            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;">
                                <i class="bi bi-robot"></i>
                            </div>
                            <div class="bg-white p-3 rounded-3 shadow-sm d-flex align-items-center gap-1" style="border-radius: 4px 16px 16px 16px !important; width: fit-content;">
                                <span class="phonecare-typing-dot" style="animation-delay: 0s;"></span>
                                <span class="phonecare-typing-dot" style="animation-delay: 0.2s;"></span>
                                <span class="phonecare-typing-dot" style="animation-delay: 0.4s;"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Footer Input -->
            <div class="card-footer bg-white border-0 py-3 px-3">
                <form @submit.prevent="sendMessage()" class="d-flex gap-2">
                    <input x-model="inputMessage" 
                           type="text" 
                           placeholder="Hỏi trợ lý ảo về phụ kiện..." 
                           class="form-control form-control-sm border shadow-none" 
                           style="border-radius: 20px; font-size: 0.9rem; padding-left: 15px;"
                           required>
                    <button type="submit" :disabled="loading || !inputMessage.trim()" class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; flex-shrink: 0;">
                        <i class="bi bi-send-fill text-white" style="font-size: 0.9rem;"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('phonecareChatbot', () => ({
                open: false,
                messages: [],
                inputMessage: '',
                loading: false,
                historyLoaded: false,

                toggleChat() {
                    this.open = !this.open;
                    if (this.open && !this.historyLoaded) {
                        this.loadHistory();
                    }
                    if (this.open) {
                        this.scrollToBottom();
                    }
                },

                async loadHistory() {
                    try {
                        const res = await fetch('{{ url("/chat/history") }}');
                        if (res.ok) {
                            const data = await res.json();
                            this.messages = data.messages.map(msg => ({
                                id: msg.id,
                                message: msg.message,
                                is_bot: msg.is_bot === 1 || msg.is_bot === true || msg.is_bot === '1'
                            }));
                            this.historyLoaded = true;
                            this.scrollToBottom();
                        }
                    } catch (err) {
                        console.error('Failed to load chat history:', err);
                    }
                },

                async sendMessage() {
                    const text = this.inputMessage.trim();
                    if (!text || this.loading) return;

                    this.inputMessage = '';
                    
                    // Add user message to UI
                    const userMsgId = Date.now();
                    this.messages.push({
                        id: userMsgId,
                        message: text,
                        is_bot: false
                    });
                    
                    this.loading = true;
                    this.scrollToBottom();

                    try {
                        const response = await fetch('{{ url("/chat") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ message: text })
                        });

                        if (response.ok) {
                            const data = await response.json();
                            // Add bot message with suggestions
                            this.messages.push({
                                id: Date.now() + 1,
                                message: data.reply,
                                is_bot: true,
                                products: data.products || []
                            });
                        } else {
                            this.messages.push({
                                id: Date.now() + 1,
                                message: 'Xin lỗi, hệ thống AI đang bận. Bạn vui lòng thử lại sau.',
                                is_bot: true
                            });
                        }
                    } catch (err) {
                        console.error('Chat error:', err);
                        this.messages.push({
                            id: Date.now() + 1,
                            message: 'Không thể kết nối đến máy chủ trợ lý ảo.',
                            is_bot: true
                        });
                    } finally {
                        this.loading = false;
                        this.scrollToBottom();
                    }
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const box = this.$refs.messageBox;
                        if (box) {
                            box.scrollTop = box.scrollHeight;
                        }
                    });
                }
            }));
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('template/js/scripts.js') }}"></script>
    @stack('scripts')
</body>
</html>