<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckWarrantyRequest;
use App\Models\Warranty;
use Illuminate\Http\JsonResponse;

class WarrantyController extends Controller
{
    public function check(CheckWarrantyRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $warranties = Warranty::query()
            ->with([
                'productSerial:id,serial_number',
                'order:id,recipient_phone',
                'orderItem:id,product_name',
                'user:id,phone',
            ])
            ->when($request->filled('serial_number'), function ($query) use ($validated) {
                $query->whereHas('productSerial', function ($productSerialQuery) use ($validated) {
                    $productSerialQuery->where('serial_number', $validated['serial_number']);
                });
            })
            ->when($request->filled('customer_phone'), function ($query) use ($validated) {
                $query->where(function ($phoneQuery) use ($validated) {
                    $phoneQuery
                        ->whereHas('order', function ($orderQuery) use ($validated) {
                            $orderQuery->where('recipient_phone', $validated['customer_phone']);
                        })
                        ->orWhereHas('user', function ($userQuery) use ($validated) {
                            $userQuery->where('phone', $validated['customer_phone']);
                        });
                });
            })
            ->orderByDesc('activated_at')
            ->get();

        if ($warranties->isEmpty()) {
            return response()->json([
                'message' => 'Warranty information not found.',
                'data' => [],
            ], 404);
        }

        $payload = $warranties->map(function (Warranty $warranty): array {
            return [
                'warranty_code' => $warranty->warranty_code,
                'serial_number' => $warranty->productSerial?->serial_number,
                'customer_phone' => $warranty->order?->recipient_phone ?? $warranty->user?->phone,
                'product_name' => $warranty->orderItem?->product_name,
                'start_date' => $warranty->activated_at?->toDateTimeString(),
                'end_date' => $warranty->expires_at?->toDateTimeString(),
                'status' => $this->resolveWarrantyStatus($warranty),
            ];
        })->values();

        if ($request->filled('serial_number') && ! $request->filled('customer_phone')) {
            return response()->json([
                'message' => 'Warranty information retrieved successfully.',
                'data' => $payload->first(),
            ]);
        }

        return response()->json([
            'message' => 'Warranty information retrieved successfully.',
            'data' => $payload,
        ]);
    }

    protected function resolveWarrantyStatus(Warranty $warranty): string
    {
        if ($warranty->status === 'voided') {
            return 'voided';
        }

        if ($warranty->expires_at && $warranty->expires_at->isPast()) {
            return 'expired';
        }

        return $warranty->status;
    }
}
