<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with(['items', 'warranties.productSerial'])
            ->latest()
            ->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['pending', 'processing', 'completed', 'cancelled'])],
        ]);

        $order->fill([
            'status' => $validated['status'],
            'placed_at' => $order->placed_at ?? now(),
            'completed_at' => $validated['status'] === 'completed'
                ? ($order->completed_at ?? now())
                : $order->completed_at,
            'cancelled_at' => $validated['status'] === 'cancelled'
                ? ($order->cancelled_at ?? now())
                : null,
        ])->save();

        $message = $validated['status'] === 'completed'
            ? 'Order marked as completed. Warranty serials have been generated.'
            : 'Order status updated successfully.';

        return back()->with('status', $message);
    }
}