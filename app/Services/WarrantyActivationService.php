<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSerial;
use App\Models\Warranty;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class WarrantyActivationService
{
    public function activateForOrder(Order $order): Collection
    {
        $order->loadMissing(['items.product', 'items.warranties']);

        if ($order->status !== 'completed') {
            return collect();
        }

        $activatedAt = $order->completed_at ?? now();

        if (! $order->completed_at) {
            $order->forceFill(['completed_at' => $activatedAt])->saveQuietly();
        }

        $createdWarranties = collect();

        foreach ($order->items as $orderItem) {
            if ($orderItem->warranties->isNotEmpty() || ! $orderItem->product_variant_id) {
                continue;
            }

            $warrantyMonths = (int) ($orderItem->product?->base_warranty_months ?? $orderItem->warranty_months);
            $productSerial = $this->createProductSerial($orderItem, $activatedAt);

            $createdWarranties->push(Warranty::create([
                'warranty_code' => $this->generateWarrantyCode($orderItem->id),
                'product_serial_id' => $productSerial->id,
                'order_id' => $order->id,
                'order_item_id' => $orderItem->id,
                'user_id' => $order->user_id,
                'status' => 'active',
                'activated_at' => $activatedAt,
                'expires_at' => $activatedAt->copy()->addMonths($warrantyMonths),
                'warranty_months' => $warrantyMonths,
            ]));
        }

        return $createdWarranties;
    }

    protected function createProductSerial(OrderItem $orderItem, mixed $activatedAt): ProductSerial
    {
        return ProductSerial::create([
            'product_variant_id' => $orderItem->product_variant_id,
            'serial_number' => $this->generateSerialNumber(),
            'status' => 'sold',
            'order_item_id' => $orderItem->id,
            'received_at' => $activatedAt,
            'sold_at' => $activatedAt,
        ]);
    }

    protected function generateSerialNumber(): string
    {
        do {
            $serialNumber = 'PX-'.Str::upper(Str::random(10));
        } while (ProductSerial::query()->where('serial_number', $serialNumber)->exists());

        return $serialNumber;
    }

    protected function generateWarrantyCode(int $orderItemId): string
    {
        do {
            $warrantyCode = sprintf('WAR-%d-%s', $orderItemId, Str::upper(Str::random(8)));
        } while (Warranty::query()->where('warranty_code', $warrantyCode)->exists());

        return $warrantyCode;
    }
}