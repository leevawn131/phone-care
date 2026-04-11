<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Services\WarrantyActivationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class OrderController extends Controller
{
    public function store(
        StoreOrderRequest $request,
        WarrantyActivationService $warrantyActivationService
    ): JsonResponse {
        $validated = $request->validated();

        try {
            $order = DB::transaction(function () use ($validated, $request, $warrantyActivationService): Order {
                $variants = ProductVariant::query()
                    ->with('product:id,name,base_warranty_months')
                    ->whereIn('id', collect($validated['items'])->pluck('product_variant_id')->unique())
                    ->get()
                    ->keyBy('id');

                $orderItems = collect($validated['items'])->map(function (array $item) use ($variants): array {
                    $variant = $variants->get($item['product_variant_id']);
                    $product = $variant?->product;
                    $unitPrice = $item['unit_price'] ?? $variant?->sale_price ?? $variant?->price;

                    if (! $variant || ! $product || $unitPrice === null) {
                        throw new RuntimeException('One or more order items are invalid.');
                    }

                    return [
                        'product_id' => $product->id,
                        'product_variant_id' => $variant->id,
                        'product_name' => $product->name,
                        'variant_name' => $variant->variant_name,
                        'sku' => $variant->sku,
                        'unit_price' => (int) $unitPrice,
                        'qty' => (int) $item['qty'],
                        'line_total' => (int) $unitPrice * (int) $item['qty'],
                        'warranty_months' => (int) $product->base_warranty_months,
                    ];
                });

                $subtotal = (int) $orderItems->sum('line_total');
                $discountTotal = (int) ($validated['discount_total'] ?? 0);
                $shippingFee = (int) ($validated['shipping_fee'] ?? 0);
                $status = $validated['status'] ?? 'pending';
                $placedAt = $validated['placed_at'] ?? now();

                $order = Order::create([
                    'order_number' => $this->generateOrderNumber(),
                    'user_id' => $validated['user_id'] ?? $request->user()?->id,
                    'status' => $status,
                    'payment_status' => $validated['payment_status'] ?? 'pending',
                    'payment_method' => $validated['payment_method'] ?? null,
                    'currency' => 'VND',
                    'subtotal' => $subtotal,
                    'discount_total' => $discountTotal,
                    'shipping_fee' => $shippingFee,
                    'grand_total' => max($subtotal - $discountTotal + $shippingFee, 0),
                    'recipient_name' => $validated['recipient_name'],
                    'recipient_phone' => $validated['recipient_phone'],
                    'province_code' => $validated['province_code'] ?? null,
                    'district_code' => $validated['district_code'] ?? null,
                    'ward_code' => $validated['ward_code'] ?? null,
                    'address_line' => $validated['address_line'],
                    'note' => $validated['note'] ?? null,
                    'placed_at' => $placedAt,
                    'completed_at' => $status === 'completed' ? now() : null,
                    'cancelled_at' => $status === 'cancelled' ? now() : null,
                ]);

                $order->items()->createMany($orderItems->all());

                if ($status === 'completed') {
                    $warrantyActivationService->activateForOrder($order->fresh(['items.product', 'items.warranties']));
                }

                return $order->fresh([
                    'items.product',
                    'warranties.productSerial',
                ]);
            });

            return response()->json([
                'message' => 'Order created successfully.',
                'data' => $order,
            ], 201);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to create order.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        $validated = $request->validated();

        try {
            [$updatedOrder, $createdWarrantyCount] = DB::transaction(function () use ($validated, $order): array {
                $existingWarrantyCount = $order->warranties()->count();
                $status = $validated['status'];

                $order->status = $status;

                if ($status === 'completed' && ! $order->completed_at) {
                    $order->completed_at = now();
                }

                if (! $order->placed_at) {
                    $order->placed_at = now();
                }

                if ($status === 'cancelled' && ! $order->cancelled_at) {
                    $order->cancelled_at = now();
                }

                if ($status !== 'cancelled') {
                    $order->cancelled_at = null;
                }

                $order->save();

                $refreshedOrder = $order->fresh([
                    'items.product',
                    'warranties.productSerial',
                ]);

                $createdWarrantyCount = max(
                    $refreshedOrder->warranties->count() - $existingWarrantyCount,
                    0
                );

                return [$refreshedOrder, $createdWarrantyCount];
            });

            return response()->json([
                'message' => 'Order status updated successfully.',
                'activated_warranties_count' => $createdWarrantyCount,
                'data' => $updatedOrder,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to update order status.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    protected function generateOrderNumber(): string
    {
        do {
            $orderNumber = sprintf(
                'ORD-%s-%s',
                now()->format('Ymd'),
                Str::upper(Str::random(8))
            );
        } while (Order::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}