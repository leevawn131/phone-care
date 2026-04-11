@php
    $checkoutItems = isset($cartItems) && collect($cartItems)->isNotEmpty()
        ? collect($cartItems)->values()->map(function ($item, $index) {
            return [
                'id' => (string) ($item['id'] ?? $item['variant_id'] ?? ($index + 1)),
                'name' => $item['name'] ?? 'Phụ kiện điện thoại cao cấp',
                'variant_name' => $item['variant_name'] ?: 'Phiên bản tiêu chuẩn',
                'image' => $item['image'] ?? 'https://placehold.co/160x160/e5e7eb/1f2937?text=Accessory',
                'price' => (int) ($item['price'] ?? 0),
                'quantity' => (int) ($item['quantity'] ?? 1),
                'warranty_months' => (int) ($item['warranty_months'] ?? 12),
                'sku' => $item['sku'] ?? 'SKU-ACC',
            ];
        })
        : collect([
            [
                'id' => '101',
                'name' => 'Ốp lưng chống sốc MagSafe cho iPhone 15 Pro Max',
                'variant_name' => 'Màu đen',
                'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=900&q=80',
                'price' => 289000,
                'quantity' => 1,
                'warranty_months' => 12,
                'sku' => 'CASE-IP15PM-BLK',
            ],
            [
                'id' => '102',
                'name' => 'Cáp sạc nhanh Type-C Baseus 100W bọc dù 1.2m',
                'variant_name' => 'Type-C to Type-C',
                'image' => 'https://images.unsplash.com/photo-1587033411391-5d9e51cce126?auto=format&fit=crop&w=900&q=80',
                'price' => 179000,
                'quantity' => 2,
                'warranty_months' => 24,
                'sku' => 'CABLE-BASEUS-100W',
            ],
        ]);

    $cartCount = (int) $checkoutItems->sum('quantity');
    $checkoutSubtotal = (int) $checkoutItems->sum(fn ($item) => $item['price'] * $item['quantity']);

    $savedAddresses = collect($savedAddresses ?? [])->values();
    $oldAddressId = old('selected_saved_address_id');
    $hasOldAddressInput = old('recipient_name') !== null || old('recipient_phone') !== null || old('address_line') !== null;

    $selectedSavedAddress = null;
    if ($oldAddressId !== null && $oldAddressId !== '') {
        $selectedSavedAddress = $savedAddresses->firstWhere('id', (string) $oldAddressId);
    }
    if (! $selectedSavedAddress && ! $hasOldAddressInput && $savedAddresses->isNotEmpty()) {
        $selectedSavedAddress = $savedAddresses->first();
    }

    $recipientName = old('recipient_name', $selectedSavedAddress['full_name'] ?? ($customer?->name ?? ''));
    $recipientPhone = old('recipient_phone', $selectedSavedAddress['phone'] ?? ($customer?->phone ?? ''));
    $addressLine = old('address_line', $selectedSavedAddress['address_line'] ?? '');
    $provinceCode = old('province_code', $selectedSavedAddress['province_code'] ?? '');
    $districtCode = old('district_code', $selectedSavedAddress['district_code'] ?? '');
    $wardCode = old('ward_code', $selectedSavedAddress['ward_code'] ?? '');
    $requiresShippingInfo = blank(trim((string) $recipientPhone)) || blank(trim((string) $addressLine));

    $noteValue = old('note', 'Giao giờ hành chính, gọi trước khi giao.');
    $selectedShippingMethod = old('shipping_method', 'express');
    $selectedPaymentMethod = old('payment_method', 'cod');
    $voucherCode = old('voucher_code', '');
    $usePoints = old('use_points', '0') === '1';
    $selectedSavedAddressId = old('selected_saved_address_id', $selectedSavedAddress['id'] ?? null);

    $loyaltyConfig = $loyaltyConfig ?? [];
    $availableLoyaltyPoints = (int) ($customer?->loyalty_points ?? 0);
    $pointValue = (int) ($loyaltyConfig['point_value'] ?? 100);
    $maxRedeemPointsPerOrder = (int) ($loyaltyConfig['max_redeem_points'] ?? 150);
    $earningStepAmount = (int) ($loyaltyConfig['earning_step_amount'] ?? 10000);
    $maxRedeemPoints = min($availableLoyaltyPoints, $maxRedeemPointsPerOrder);
    $maxPointDiscount = $maxRedeemPoints * $pointValue;
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thanh toán phụ kiện điện thoại</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body
    class="min-h-screen bg-gray-50 text-gray-800"
    x-data="checkoutPage({
        subtotal: {{ $checkoutSubtotal }},
        shippingMethod: @js($selectedShippingMethod),
        paymentMethod: @js($selectedPaymentMethod),
        voucherCode: @js($voucherCode),
        usePoints: @js($usePoints),
        recipientName: @js($recipientName),
        recipientPhone: @js($recipientPhone),
        addressLine: @js($addressLine),
        provinceCode: @js($provinceCode),
        districtCode: @js($districtCode),
        wardCode: @js($wardCode),
        requiresShippingInfo: @js($requiresShippingInfo),
        availableLoyaltyPoints: @js($availableLoyaltyPoints),
        pointValue: @js($pointValue),
        maxRedeemPoints: @js($maxRedeemPoints),
        earningStepAmount: @js($earningStepAmount),
        savedAddresses: @js($savedAddresses),
        selectedSavedAddressId: @js($selectedSavedAddressId),
        hasOldAddressInput: @js($hasOldAddressInput),
        note: @js($noteValue),
    })"
>
    @include('layouts.header')

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @if (session('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="mb-6 rounded-lg bg-gradient-to-r from-blue-600 via-blue-600 to-sky-500 px-6 py-6 text-white shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-100">Trang thanh toán phụ kiện điện thoại</p>
                    <h1 class="mt-1 text-3xl font-bold">Xác nhận đơn hàng phụ kiện điện thoại</h1>
                    <p class="mt-2 max-w-2xl text-sm text-blue-50">Thông tin nhận hàng rõ ràng, bảo hành minh bạch theo từng sản phẩm và phương thức thanh toán linh hoạt cho khách mua lẻ.</p>
                </div>
                <div class="grid grid-cols-3 gap-3 text-center text-sm">
                    <div class="rounded-lg bg-white/10 px-4 py-3">
                        <p class="text-blue-100">Giao nhanh</p>
                        <p class="mt-1 text-lg font-bold">2h</p>
                    </div>
                    <div class="rounded-lg bg-white/10 px-4 py-3">
                        <p class="text-blue-100">Bảo hành</p>
                        <p class="mt-1 text-lg font-bold">Theo serial</p>
                    </div>
                    <div class="rounded-lg bg-white/10 px-4 py-3">
                        <p class="text-blue-100">Thanh toán</p>
                        <p class="mt-1 text-lg font-bold">Linh hoạt</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1fr,360px]">
            <div class="space-y-6">
                <form id="place-order-form" method="POST" action="{{ route('checkout.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="shipping_method" :value="shippingMethod">
                    <input type="hidden" name="payment_method" :value="paymentMethod">
                    <input type="hidden" name="voucher_code" :value="voucherCode">
                    <input type="hidden" name="use_points" :value="usePoints ? 1 : 0">

                    <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="flex flex-col gap-4 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-red-50 text-red-500">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11Z" />
                                        <circle cx="12" cy="10" r="2.5" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-base font-semibold text-gray-900">Địa chỉ nhận hàng</p>
                                    <p class="text-sm text-gray-500">Điền chính xác để đơn giao thuận lợi hơn</p>
                                </div>
                            </div>
                            <button type="button" @click="handleAddressAction()" class="inline-flex items-center justify-center rounded-lg border border-blue-600 px-4 py-2 text-sm font-semibold text-blue-600 transition hover:bg-blue-600 hover:text-white">
                                <span x-text="addressActionLabel()"></span>
                            </button>
                        </div>

                        <div class="px-5 py-5">
                            @if ($requiresShippingInfo)
                                <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                                    Tài khoản mới chưa có số điện thoại hoặc địa chỉ nhận hàng. Vui lòng nhập đầy đủ để tiếp tục đặt hàng.
                                </div>
                            @endif

                            <div class="rounded-lg bg-gray-50 px-4 py-4">
                                <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:gap-4">
                                    <p class="text-base font-bold text-gray-900" x-text="recipientName || 'Chưa nhập tên người nhận'"></p>
                                    <p class="text-sm font-medium text-gray-600" x-text="recipientPhone || 'Chưa nhập số điện thoại'"></p>
                                </div>
                                <p class="mt-2 text-sm leading-6 text-gray-600" x-text="addressLine || 'Chưa nhập địa chỉ nhận hàng'"></p>
                                <p class="mt-2 text-xs text-gray-500" x-text="fullAreaText()"></p>
                            </div>

                            <div x-show="editingAddress" x-cloak class="mt-5 space-y-4">
                                <template x-if="hasSavedAddresses()">
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="text-sm font-semibold text-gray-800">Chọn địa chỉ đã lưu</p>
                                            <button
                                                type="button"
                                                @click="manualAddress = !manualAddress; if (manualAddress) { selectedSavedAddressId = null; }"
                                                class="text-xs font-semibold text-blue-600 hover:text-blue-700"
                                            >
                                                <span x-text="manualAddress ? 'Ẩn form nhập mới' : 'Nhập địa chỉ mới'"></span>
                                            </button>
                                        </div>

                                        <div class="grid gap-3">
                                            <template x-for="address in savedAddresses" :key="address.id">
                                                <button
                                                    type="button"
                                                    @click="selectSavedAddress(address.id)"
                                                    class="w-full rounded-lg border px-4 py-3 text-left transition"
                                                    :class="selectedSavedAddressId === address.id ? 'border-blue-600 bg-blue-50' : 'border-gray-200 bg-white hover:border-blue-300'"
                                                >
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div>
                                                            <p class="text-sm font-semibold text-gray-900" x-text="address.full_name"></p>
                                                            <p class="mt-1 text-xs text-gray-600" x-text="address.phone || ''"></p>
                                                            <p class="mt-2 text-xs leading-5 text-gray-600" x-text="formatAddress(address)"></p>
                                                        </div>
                                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-[11px] font-semibold text-gray-600" x-text="address.label"></span>
                                                    </div>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="manualAddress || !hasSavedAddresses()" x-cloak class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="recipient_name" class="mb-2 block text-sm font-semibold text-gray-700">Họ và tên</label>
                                        <input id="recipient_name" type="text" x-model="recipientName" class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm shadow-sm focus:border-blue-600 focus:outline-none focus:ring-0">
                                    </div>
                                    <div>
                                        <label for="recipient_phone" class="mb-2 block text-sm font-semibold text-gray-700">Số điện thoại</label>
                                        <input id="recipient_phone" type="text" x-model="recipientPhone" class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm shadow-sm focus:border-blue-600 focus:outline-none focus:ring-0">
                                    </div>
                                    <div>
                                        <label for="province_code" class="mb-2 block text-sm font-semibold text-gray-700">Tỉnh / Thành</label>
                                        <input id="province_code" type="text" x-model="provinceCode" class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm shadow-sm focus:border-blue-600 focus:outline-none focus:ring-0">
                                    </div>
                                    <div>
                                        <label for="district_code" class="mb-2 block text-sm font-semibold text-gray-700">Quận / Huyện</label>
                                        <input id="district_code" type="text" x-model="districtCode" class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm shadow-sm focus:border-blue-600 focus:outline-none focus:ring-0">
                                    </div>
                                    <div>
                                        <label for="ward_code" class="mb-2 block text-sm font-semibold text-gray-700">Phường / Xã</label>
                                        <input id="ward_code" type="text" x-model="wardCode" class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm shadow-sm focus:border-blue-600 focus:outline-none focus:ring-0">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="address_line" class="mb-2 block text-sm font-semibold text-gray-700">Địa chỉ chi tiết</label>
                                        <textarea id="address_line" rows="3" x-model="addressLine" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-blue-600 focus:outline-none focus:ring-0"></textarea>
                                    </div>
                                </div>

                                <div x-show="!hasSavedAddresses()" x-cloak class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-700">
                                    Bạn chưa có địa chỉ đã lưu. Vui lòng nhập địa chỉ mới để đặt hàng.
                                </div>
                            </div>

                            <input type="hidden" name="selected_saved_address_id" :value="selectedSavedAddressId ?? ''">
                            <input type="hidden" name="recipient_name" :value="recipientName">
                            <input type="hidden" name="recipient_phone" :value="recipientPhone">
                            <input type="hidden" name="address_line" :value="addressLine">
                            <input type="hidden" name="province_code" :value="provinceCode">
                            <input type="hidden" name="district_code" :value="districtCode">
                            <input type="hidden" name="ward_code" :value="wardCode">
                        </div>
                    </section>

                    <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-5 py-4">
                            <p class="text-base font-semibold text-gray-900">Vận chuyển và ghi chú</p>
                        </div>

                        <div class="grid gap-5 px-5 py-5 lg:grid-cols-[1.1fr,0.9fr]">
                            <div>
                                <label for="note" class="mb-2 block text-sm font-semibold text-gray-700">Lời nhắn cho người bán</label>
                                <textarea id="note" name="note" rows="4" x-model="note" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm focus:border-blue-600 focus:outline-none focus:ring-0" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao hàng..."></textarea>
                            </div>

                            <div>
                                <p class="mb-3 text-sm font-semibold text-gray-700">Chọn phương thức vận chuyển</p>
                                <div class="space-y-3">
                                    <button type="button" @click="shippingMethod = 'express'" :class="shippingMethod === 'express' ? 'border-blue-600 bg-blue-50' : 'border-gray-200 bg-white'" class="flex w-full items-start justify-between rounded-lg border px-4 py-4 text-left transition hover:border-blue-600">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">Giao nhanh</p>
                                            <p class="mt-1 text-xs text-gray-500">Ưu tiên nội thành, nhận hàng trong ngày.</p>
                                        </div>
                                        <span class="text-sm font-bold text-blue-600" x-text="formatCurrency(shippingFees.express)"></span>
                                    </button>
                                    <button type="button" @click="shippingMethod = 'saver'" :class="shippingMethod === 'saver' ? 'border-blue-600 bg-blue-50' : 'border-gray-200 bg-white'" class="flex w-full items-start justify-between rounded-lg border px-4 py-4 text-left transition hover:border-blue-600">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">Giao tiết kiệm</p>
                                            <p class="mt-1 text-xs text-gray-500">Phù hợp đơn thông thường, chi phí tối ưu.</p>
                                        </div>
                                        <span class="text-sm font-bold text-blue-600" x-text="formatCurrency(shippingFees.saver)"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-5 py-4">
                            <p class="text-base font-semibold text-gray-900">Mã giảm giá và tích điểm</p>
                            <p class="mt-1 text-xs text-gray-500">
                                Điểm khả dụng: <span class="font-semibold text-blue-600">{{ number_format($availableLoyaltyPoints) }} điểm</span>
                            </p>
                        </div>

                        <div class="grid gap-5 px-5 py-5 lg:grid-cols-[1fr,280px] lg:items-center">
                            <div>
                                <label for="voucher_code" class="mb-2 block text-sm font-semibold text-gray-700">Voucher / Mã giảm giá</label>
                                <div class="flex flex-col gap-3 sm:flex-row">
                                    <input id="voucher_code" type="text" x-model="voucherCode" class="h-11 flex-1 rounded-lg border border-gray-200 px-4 text-sm focus:border-blue-600 focus:outline-none focus:ring-0" placeholder="Nhập mã như PHUKIEN20">
                                    <button type="button" @click="voucherApplied = voucherCode.trim().length > 0" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                                        Áp dụng
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Nhập mã để hệ thống áp dụng ưu đãi tạm tính ngay trên trang.</p>
                            </div>

                            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Dùng điểm tích lũy</p>
                                        <p class="mt-1 text-xs text-gray-500">
                                            Tối đa <span class="font-semibold text-gray-700">{{ number_format($maxRedeemPoints) }} điểm</span>
                                            (giảm {{ number_format($maxPointDiscount) }} VND) cho đơn hàng hiện tại.
                                        </p>
                                    </div>
                                    <button type="button" @click="toggleUsePoints()" :class="usePoints ? 'bg-blue-600' : 'bg-gray-300'" class="relative inline-flex h-7 w-12 items-center rounded-full transition" :disabled="availableLoyaltyPoints <= 0">
                                        <span :class="usePoints ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-5 w-5 rounded-full bg-white transition"></span>
                                    </button>
                                </div>
                                <p x-show="availableLoyaltyPoints <= 0" x-cloak class="mt-2 text-xs text-amber-700">Bạn chưa có điểm tích lũy để sử dụng.</p>
                                <p class="mt-2 text-xs text-emerald-700">
                                    Hoàn tất đơn này, bạn sẽ nhận khoảng <span class="font-semibold" x-text="new Intl.NumberFormat('vi-VN').format(estimatedEarnedPoints())"></span> điểm.
                                </p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-5 py-4">
                            <p class="text-base font-semibold text-gray-900">Phương thức thanh toán</p>
                        </div>

                        <div class="grid gap-3 px-5 py-5 md:grid-cols-2 xl:grid-cols-4">
                            <button type="button" @click="paymentMethod = 'cod'" :class="paymentMethod === 'cod' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-700'" class="rounded-lg border px-4 py-4 text-left transition hover:border-blue-600">
                                <p class="text-sm font-semibold">Thanh toán khi nhận hàng</p>
                                <p class="mt-1 text-xs text-gray-500">COD phù hợp cho đơn mua lẻ.</p>
                            </button>
                            <button type="button" @click="paymentMethod = 'card'" :class="paymentMethod === 'card' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-700'" class="rounded-lg border px-4 py-4 text-left transition hover:border-blue-600">
                                <p class="text-sm font-semibold">Thẻ tín dụng</p>
                                <p class="mt-1 text-xs text-gray-500">Visa, MasterCard, JCB.</p>
                            </button>
                            <button type="button" @click="paymentMethod = 'ewallet'" :class="paymentMethod === 'ewallet' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-700'" class="rounded-lg border px-4 py-4 text-left transition hover:border-blue-600">
                                <p class="text-sm font-semibold">Ví điện tử</p>
                                <p class="mt-1 text-xs text-gray-500">Momo, VNPay, ZaloPay.</p>
                            </button>
                            <button type="button" @click="paymentMethod = 'bank_transfer'" :class="paymentMethod === 'bank_transfer' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-700'" class="rounded-lg border px-4 py-4 text-left transition hover:border-blue-600">
                                <p class="text-sm font-semibold">Chuyển khoản ngân hàng</p>
                                <p class="mt-1 text-xs text-gray-500">Xác nhận tự động sau thanh toán.</p>
                            </button>
                        </div>
                    </section>
                </form>

                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <div class="grid gap-3 text-sm font-semibold text-gray-500 md:grid-cols-[1.6fr,0.8fr,0.7fr,0.9fr]">
                            <span>Sản phẩm</span>
                            <span class="md:text-center">Đơn giá</span>
                            <span class="md:text-center">Số lượng</span>
                            <span class="md:text-right">Thành tiền</span>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach ($checkoutItems as $item)
                            <article class="grid gap-4 px-5 py-5 md:grid-cols-[1.6fr,0.8fr,0.7fr,0.9fr] md:items-center">
                                <div class="flex gap-4">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="h-24 w-24 rounded-lg border border-gray-100 object-cover" onerror="this.onerror=null;this.src='https://placehold.co/160x160/e5e7eb/1f2937?text=Accessory';">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold leading-6 text-gray-900">{{ $item['name'] }}</p>
                                        <p class="mt-1 text-xs text-gray-500">Phân loại: {{ $item['variant_name'] }}</p>
                                        <div class="mt-2 flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V7l7-4Z" />
                                                </svg>
                                                Bảo hành {{ $item['warranty_months'] }} tháng
                                            </span>
                                            <span class="text-xs text-gray-400">SKU: {{ $item['sku'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-sm font-semibold text-gray-900 md:text-center">
                                    {{ number_format($item['price']) }} VND
                                </div>

                                <div class="flex items-center gap-2 md:justify-center">
                                    @if ($item['quantity'] > 1)
                                        <form method="POST" action="{{ route('cart.update', $item['id']) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                            <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-blue-600 hover:text-blue-600">-</button>
                                        </form>
                                    @else
                                        <button type="button" disabled class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-100 text-gray-300">-</button>
                                    @endif

                                    <div class="flex h-9 min-w-[44px] items-center justify-center rounded-lg border border-gray-200 px-3 text-sm font-semibold text-gray-900">
                                        {{ $item['quantity'] }}
                                    </div>

                                    <form method="POST" action="{{ route('cart.update', $item['id']) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                        <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-blue-600 hover:text-blue-600">+</button>
                                    </form>
                                </div>

                                <div class="flex items-center justify-between gap-3 md:justify-end">
                                    <div class="text-right">
                                        <p class="text-base font-bold text-blue-600">{{ number_format($item['price'] * $item['quantity']) }} VND</p>
                                        <p class="text-xs text-gray-400">Đã gồm VAT</p>
                                    </div>
                                    <form method="POST" action="{{ route('cart.remove', $item['id']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-500 transition hover:bg-red-50">Xóa</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            </div>

            <aside class="space-y-6 xl:sticky xl:top-6 xl:self-start">
                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <p class="text-base font-semibold text-gray-900">Tổng thanh toán</p>
                    </div>

                    <div class="space-y-4 px-5 py-5 text-sm text-gray-600">
                        <div class="flex items-center justify-between">
                            <span>Tổng tiền hàng</span>
                            <span class="font-semibold text-gray-900" x-text="formatCurrency(subtotal)"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Phí vận chuyển</span>
                            <span class="font-semibold text-gray-900" x-text="formatCurrency(shippingFee())"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Giảm giá tạm tính</span>
                            <span class="font-semibold text-emerald-600" x-text="'-' + formatCurrency(discountTotal())"></span>
                        </div>
                        <div class="rounded-lg bg-gray-50 px-4 py-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600">Tổng thanh toán</span>
                                <span class="text-2xl font-bold text-blue-600" x-text="formatCurrency(grandTotal())"></span>
                            </div>
                            <p class="mt-2 text-xs leading-5 text-gray-500">Đơn hàng sẽ được tạo ở trạng thái <span class="font-semibold text-gray-700">pending</span> để admin xác nhận trước khi kích hoạt bảo hành.</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 px-5 py-5">
                        <button type="submit" form="place-order-form" class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-700">
                            Đặt hàng
                        </button>
                        <a href="{{ route('products.index') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-lg border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:border-blue-600 hover:text-blue-600">
                            Tiếp tục mua sắm
                        </a>
                    </div>
                </section>

                <section class="rounded-lg border border-blue-100 bg-blue-50 px-5 py-5 shadow-sm">
                    <p class="text-sm font-semibold text-blue-700">Cam kết cho khách mua phụ kiện</p>
                    <ul class="mt-3 space-y-3 text-sm text-blue-800">
                        <li class="flex gap-3">
                            <span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span>
                            <span>Mỗi sản phẩm đều có thời hạn bảo hành hiển thị rõ và kích hoạt sau khi đơn hoàn tất.</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span>
                            <span>Có thể tra cứu bảo hành bằng số điện thoại hoặc serial number bất kỳ lúc nào.</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span>
                            <span>Hotline hỗ trợ kỹ thuật và đổi mới khi lỗi theo đúng chính sách từng sản phẩm.</span>
                        </li>
                    </ul>
                </section>
            </aside>
        </div>
    </main>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('checkoutPage', (config) => ({
                subtotal: Number(config.subtotal ?? 0),
                shippingMethod: config.shippingMethod ?? 'express',
                paymentMethod: config.paymentMethod ?? 'cod',
                voucherCode: config.voucherCode ?? '',
                usePoints: Boolean(config.usePoints),
                voucherApplied: Boolean((config.voucherCode ?? '').trim().length),
                recipientName: config.recipientName ?? '',
                recipientPhone: config.recipientPhone ?? '',
                addressLine: config.addressLine ?? '',
                provinceCode: config.provinceCode ?? '',
                districtCode: config.districtCode ?? '',
                wardCode: config.wardCode ?? '',
                savedAddresses: Array.isArray(config.savedAddresses) ? config.savedAddresses : [],
                selectedSavedAddressId: config.selectedSavedAddressId ?? null,
                requiresShippingInfo: Boolean(config.requiresShippingInfo),
                availableLoyaltyPoints: Number(config.availableLoyaltyPoints ?? 0),
                pointValue: Number(config.pointValue ?? 100),
                maxRedeemPoints: Number(config.maxRedeemPoints ?? 0),
                earningStepAmount: Number(config.earningStepAmount ?? 10000),
                note: config.note ?? '',
                editingAddress: false,
                manualAddress: Boolean(config.hasOldAddressInput),
                shippingFees: {
                    express: 32000,
                    saver: 18000,
                },
                init() {
                    if (this.hasSavedAddresses() && ! this.manualAddress && this.selectedSavedAddressId) {
                        this.selectSavedAddress(this.selectedSavedAddressId, false);
                    }

                    if (this.requiresShippingInfo) {
                        this.editingAddress = true;
                        this.manualAddress = true;
                        this.selectedSavedAddressId = null;
                    }
                },
                hasSavedAddresses() {
                    return this.savedAddresses.length > 0;
                },
                findAddressById(id) {
                    return this.savedAddresses.find((address) => String(address.id) === String(id)) ?? null;
                },
                formatAddress(address) {
                    const parts = [
                        address.address_line,
                        address.ward_code,
                        address.district_code,
                        address.province_code,
                    ].filter((item) => String(item ?? '').trim().length > 0);

                    return parts.length ? parts.join(', ') : 'Chưa cập nhật địa chỉ';
                },
                fullAreaText() {
                    const parts = [this.wardCode, this.districtCode, this.provinceCode].filter((item) => String(item ?? '').trim().length > 0);

                    return parts.length ? parts.join(', ') : 'Chưa cập nhật khu vực';
                },
                selectSavedAddress(id, closeManual = true) {
                    const address = this.findAddressById(id);
                    if (! address) {
                        return;
                    }

                    this.selectedSavedAddressId = String(address.id);
                    this.recipientName = address.full_name ?? '';
                    this.recipientPhone = address.phone ?? '';
                    this.addressLine = address.address_line ?? '';
                    this.provinceCode = address.province_code ?? '';
                    this.districtCode = address.district_code ?? '';
                    this.wardCode = address.ward_code ?? '';

                    if (closeManual) {
                        this.manualAddress = false;
                    }
                },
                toggleAddressEditor() {
                    this.editingAddress = ! this.editingAddress;

                    if (this.editingAddress) {
                        this.manualAddress = ! this.hasSavedAddresses();
                    }
                },
                hasRequiredShippingInfo() {
                    return this.recipientPhone.trim().length > 0 && this.addressLine.trim().length > 0;
                },
                addressActionLabel() {
                    if (this.requiresShippingInfo && this.editingAddress) {
                        return 'Lưu';
                    }

                    return this.editingAddress ? 'Ẩn chỉnh sửa' : 'Thay đổi';
                },
                handleAddressAction() {
                    if (this.requiresShippingInfo && this.editingAddress) {
                        if (! this.hasRequiredShippingInfo()) {
                            window.alert('Vui lòng nhập số điện thoại và địa chỉ nhận hàng trước khi lưu.');
                            return;
                        }

                        this.requiresShippingInfo = false;
                        this.editingAddress = false;
                        return;
                    }

                    this.toggleAddressEditor();
                },
                shippingFee() {
                    return this.shippingFees[this.shippingMethod] ?? 0;
                },
                toggleUsePoints() {
                    if (this.availableLoyaltyPoints <= 0) {
                        return;
                    }

                    this.usePoints = ! this.usePoints;
                },
                voucherDiscount() {
                    return this.voucherCode.trim().length > 0 ? 20000 : 0;
                },
                pointsDiscount() {
                    if (! this.usePoints) {
                        return 0;
                    }

                    return this.maxRedeemPoints * this.pointValue;
                },
                discountTotal() {
                    return this.voucherDiscount() + this.pointsDiscount();
                },
                estimatedEarnedPoints() {
                    const eligibleAmount = Math.max(this.subtotal - this.discountTotal(), 0);
                    return Math.floor(eligibleAmount / this.earningStepAmount);
                },
                grandTotal() {
                    return Math.max(this.subtotal + this.shippingFee() - this.discountTotal(), 0);
                },
                formatCurrency(value) {
                    return new Intl.NumberFormat('vi-VN').format(Number(value || 0)) + ' VND';
                },
            }));
        });
    </script>
</body>
</html>
