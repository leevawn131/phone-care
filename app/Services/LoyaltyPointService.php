<?php

namespace App\Services;

use App\Models\Order;

class LoyaltyPointService
{
    public const POINT_VALUE = 100;

    public const MAX_REDEEM_POINTS_PER_ORDER = 150;

    public const EARNING_STEP_AMOUNT = 10000;

    public function calculateRedeemablePoints(int $availablePoints, bool $usePoints): int
    {
        if (! $usePoints) {
            return 0;
        }

        return max(min($availablePoints, self::MAX_REDEEM_POINTS_PER_ORDER), 0);
    }

    public function pointsToCurrency(int $points): int
    {
        return max($points, 0) * self::POINT_VALUE;
    }

    public function calculateEarnedPoints(Order $order): int
    {
        $eligibleAmount = max(((int) $order->subtotal) - ((int) $order->discount_total), 0);

        return intdiv($eligibleAmount, self::EARNING_STEP_AMOUNT);
    }
}
