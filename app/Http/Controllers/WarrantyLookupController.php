<?php

namespace App\Http\Controllers;

use App\Models\WarrantyClaim;
use App\Models\Warranty;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WarrantyLookupController extends Controller
{
    public function index(Request $request): View
    {
        $warranties = Warranty::query()
            ->with(['productSerial', 'orderItem.product', 'order', 'latestClaim'])
            ->where('user_id', $request->user()->id)
            ->latest('activated_at')
            ->get();

        return view('warranty-lookup.index', [
            'warranties' => $this->prepareResults($warranties),
        ]);
    }

    public function store(Request $request, Warranty $warranty): RedirectResponse
    {
        abort_unless($warranty->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'issue_description' => ['required', 'string', 'min:20', 'max:2000'],
            'attachments' => ['nullable', 'array', 'max:6'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,webp,mp4,mov,avi,webm', 'max:20480'],
        ]);

        $latestClaim = $warranty->latestClaim()->first();
        if ($latestClaim && in_array($latestClaim->status, ['pending', 'approved', 'received', 'in_progress'], true)) {
            return back()->with('error', 'Yêu cầu bảo hành cho sản phẩm này đang được xử lý.');
        }

        $attachmentPaths = collect($request->file('attachments', []))
            ->filter()
            ->map(function ($file): array {
                $path = $file->store('warranty-claims/'.now()->format('Y/m'), 'public');

                return [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            })
            ->values()
            ->all();

        WarrantyClaim::create([
            'claim_number' => $this->generateClaimNumber($warranty->id),
            'warranty_id' => $warranty->id,
            'handled_by' => null,
            'status' => 'pending',
            'issue_description' => $validated['issue_description'],
            'attachments' => $attachmentPaths,
        ]);

        return back()->with('success', 'Yêu cầu bảo hành đã được gửi và đang chờ xét duyệt.');
    }

    private function prepareResults(Collection $warranties): Collection
    {
        $now = Carbon::now();

        return $warranties->map(function (Warranty $warranty) use ($now): Warranty {
            $expiresAt = $warranty->expires_at ? Carbon::parse($warranty->expires_at) : null;
            $activatedAt = $warranty->activated_at ? Carbon::parse($warranty->activated_at) : null;
            $latestClaim = $warranty->latestClaim;

            if ($expiresAt && $expiresAt->lt($now) && $warranty->status !== 'expired') {
                $warranty->forceFill(['status' => 'expired'])->saveQuietly();
                $warranty->status = 'expired';
            }

            // Normalize to day boundaries so the UI always shows whole remaining days.
            $remainingDays = $expiresAt
                ? (int) $now->copy()->startOfDay()->diffInDays($expiresAt->copy()->startOfDay(), false)
                : null;
            $productName = $warranty->orderItem?->product_name
                ?? $warranty->orderItem?->product?->name
                ?? 'Sản phẩm không xác định';

            $warranty->setAttribute('product_display_name', $productName);
            $warranty->setAttribute('serial_display', $warranty->productSerial?->serial_number ?? 'N/A');
            $warranty->setAttribute('purchase_date_display', $warranty->orderItem?->order?->placed_at?->format('d/m/Y'));
            $warranty->setAttribute('remaining_days', $remainingDays);
            $warranty->setAttribute('activated_at_display', $activatedAt?->format('d/m/Y'));
            $warranty->setAttribute('expires_at_display', $expiresAt?->format('d/m/Y'));
            $warranty->setAttribute('claim_status', $latestClaim?->status);
            $warranty->setAttribute('claim_status_label', $latestClaim ? (WarrantyClaim::statusOptions()[$latestClaim->status] ?? ucfirst($latestClaim->status)) : null);
            $warranty->setAttribute('claim_status_color', $latestClaim ? WarrantyClaim::statusColor($latestClaim->status) : null);
            $warranty->setAttribute('can_request_claim', ! $latestClaim || ! in_array($latestClaim->status, ['pending', 'approved', 'received', 'in_progress'], true));
            $warranty->setAttribute('claim_attachments', $latestClaim?->attachments ?? []);
            $warranty->setAttribute('claim_resolution_note', $latestClaim?->resolution_note);
            $warranty->setAttribute('claim_technician_note', $latestClaim?->technician_note);

            return $warranty;
        });
    }

    private function generateClaimNumber(int $warrantyId): string
    {
        do {
            $claimNumber = sprintf('CLM-%d-%s', $warrantyId, Str::upper(Str::random(8)));
        } while (WarrantyClaim::query()->where('claim_number', $claimNumber)->exists());

        return $claimNumber;
    }
}