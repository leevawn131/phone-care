<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class WarrantyLookupController extends Controller
{
    public function index(): View
    {
        return view('warranty-lookup.index', [
            'searchQuery' => '',
            'results' => collect(),
            'hasSearched' => false,
            'searchType' => null,
        ]);
    }

    public function search(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'search_query' => ['required', 'string', 'max:100'],
        ]);

        $searchQuery = trim($validated['search_query']);
        $searchType = $this->detectSearchType($searchQuery);
        $results = $searchType === 'phone'
            ? $this->searchByPhone($searchQuery)
            : $this->searchBySerial($searchQuery);

        return view('warranty-lookup.index', [
            'searchQuery' => $searchQuery,
            'results' => $this->prepareResults($results),
            'hasSearched' => true,
            'searchType' => $searchType,
        ]);
    }

    private function searchByPhone(string $searchQuery): Collection
    {
        $phoneCandidates = $this->buildPhoneCandidates($searchQuery);
        $normalizedPhoneExpression = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(recipient_phone, ' ', ''), '-', ''), '.', ''), '(', ''), ')', '')";

        return Warranty::query()
            ->with(['productSerial', 'orderItem.product', 'orderItem.order'])
            ->whereHas('orderItem.order', function ($query) use ($phoneCandidates, $normalizedPhoneExpression): void {
                $query->where(function ($phoneQuery) use ($phoneCandidates, $normalizedPhoneExpression): void {
                    foreach ($phoneCandidates as $phone) {
                        $phoneQuery->orWhere('recipient_phone', $phone)
                            ->orWhereRaw("{$normalizedPhoneExpression} = ?", [$phone]);
                    }
                });
            })
            ->latest('activated_at')
            ->get();
    }

    private function searchBySerial(string $searchQuery): Collection
    {
        $serialNumber = strtoupper(preg_replace('/\s+/', '', $searchQuery));

        return Warranty::query()
            ->with(['productSerial', 'orderItem.product', 'orderItem.order'])
            ->whereHas('productSerial', function ($query) use ($serialNumber): void {
                $query->where('serial_number', $serialNumber);
            })
            ->latest('activated_at')
            ->get();
    }

    private function prepareResults(Collection $warranties): Collection
    {
        $now = Carbon::now();

        return $warranties->map(function (Warranty $warranty) use ($now): Warranty {
            $expiresAt = $warranty->expires_at ? Carbon::parse($warranty->expires_at) : null;
            $activatedAt = $warranty->activated_at ? Carbon::parse($warranty->activated_at) : null;

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

            return $warranty;
        });
    }

    private function detectSearchType(string $searchQuery): string
    {
        return preg_match('/[A-Za-z-]/', $searchQuery) ? 'serial' : 'phone';
    }

    private function buildPhoneCandidates(string $searchQuery): array
    {
        $trimmed = trim($searchQuery);
        $normalized = preg_replace('/\D+/', '', $trimmed);
        $candidates = collect([$trimmed, $normalized])
            ->filter()
            ->values();

        if ($normalized && str_starts_with($normalized, '84')) {
            $candidates->push('0'.substr($normalized, 2));
        }

        if ($normalized && str_starts_with($normalized, '0')) {
            $candidates->push('84'.substr($normalized, 1));
        }

        return $candidates->filter()->unique()->values()->all();
    }
}