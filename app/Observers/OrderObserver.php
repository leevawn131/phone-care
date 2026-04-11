<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\LoyaltyPointService;
use App\Services\WarrantyActivationService;
use Illuminate\Support\Facades\DB;

class OrderObserver
{
    public function __construct(
        protected WarrantyActivationService $warrantyActivationService,
        protected LoyaltyPointService $loyaltyPointService
    ) {
    }

    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if (! $order->wasChanged('status') || $order->status !== 'completed') {
            return;
        }

        DB::transaction(function () use ($order): void {
            $order->loadMissing(['items.product', 'items.warranties', 'user']);
            $this->warrantyActivationService->activateForOrder($order);

            if ($order->points_awarded_at || ! $order->user) {
                return;
            }

            $earnedPoints = $this->loyaltyPointService->calculateEarnedPoints($order);
            if ($earnedPoints > 0) {
                $order->user->increment('loyalty_points', $earnedPoints);
            }

            $order->forceFill([
                'points_earned' => $earnedPoints,
                'points_awarded_at' => now(),
            ])->saveQuietly();
        });
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}