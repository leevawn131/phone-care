@php
    $checkoutItems = isset($cartItems) && collect($cartItems)->isNotEmpty()
        ? collect($cartItems)->values()->map(function ($item, $index) {
            return [
                'id' => (string) ($item['id'] ?? $item['variant_id'] ?? ($index + 1)),
                'name' => $item['name'] ?? 'Phụ kiện điện thoại cao cấp',
                'variant_name' => $item['variant_name'] ?: 'Phiên bản tiêu chuẩn',
                'image' => $item['image'] ?? 'https://placehold.co/160x160/e5e7eb/1f2937?text=Hinh+san+pham',
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
@extends('layouts.shop')

@section('content')
<div class="container mt-5 mb-5">
<div
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
    x-cloak
>
    @if (session('error'))
        <div class="alert alert-danger mb-4" role="alert">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mb-4" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-dark text-white p-4 rounded-2 mb-4">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <p class="text-uppercase" style="font-size: 0.875rem; opacity: 0.9; margin: 0;">Thanh toán phụ kiện điện thoại</p>
                <h1 class="fw-bold mt-2" style="font-size: 2rem; margin: 0;">Xác nhận đơn hàng phụ kiện điện thoại</h1>
                <p class="mt-2" style="font-size: 0.875rem; opacity: 0.95; max-width: 42rem;">Thông tin nhận hàng rõ ràng, bảo hành minh bạch theo từng sản phẩm và phương thức thanh toán linh hoạt cho khách mua lẻ.</p>
            </div>
            <div class="col-lg-5">
                <div class="row row-cols-3 g-2 text-center" style="font-size: 0.875rem;">
                    <div class="col rounded-2" style="background-color: rgba(255,255,255,0.1); padding: 0.75rem;">
                        <p style="opacity: 0.9; margin: 0;">Giao nhanh</p>
                        <p class="fw-bold mt-1" style="margin: 0;">2h</p>
                    </div>
                    <div class="col rounded-2" style="background-color: rgba(255,255,255,0.1); padding: 0.75rem;">
                        <p style="opacity: 0.9; margin: 0;">Bảo hành</p>
                        <p class="fw-bold mt-1" style="margin: 0;">Theo serial</p>
                    </div>
                    <div class="col rounded-2" style="background-color: rgba(255,255,255,0.1); padding: 0.75rem;">
                        <p style="opacity: 0.9; margin: 0;">Thanh toán</p>
                        <p class="fw-bold mt-1" style="margin: 0;">Linh hoạt</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8 space-y-4">
            <form id="place-order-form" method="POST" action="{{ route('checkout.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="shipping_method" :value="shippingMethod">
                <input type="hidden" name="payment_method" :value="paymentMethod">
                <input type="hidden" name="voucher_code" :value="voucherCode">
                <input type="hidden" name="use_points" :value="usePoints ? 1 : 0">

                <div class="card border-0 rounded-2">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 44px; height: 44px; background-color: #fef2f2; color: #dc2626;">
                                <svg class="bi" style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11Z" />
                                    <circle cx="12" cy="10" r="2.5" />
                                </svg>
                            </div>
                            <div>
                                <p class="fw-bold m-0">Địa chỉ nhận hàng</p>
                                <p class="text-muted m-0" style="font-size: 0.875rem;">Điền chính xác để đơn giao thuận lợi hơn</p>
                            </div>
                        </div>
                        <button type="button" @click="handleAddressAction()" class="btn btn-sm btn-outline-primary">
                            <span x-text="addressActionLabel()"></span>
                        </button>
                    </div>

                    <div class="card-body">
                        @if ($requiresShippingInfo)
                            <div class="alert alert-warning" role="alert" style="font-size: 0.875rem;">
                                Tài khoản mới chưa có số điện thoại hoặc địa chỉ nhận hàng. Vui lòng nhập đầy đủ để tiếp tục đặt hàng.
                            </div>
                        @endif

                        <div class="rounded-2" style="background-color: #f9fafb; padding: 1rem;">
                            <div class="d-flex flex-wrap gap-3 align-items-center" style="font-size: 0.875rem;">
                                <p class="fw-bold m-0" x-text="recipientName || 'Chưa nhập tên người nhận'"></p>
                                <p class="fw-bold m-0" style="color: #6b7280;" x-text="recipientPhone || 'Chưa nhập số điện thoại'"></p>
                            </div>
                            <p class="mt-2 m-0" style="font-size: 0.875rem; line-height: 1.5; color: #6b7280;" x-text="addressLine || 'Chưa nhập địa chỉ nhận hàng'"></p>
                            <p class="mt-2 m-0 text-muted" style="font-size: 0.75rem;" x-text="fullAreaText()"></p>
                        </div>

                        <div x-show="editingAddress" x-cloak class="mt-4">
                            <template x-if="hasSavedAddresses()">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <p class="fw-bold m-0" style="font-size: 0.875rem;">Chọn địa chỉ đã lưu</p>
                                        <button
                                            type="button"
                                            @click="manualAddress = !manualAddress; if (manualAddress) { selectedSavedAddressId = null; }"
                                            class="btn-link text-primary p-0"
                                            style="font-size: 0.75rem; text-decoration: none;"
                                        >
                                            <span x-text="manualAddress ? 'Ẩn form nhập mới' : 'Nhập địa chỉ mới'"></span>
                                        </button>
                                    </div>

                                    <div class="space-y-2">
                                        <template x-for="address in savedAddresses" :key="address.id">
                                            <button
                                                type="button"
                                                @click="selectSavedAddress(address.id)"
                                                class="btn w-100 text-start rounded-2 p-3"
                                                :class="selectedSavedAddressId === address.id ? 'btn-light border-primary border-2' : 'btn-light border-1'"
                                                style="border-color: #e5e7eb; font-size: 0.875rem;"
                                            >
                                                <div class="d-flex justify-content-between align-items-start gap-2">
                                                    <div>
                                                        <p class="fw-bold m-0" x-text="address.full_name"></p>
                                                        <p class="text-muted m-0" x-text="address.phone || ''"></p>
                                                        <p class="text-muted m-0 mt-1" x-text="formatAddress(address)"></p>
                                                    </div>
                                                    <span class="badge bg-secondary" x-text="address.label"></span>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <div x-show="manualAddress || !hasSavedAddresses()" x-cloak>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="recipient_name" class="form-label" style="font-size: 0.875rem; font-weight: 600;">Họ và tên</label>
                                        <input id="recipient_name" type="text" x-model="recipientName" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="recipient_phone" class="form-label" style="font-size: 0.875rem; font-weight: 600;">Số điện thoại</label>
                                        <input id="recipient_phone" type="text" x-model="recipientPhone" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="province_code" class="form-label" style="font-size: 0.875rem; font-weight: 600;">Tỉnh / Thành</label>
                                        <input id="province_code" type="text" x-model="provinceCode" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="district_code" class="form-label" style="font-size: 0.875rem; font-weight: 600;">Quận / Huyện</label>
                                        <input id="district_code" type="text" x-model="districtCode" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="ward_code" class="form-label" style="font-size: 0.875rem; font-weight: 600;">Phường / Xã</label>
                                        <input id="ward_code" type="text" x-model="wardCode" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-12">
                                        <label for="address_line" class="form-label" style="font-size: 0.875rem; font-weight: 600;">Địa chỉ chi tiết</label>
                                        <textarea id="address_line" rows="3" x-model="addressLine" class="form-control form-control-sm"></textarea>
                                    </div>
                                </div>

                                <div x-show="!hasSavedAddresses()" x-cloak class="alert alert-warning mt-3" role="alert" style="font-size: 0.75rem;">
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
                    </div>
                </div>

                <div class="card border-0 rounded-2">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <p class="fw-bold m-0">Vận chuyển và ghi chú</p>
                    </div>

                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <label for="note" class="form-label fw-bold" style="font-size: 0.875rem;">Lời nhắn cho người bán</label>
                                <textarea id="note" name="note" rows="4" x-model="note" class="form-control" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao hàng..."></textarea>
                            </div>

                            <div class="col-lg-5">
                                <p class="fw-bold mb-3" style="font-size: 0.875rem;">Chọn phương thức vận chuyển</p>
                                <div class="space-y-2">
                                    <button type="button" @click="shippingMethod = 'express'" :class="shippingMethod === 'express' ? 'border-primary' : ''" class="btn btn-sm w-100 text-start p-3 border rounded-2" style="border-color: #e5e7eb;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="fw-bold m-0" style="font-size: 0.875rem;">Giao nhanh</p>
                                                <p class="text-muted m-0 mt-1" style="font-size: 0.75rem;">Ưu tiên nội thành, nhận hàng trong ngày.</p>
                                            </div>
                                            <span class="fw-bold text-primary" x-text="formatCurrency(shippingFees.express)"></span>
                                        </div>
                                    </button>
                                    <button type="button" @click="shippingMethod = 'saver'" :class="shippingMethod === 'saver' ? 'border-primary' : ''" class="btn btn-sm w-100 text-start p-3 border rounded-2" style="border-color: #e5e7eb;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="fw-bold m-0" style="font-size: 0.875rem;">Giao tiết kiệm</p>
                                                <p class="text-muted m-0 mt-1" style="font-size: 0.75rem;">Phù hợp đơn thông thường, chi phí tối ưu.</p>
                                            </div>
                                            <span class="fw-bold text-primary" x-text="formatCurrency(shippingFees.saver)"></span>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 rounded-2">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <p class="fw-bold m-0">Mã giảm giá và tích điểm</p>
                        <p class="text-muted m-0 mt-1" style="font-size: 0.75rem;">Điểm khả dụng: <span class="fw-bold text-primary">{{ number_format($availableLoyaltyPoints) }} điểm</span></p>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-7">
                                <label for="voucher_code" class="form-label fw-bold" style="font-size: 0.875rem;">Voucher / Mã giảm giá</label>
                                <div class="d-flex gap-2">
                                    <input id="voucher_code" type="text" x-model="voucherCode" class="form-control form-control-sm" placeholder="Nhập mã như PHUKIEN20">
                                    <button type="button" @click="voucherApplied = voucherCode.trim().length > 0" class="btn btn-primary btn-sm fw-bold">
                                        Áp dụng
                                    </button>
                                </div>
                                <p class="text-muted mt-2 m-0" style="font-size: 0.75rem;">Nhập mã để hệ thống áp dụng ưu đãi tạm tính ngay trên trang.</p>
                            </div>

                            <div class="col-lg-5">
                                <div class="rounded-2 p-3" style="background-color: #f9fafb; border: 1px solid #e5e7eb;">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            <p class="fw-bold m-0" style="font-size: 0.875rem;">Dùng điểm tích lũy</p>
                                            <p class="text-muted m-0 mt-1" style="font-size: 0.75rem;">
                                                Tối đa <span class="fw-bold text-muted">{{ number_format($maxRedeemPoints) }} điểm</span>
                                                (giảm {{ number_format($maxPointDiscount) }} VND) cho đơn hàng hiện tại.
                                            </p>
                                        </div>
                                        <button type="button" @click="toggleUsePoints()" :class="usePoints ? 'bg-primary' : 'bg-secondary'" class="btn btn-sm rounded-circle p-0" style="width: 28px; height: 28px; :disabled="availableLoyaltyPoints <= 0">
                                            <span :class="usePoints ? 'translate-x-3' : ''" class="d-inline-block" style="width: 20px; height: 20px; background-color: white; border-radius: 50%; transition: all 0.2s;"></span>
                                        </button>
                                    </div>
                                    <p x-show="availableLoyaltyPoints <= 0" x-cloak class="text-warning m-0 mt-2" style="font-size: 0.75rem;">Bạn chưa có điểm tích lũy để sử dụng.</p>
                                    <p class="text-success m-0 mt-2" style="font-size: 0.75rem;">
                                        Hoàn tất đơn này, bạn sẽ nhận khoảng <span class="fw-bold" x-text="new Intl.NumberFormat('vi-VN').format(estimatedEarnedPoints())"></span> điểm.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 rounded-2">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <p class="fw-bold m-0">Phương thức thanh toán</p>
                    </div>

                    <div class="card-body">
                        <div class="row row-cols-2 row-cols-md-4 g-2">
                            <div class="col">
                                <button type="button" @click="paymentMethod = 'cod'" :class="paymentMethod === 'cod' ? 'border-primary border-2' : ''" class="btn btn-light w-100 p-2 rounded-2 text-start" style="border: 1px solid #e5e7eb; font-size: 0.75rem;">
                                    <p class="fw-bold m-0">Thanh toán khi nhận hàng</p>
                                    <p class="text-muted m-0 mt-1">COD phù hợp cho đơn mua lẻ.</p>
                                </button>
                            </div>
                            <div class="col">
                                <button type="button" @click="paymentMethod = 'card'" :class="paymentMethod === 'card' ? 'border-primary border-2' : ''" class="btn btn-light w-100 p-2 rounded-2 text-start" style="border: 1px solid #e5e7eb; font-size: 0.75rem;">
                                    <p class="fw-bold m-0">Thẻ tín dụng</p>
                                    <p class="text-muted m-0 mt-1">Visa, MasterCard, JCB.</p>
                                </button>
                            </div>
                            <div class="col">
                                <button type="button" @click="paymentMethod = 'ewallet'" :class="paymentMethod === 'ewallet' ? 'border-primary border-2' : ''" class="btn btn-light w-100 p-2 rounded-2 text-start" style="border: 1px solid #e5e7eb; font-size: 0.75rem;">
                                    <p class="fw-bold m-0">Ví điện tử</p>
                                    <p class="text-muted m-0 mt-1">Momo, VNPay, ZaloPay.</p>
                                </button>
                            </div>
                            <div class="col">
                                <button type="button" @click="paymentMethod = 'bank_transfer'" :class="paymentMethod === 'bank_transfer' ? 'border-primary border-2' : ''" class="btn btn-light w-100 p-2 rounded-2 text-start" style="border: 1px solid #e5e7eb; font-size: 0.75rem;">
                                    <p class="fw-bold m-0">Chuyển khoản ngân hàng</p>
                                    <p class="text-muted m-0 mt-1">Xác nhận tự động sau thanh toán.</p>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="card border-0 rounded-2">
                <div class="card-header bg-transparent border-bottom py-3">
                    <p class="fw-bold m-0">Sản phẩm được đặt</p>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm m-0" style="font-size: 0.875rem;">
                            <thead class="table-light">
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <th style="font-weight: 600; color: #6b7280;">Sản phẩm</th>
                                    <th class="text-center" style="font-weight: 600; color: #6b7280;">Đơn giá</th>
                                    <th class="text-center" style="font-weight: 600; color: #6b7280;">Số lượng</th>
                                    <th class="text-end" style="font-weight: 600; color: #6b7280;">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @foreach ($checkoutItems as $item)
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <td>
                                            <div class="d-flex gap-2">
                                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;" onerror="this.onerror=null;this.src='https://placehold.co/160x160/e5e7eb/1f2937?text=Hinh+san+pham';">
                                                <div style="min-width: 0;">
                                                    <p class="fw-bold m-0">{{ $item['name'] }}</p>
                                                    <p class="text-muted m-0 mt-1" style="font-size: 0.75rem;">{{ $item['variant_name'] }}</p>
                                                    <div class="mt-1">
                                                        <span class="badge bg-info text-dark" style="font-size: 0.65rem;">Bảo hành {{ $item['warranty_months'] }} tháng</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center fw-bold">{{ number_format($item['price']) }} VND</td>
                                        <td class="text-center">{{ $item['quantity'] }}</td>
                                        <td class="text-end fw-bold text-primary">{{ number_format($item['price'] * $item['quantity']) }} VND</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 rounded-2 sticky-top" style="top: 24px;">
                <div class="card-header bg-transparent border-bottom py-3">
                    <p class="fw-bold m-0">Tổng thanh toán</p>
                </div>

                <div class="card-body">
                    <div class="space-y-3" style="font-size: 0.875rem;">
                        <div class="d-flex justify-content-between">
                            <span>Tổng tiền hàng</span>
                            <span class="fw-bold" x-text="formatCurrency(subtotal)"></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Phí vận chuyển</span>
                            <span class="fw-bold" x-text="formatCurrency(shippingFee())"></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Giảm giá tạm tính</span>
                            <span class="fw-bold text-success" x-text="'-' + formatCurrency(discountTotal())"></span>
                        </div>
                        <div class="rounded-2 p-3" style="background-color: #f9fafb; margin-top: 1rem;">
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <span class="fw-bold" style="font-size: 0.875rem; color: #6b7280;">Tổng thanh toán</span>
                                <span class="fw-bold" style="font-size: 1.5rem; color: #2563eb;" x-text="formatCurrency(grandTotal())"></span>
                            </div>
                            <p class="text-muted m-0 mt-2" style="font-size: 0.75rem; line-height: 1.5;">Đơn hàng sẽ được tạo ở trạng thái <span class="fw-bold text-muted">pending</span> để admin xác nhận trước khi kích hoạt bảo hành.</p>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 mt-4">
                        <button type="submit" form="place-order-form" class="btn btn-primary btn-sm fw-bold">
                            Đặt hàng
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm fw-bold">
                            Tiếp tục mua sắm
                        </a>
                    </div>
                </div>

                <div class="card-body border-top" style="background-color: #eff6ff; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem;">
                    <p class="fw-bold m-0" style="font-size: 0.875rem; color: #2563eb;">Cam kết cho khách mua phụ kiện</p>
                    <ul class="m-0 mt-2 ps-3" style="font-size: 0.875rem; color: #1e40af; line-height: 1.75;">
                        <li>Mỗi sản phẩm đều có thời hạn bảo hành hiển thị rõ và kích hoạt sau khi đơn hoàn tất.</li>
                        <li>Có thể tra cứu bảo hành bằng số điện thoại hoặc serial number bất kỳ lúc nào.</li>
                        <li>Hotline hỗ trợ kỹ thuật và đổi mới khi lỗi theo đúng chính sách từng sản phẩm.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
    <style>
        [x-cloak] { display: none !important; }
        .space-y-2 > * + * { margin-top: 0.5rem; }
        .space-y-3 > * + * { margin-top: 0.75rem; }
        .space-y-4 > * + * { margin-top: 1rem; }
        .space-y-6 > * + * { margin-top: 1.5rem; }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('checkoutPage', (initialData) => ({
                ...initialData,
                editingAddress: false,
                manualAddress: initialData.hasOldAddressInput,
                shippingFees: { express: 30000, saver: 15000 },
                voucherApplied: false,

                hasSavedAddresses() {
                    return Array.isArray(this.savedAddresses) && this.savedAddresses.length > 0;
                },
                findAddressById(id) {
                    return this.savedAddresses.find((addr) => String(addr.id) === String(id));
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
@endpush
