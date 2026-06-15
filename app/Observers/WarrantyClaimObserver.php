<?php

namespace App\Observers;

use App\Models\Warranty;
use App\Models\WarrantyClaim;

class WarrantyClaimObserver
{
    public function saved(WarrantyClaim $claim): void
    {
        $warranty = Warranty::query()->find($claim->warranty_id);

        if (! $warranty) {
            return;
        }

        $warranty->forceFill(['status' => $claim->status])->saveQuietly();

        $claimUpdates = [];

        if ($claim->status === 'received' && ! $claim->received_at) {
            $claimUpdates['received_at'] = now();
        }

        if (in_array($claim->status, ['rejected', 'completed'], true) && ! $claim->resolved_at) {
            $claimUpdates['resolved_at'] = now();
        }

        if ($claimUpdates !== []) {
            $claim->forceFill($claimUpdates)->saveQuietly();
        }
    }
}