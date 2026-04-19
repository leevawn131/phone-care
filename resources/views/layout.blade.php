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

    <style>
        .chatbot-shell {
            position: fixed !important;
            inset: auto 1rem 1rem auto !important;
            z-index: 9999 !important;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .chatbot-toggle {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            border-radius: 999px;
            padding: 0.7rem 1rem;
            background: linear-gradient(135deg, #0284c7, #2563eb);
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.35);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .chatbot-toggle:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 32px rgba(37, 99, 235, 0.4);
        }

        .chatbot-panel {
            margin-top: 0.75rem;
            width: min(370px, calc(100vw - 2rem));
            border-radius: 20px;
            border: 1px solid #dbeafe;
            background: radial-gradient(circle at top right, #eff6ff, #ffffff 40%);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
            overflow: hidden;
            transform-origin: bottom right;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .chatbot-panel.is-hidden {
            opacity: 0;
            transform: scale(0.92) translateY(12px);
            pointer-events: none;
        }

        .chatbot-header {
            padding: 0.95rem 1rem;
            background: rgba(37, 99, 235, 0.08);
            border-bottom: 1px solid #dbeafe;
        }

        .chatbot-title {
            margin: 0;
            color: #1e3a8a;
            font-size: 0.95rem;
            font-weight: 700;
        }

        .chatbot-subtitle {
            margin: 0.2rem 0 0;
            color: #475569;
            font-size: 0.78rem;
        }

        .chatbot-box {
            height: 310px;
            overflow-y: auto;
            padding: 0.9rem;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .chatbot-bubble {
            max-width: 85%;
            margin-bottom: 0.6rem;
            padding: 0.6rem 0.75rem;
            border-radius: 14px;
            line-height: 1.45;
            font-size: 0.88rem;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .chatbot-bubble.user {
            margin-left: auto;
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            color: #ffffff;
            border-bottom-right-radius: 6px;
        }

        .chatbot-bubble.bot {
            margin-right: auto;
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #dbeafe;
            border-bottom-left-radius: 6px;
        }

        .chatbot-bubble.meta {
            margin-right: auto;
            background: #fff7ed;
            color: #9a3412;
            border: 1px solid #fed7aa;
            border-bottom-left-radius: 6px;
        }

        .chatbot-suggestions {
            display: grid;
            gap: 0.55rem;
            margin-bottom: 0.6rem;
        }

        .chatbot-product-card {
            display: grid;
            grid-template-columns: 54px 1fr;
            gap: 0.55rem;
            align-items: center;
            text-decoration: none;
            color: #0f172a;
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 12px;
            padding: 0.45rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .chatbot-product-card:hover {
            border-color: #60a5fa;
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.15);
        }

        .chatbot-product-card img {
            width: 54px;
            height: 54px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid #e5e7eb;
        }

        .chatbot-product-name {
            font-size: 0.8rem;
            font-weight: 600;
            line-height: 1.3;
        }

        .chatbot-product-meta {
            margin-top: 0.15rem;
            font-size: 0.75rem;
            color: #2563eb;
            font-weight: 600;
        }

        .chatbot-form {
            display: flex;
            gap: 0.5rem;
            padding: 0.8rem;
            border-top: 1px solid #dbeafe;
            background: #ffffff;
        }

        .chatbot-input {
            flex: 1;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 0.65rem 0.75rem;
            font-size: 0.9rem;
            outline: none;
        }

        .chatbot-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.16);
        }

        .chatbot-send {
            border: none;
            border-radius: 12px;
            padding: 0.62rem 0.95rem;
            background: #1d4ed8;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .chatbot-send:hover:not(:disabled) {
            background: #1e40af;
        }

        .chatbot-send:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        @media (max-width: 640px) {
            .chatbot-shell {
                right: 0.75rem !important;
                left: 0.75rem !important;
                bottom: 0.75rem !important;
                inset: auto 0.75rem 0.75rem 0.75rem !important;
            }

            .chatbot-toggle {
                margin-left: auto;
            }

            .chatbot-panel {
                width: 100%;
            }

            .chatbot-box {
                height: 260px;
            }
        }
    </style>

    <div class="chatbot-shell" id="chatbot-shell">
        <button type="button" class="chatbot-toggle" id="chatbot-toggle" aria-expanded="false" aria-controls="chatbot-panel">
            <span>Trợ giúp AI</span>
        </button>

        <section class="chatbot-panel is-hidden" id="chatbot-panel" role="dialog" aria-label="Chatbot tư vấn sản phẩm">
            <header class="chatbot-header">
                <p class="chatbot-title">Trợ lý PhoneCare</p>
                <p class="chatbot-subtitle">Hỏi về phụ kiện, giá bán và bảo hành</p>
            </header>

            <div class="chatbot-box" id="chat-box">
                <div class="chatbot-bubble bot">Xin chào, mình có thể gợi ý phụ kiện phù hợp cho bạn.</div>
            </div>

            <form class="chatbot-form" id="chatbot-form">
                <input id="msg" class="chatbot-input" autocomplete="off" placeholder="Nhập câu hỏi..." />
                <button type="submit" class="chatbot-send" id="chatbot-send">Gửi</button>
            </form>
        </section>
    </div>

    <script>
        (() => {
            const toggleBtn = document.getElementById('chatbot-toggle');
            const panel = document.getElementById('chatbot-panel');
            const form = document.getElementById('chatbot-form');
            const input = document.getElementById('msg');
            const sendButton = document.getElementById('chatbot-send');
            const chatBox = document.getElementById('chat-box');
            const fallbackImage = 'https://placehold.co/200x200/e5e7eb/1f2937?text=Hinh+san+pham';

            const appendMessage = (text, type = 'bot') => {
                const bubble = document.createElement('div');
                bubble.className = `chatbot-bubble ${type}`;
                bubble.textContent = text;
                chatBox.appendChild(bubble);
                chatBox.scrollTop = chatBox.scrollHeight;
                return bubble;
            };

            const appendSuggestedProducts = (products) => {
                if (!Array.isArray(products) || products.length === 0) {
                    return;
                }

                const wrapper = document.createElement('div');
                wrapper.className = 'chatbot-suggestions';

                products.forEach((product) => {
                    const card = document.createElement('a');
                    card.className = 'chatbot-product-card';
                    card.href = product.url || '#';
                    card.target = '_self';

                    const image = document.createElement('img');
                    image.src = product.image || fallbackImage;
                    image.alt = product.name || 'Sản phẩm';
                    image.onerror = () => {
                        image.onerror = null;
                        image.src = fallbackImage;
                    };

                    const content = document.createElement('div');
                    const name = document.createElement('p');
                    name.className = 'chatbot-product-name';
                    name.textContent = product.name || 'Sản phẩm';

                    const meta = document.createElement('p');
                    meta.className = 'chatbot-product-meta';
                    meta.textContent = product.price || 'Xem chi tiết';

                    content.appendChild(name);
                    content.appendChild(meta);

                    card.appendChild(image);
                    card.appendChild(content);
                    wrapper.appendChild(card);
                });

                chatBox.appendChild(wrapper);
                chatBox.scrollTop = chatBox.scrollHeight;
            };

            const renderHistory = (messages) => {
                chatBox.innerHTML = '';

                if (!Array.isArray(messages) || messages.length === 0) {
                    appendMessage('Xin chào, mình có thể gợi ý phụ kiện phù hợp cho bạn.', 'bot');
                    return;
                }

                messages.forEach((item) => {
                    appendMessage(item.message || '', item.is_bot ? 'bot' : 'user');
                });
            };

            const loadHistory = async () => {
                try {
                    const response = await fetch('/chat/history', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    renderHistory(payload.messages || []);
                } catch (error) {
                    // Skip history hydration silently when unavailable.
                }
            };

            const setSendingState = (sending) => {
                input.disabled = sending;
                sendButton.disabled = sending;
                sendButton.textContent = sending ? 'Đang gửi...' : 'Gửi';
            };

            const togglePanel = () => {
                const isHidden = panel.classList.toggle('is-hidden');
                toggleBtn.setAttribute('aria-expanded', String(!isHidden));
                if (!isHidden) {
                    input.focus();
                }
            };

            toggleBtn.addEventListener('click', togglePanel);

            loadHistory();

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const msg = input.value.trim();

                if (!msg) {
                    return;
                }

                appendMessage(msg, 'user');
                input.value = '';
                setSendingState(true);

                const loadingBubble = appendMessage('Đang suy nghĩ...', 'meta');

                try {
                    const response = await fetch('/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message: msg })
                    });

                    loadingBubble.remove();

                    if (!response.ok) {
                        appendMessage('Không thể kết nối chatbot. Vui lòng thử lại.', 'meta');
                        return;
                    }

                    let payload = null;

                    try {
                        payload = await response.json();
                    } catch (error) {
                        const text = await response.text();
                        payload = { reply: text, products: [] };
                    }

                    appendMessage(payload.reply || 'Tạm thời chưa có phản hồi.', 'bot');
                    appendSuggestedProducts(payload.products || []);
                } catch (error) {
                    loadingBubble.remove();
                    appendMessage('Đã xảy ra lỗi khi gửi tin nhắn.', 'meta');
                } finally {
                    setSendingState(false);
                    input.focus();
                }
            });
        })();
    </script>

</body>
</html>