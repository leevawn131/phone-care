<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\WarrantyActivationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $customer = User::query()->where('email', 'test@example.com')->firstOrFail();
        $variants = ProductVariant::query()->with('product')->orderBy('id')->take(5)->get();

        if ($variants->count() < 3) {
            return;
        }

        $definitions = [
            [
                'order_number' => 'ORD-DEMO-1001',
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'cod',
                'note' => 'Don hang demo cho trang quan ly admin.',
                'items' => [
                    ['variant' => $variants[0], 'qty' => 1],
                    ['variant' => $variants[1], 'qty' => 1],
                ],
            ],
            [
                'order_number' => 'ORD-DEMO-1002',
                'status' => 'processing',
                'payment_status' => 'paid',
                'payment_method' => 'bank_transfer',
                'note' => 'Don hang dang xu ly.',
                'items' => [
                    ['variant' => $variants[2], 'qty' => 1],
                ],
            ],
            [
                'order_number' => 'ORD-DEMO-1003',
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => 'momo',
                'note' => 'Don hang da hoan thanh de kiem tra observer bao hanh.',
                'items' => [
                    ['variant' => $variants[3], 'qty' => 1],
                    ['variant' => $variants[4], 'qty' => 1],
                ],
            ],
        ];

        foreach ($definitions as $definition) {
            DB::transaction(function () use ($customer, $definition): void {
                $subtotal = collect($definition['items'])->sum(function (array $item): int {
                    $variant = $item['variant'];
                    $unitPrice = (int) ($variant->sale_price ?? $variant->price);

                    return $unitPrice * (int) $item['qty'];
                });

                $order = Order::query()->firstOrCreate(
                    ['order_number' => $definition['order_number']],
                    [
                        'user_id' => $customer->id,
                        'status' => 'pending',
                        'payment_status' => $definition['payment_status'],
                        'payment_method' => $definition['payment_method'],
                        'currency' => 'VND',
                        'subtotal' => $subtotal,
                        'discount_total' => 0,
                        'shipping_fee' => 30000,
                        'grand_total' => $subtotal + 30000,
                        'recipient_name' => $customer->name,
                        'recipient_phone' => $customer->phone,
                        'province_code' => '79',
                        'district_code' => '760',
                        'ward_code' => '26734',
                        'address_line' => '123 Demo Street, Ho Chi Minh City',
                        'note' => $definition['note'],
                        'placed_at' => now()->subDays(2),
                    ]
                );

                if ($order->items()->doesntExist()) {
                    $items = collect($definition['items'])->map(function (array $item): array {
                        $variant = $item['variant'];
                        $product = $variant->product;
                        $unitPrice = (int) ($variant->sale_price ?? $variant->price);

                        return [
                            'product_id' => $product->id,
                            'product_variant_id' => $variant->id,
                            'product_name' => $product->name,
                            'variant_name' => $variant->variant_name,
                            'sku' => $variant->sku,
                            'unit_price' => $unitPrice,
                            'qty' => (int) $item['qty'],
                            'line_total' => $unitPrice * (int) $item['qty'],
                            'warranty_months' => (int) $product->base_warranty_months,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    })->all();

                    $order->items()->createMany($items);
                }

                if ($definition['status'] !== $order->status) {
                    $order->fill([
                        'status' => $definition['status'],
                        'completed_at' => $definition['status'] === 'completed' ? ($order->completed_at ?? now()->subDay()) : null,
                        'cancelled_at' => $definition['status'] === 'cancelled' ? ($order->cancelled_at ?? now()) : null,
                    ])->save();
                }
            });
        }

        $completedOrder = Order::query()
            ->with(['items.product', 'items.warranties'])
            ->where('order_number', 'ORD-DEMO-1003')
            ->first();

        if ($completedOrder && $completedOrder->warranties()->doesntExist()) {
            app(WarrantyActivationService::class)->activateForOrder($completedOrder);
        }
    }
}