<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\LoyaltyPointService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    private const CART_KEY = 'cart';

    public function __construct(
        protected LoyaltyPointService $loyaltyPointService
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (! $request->user()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục thanh toán.');
        }

        $cartItems = collect(session(self::CART_KEY, []))->values();

        if ($cartItems->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Giỏ hàng đang trống. Hãy chọn sản phẩm trước khi thanh toán.');
        }

        $cartTotal = $this->calculateTotal($cartItems);
        $customer = $request->user();
        $savedAddresses = collect();

        if ($customer) {
            $savedAddresses = $customer->customerAddresses()
                ->orderByDesc('is_default')
                ->latest('id')
                ->get()
                ->map(function ($address): array {
                    return [
                        'id' => (string) $address->id,
                        'label' => $address->is_default ? 'Địa chỉ mặc định' : 'Địa chỉ đã lưu',
                        'full_name' => $address->full_name,
                        'phone' => $address->phone,
                        'address_line' => $address->address_line,
                        'province_code' => $address->province_code,
                        'district_code' => $address->district_code,
                        'ward_code' => $address->ward_code,
                        'is_default' => (bool) $address->is_default,
                    ];
                })
                ->values();

            $hasProfileAddress = filled($customer->address_line)
                || filled($customer->province_code)
                || filled($customer->district_code)
                || filled($customer->ward_code);

            if ($hasProfileAddress) {
                $savedAddresses->prepend([
                    'id' => 'profile-'.$customer->id,
                    'label' => 'Từ Profile',
                    'full_name' => $customer->name,
                    'phone' => $customer->phone,
                    'address_line' => $customer->address_line,
                    'province_code' => $customer->province_code,
                    'district_code' => $customer->district_code,
                    'ward_code' => $customer->ward_code,
                    'is_default' => true,
                ]);
            }
        }

        $loyaltyConfig = [
            'point_value' => LoyaltyPointService::POINT_VALUE,
            'max_redeem_points' => LoyaltyPointService::MAX_REDEEM_POINTS_PER_ORDER,
            'earning_step_amount' => LoyaltyPointService::EARNING_STEP_AMOUNT,
        ];

        return view('checkout.index', compact('cartItems', 'cartTotal', 'customer', 'savedAddresses', 'loyaltyConfig'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $request->user()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đặt hàng.');
        }

        $cartItems = collect(session(self::CART_KEY, []))->values();

        if ($cartItems->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Giỏ hàng đang trống.');
        }

        // Bổ sung validation cho các trường địa chỉ
        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'string', 'max:20'],
            'address_line' => ['required', 'string', 'max:500'],
            'province_code' => ['nullable', 'string', 'max:255'],
            'district_code' => ['nullable', 'string', 'max:255'],
            'ward_code' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['nullable', 'string', 'in:cod,card,ewallet,bank_transfer'],
            'shipping_method' => ['nullable', 'string', 'in:express,saver'],
            'voucher_code' => ['nullable', 'string', 'max:50'],
            'use_points' => ['nullable', 'boolean'],
        ]);

        $variantIds = $cartItems->pluck('variant_id')->map(fn ($id): int => (int) $id)->values();
        $variants = ProductVariant::query()
            ->with('product')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        if ($variants->count() !== $variantIds->count()) {
            return redirect()->route('cart.index')->with('error', 'Một số sản phẩm trong giỏ không còn tồn tại. Vui lòng kiểm tra lại giỏ hàng.');
        }

        $shippingMethod = $validated['shipping_method'] ?? 'express';
        $shippingFee = $this->resolveShippingFee($shippingMethod);
        $paymentMethod = $validated['payment_method'] ?? 'cod';
        $voucherCode = trim((string) ($validated['voucher_code'] ?? ''));
        $usePoints = $request->boolean('use_points');

        try {
            $order = DB::transaction(function () use ($request, $validated, $cartItems, $variants, $shippingFee, $paymentMethod, $voucherCode, $usePoints): Order {
                $orderLines = [];
                $subtotal = 0;
                $customer = User::query()->whereKey($request->user()->id)->lockForUpdate()->firstOrFail();

                foreach ($cartItems as $cartItem) {
                    $variant = $variants->get((int) $cartItem['variant_id']);
                    $product = $variant?->product;

                    if (! $variant || ! $product || ! $product->is_active || ! $variant->is_active) {
                        throw ValidationException::withMessages([
                            'cart' => 'Một sản phẩm trong giỏ không còn khả dụng.',
                        ]);
                    }

                    if ($variant->stock < (int) $cartItem['quantity']) {
                        throw ValidationException::withMessages([
                            'cart' => sprintf('Sản phẩm "%s" hiện chỉ còn %d sản phẩm.', $product->name, $variant->stock),
                        ]);
                    }

                    $unitPrice = (int) ($variant->sale_price ?? $variant->price);
                    $quantity = (int) $cartItem['quantity'];
                    $lineTotal = $unitPrice * $quantity;
                    $subtotal += $lineTotal;

                    $orderLines[] = [
                        'product_id' => $product->id,
                        'product_variant_id' => $variant->id,
                        'product_name' => $product->name,
                        'variant_name' => $variant->variant_name,
                        'sku' => $variant->sku,
                        'unit_price' => $unitPrice,
                        'qty' => $quantity,
                        'line_total' => $lineTotal,
                        'warranty_months' => (int) $product->base_warranty_months,
                    ];
                }

                $pointsRedeemed = $this->loyaltyPointService->calculateRedeemablePoints((int) $customer->loyalty_points, $usePoints);
                $pointsDiscount = $this->loyaltyPointService->pointsToCurrency($pointsRedeemed);
                $discountTotal = $this->calculateVoucherDiscount($voucherCode) + $pointsDiscount;
                $grandTotal = max($subtotal + $shippingFee - $discountTotal, 0);

                // Bổ sung các trường địa chỉ khi tạo Order
                $order = Order::query()->create([
                    'order_number' => $this->generateOrderNumber(),
                    'user_id' => $request->user()?->id,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'payment_method' => $paymentMethod,
                    'currency' => 'VND',
                    'subtotal' => $subtotal,
                    'discount_total' => $discountTotal,
                    'points_redeemed' => $pointsRedeemed,
                    'points_discount_total' => $pointsDiscount,
                    'shipping_fee' => $shippingFee,
                    'grand_total' => $grandTotal,
                    'points_earned' => 0,
                    'recipient_name' => $validated['recipient_name'],
                    'recipient_phone' => $validated['recipient_phone'],
                    'address_line' => $validated['address_line'],
                    'province_code' => $validated['province_code'] ?? null,
                    'district_code' => $validated['district_code'] ?? null,
                    'ward_code' => $validated['ward_code'] ?? null,
                    'note' => $validated['note'] ?? null,
                    'placed_at' => now(),
                ]);

                $order->items()->createMany($orderLines);

                if ($pointsRedeemed > 0) {
                    $customer->decrement('loyalty_points', $pointsRedeemed);
                }

                return $order;
            });
        } catch (ValidationException $exception) {
            return redirect()->route('cart.index')
                ->withErrors($exception->errors())
                ->with('error', collect($exception->errors())->flatten()->first());
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Không thể tạo đơn hàng lúc này. Vui lòng thử lại sau.');
        }

        $request->session()->forget(self::CART_KEY);

        return redirect()
            ->route('checkout.thank-you', $order->order_number)
            ->with('success', 'Đặt hàng thành công. Chúng tôi sẽ liên hệ xác nhận sớm nhất.');
    }

    public function thankYou(Request $request, Order $order): View
    {
        $user = $request->user();

        abort_unless(
            $user && ($user->isAdmin() || $order->user_id === $user->id),
            403
        );

        return view('checkout.thank-you', compact('order'));
    }

    private function calculateTotal(Collection $cartItems): int
    {
        return (int) $cartItems->sum(fn (array $item): int => $item['price'] * $item['quantity']);
    }

    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));
        } while (Order::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    private function resolveShippingFee(string $shippingMethod): int
    {
        return match ($shippingMethod) {
            'saver' => 18000,
            default => 32000,
        };
    }

    private function calculateVoucherDiscount(string $voucherCode): int
    {
        return $voucherCode !== '' ? 20000 : 0;
    }
}
