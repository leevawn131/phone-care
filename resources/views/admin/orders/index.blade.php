@extends('layouts.shop')

@section('title', 'Admin Order Management')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0" style="font-size: 1.5rem;">Admin Order Management</h2>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            @forelse ($orders as $order)
                <div class="card border-0 rounded-2 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column gap-3 flex-xl-row justify-content-xl-between align-items-xl-start">
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h3 class="fw-bold m-0" style="font-size: 1.125rem;">{{ $order->order_number }}</h3>
                                    <span class="badge text-bg-secondary">
                                        {{ $order->status }}
                                    </span>
                                </div>
                                <p class="text-muted mt-2 mb-1" style="font-size: 0.875rem;">
                                    Customer: <span class="font-medium text-gray-800">{{ $order->recipient_name }}</span>
                                    <span class="mx-2">|</span>
                                    {{ $order->recipient_phone }}
                                </p>
                                <p class="text-muted mb-1" style="font-size: 0.875rem;">Address: {{ $order->address_line }}</p>
                                <p class="text-muted mb-0" style="font-size: 0.875rem;">Total: {{ number_format($order->grand_total) }} VND</p>
                            </div>

                            <form method="POST" action="{{ route('legacy-admin.orders.update-status', $order) }}" class="d-flex flex-column flex-sm-row gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm" style="min-width: 180px;">
                                    @foreach (['pending', 'processing', 'completed', 'cancelled'] as $status)
                                        <option value="{{ $status }}" @selected($order->status === $status)>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    Update Status
                                </button>
                            </form>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-lg-6">
                                <div class="rounded-2 border p-3" style="border-color: #e5e7eb; background-color: #f9fafb;">
                                    <h4 class="text-uppercase fw-bold m-0" style="font-size: 0.75rem; color: #6b7280;">Order Items</h4>
                                    <div class="mt-3" style="display: flex; flex-direction: column; gap: 0.75rem;">
                                    @foreach ($order->items as $item)
                                        <div class="rounded-2 border bg-white p-3" style="border-color: #e5e7eb;">
                                            <div class="fw-bold">{{ $item->product_name }}</div>
                                            <div class="mt-1 text-muted" style="font-size: 0.875rem;">
                                                Qty: {{ $item->qty }}
                                                <span class="mx-2">|</span>
                                                Price: {{ number_format($item->unit_price) }} VND
                                                <span class="mx-2">|</span>
                                                Warranty: {{ $item->warranty_months }} months
                                            </div>
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="rounded-2 border p-3" style="border-color: #e5e7eb; background-color: #f9fafb;">
                                    <h4 class="text-uppercase fw-bold m-0" style="font-size: 0.75rem; color: #6b7280;">Generated Warranty Serials</h4>
                                    <div class="mt-3" style="display: flex; flex-direction: column; gap: 0.75rem;">
                                    @forelse ($order->warranties as $warranty)
                                        <div class="rounded-2 border bg-white p-3" style="border-color: #bfdbfe;">
                                            <div class="fw-bold" style="font-family: monospace; color: #1d4ed8; font-size: 0.875rem;">
                                                {{ $warranty->productSerial?->serial_number ?? 'N/A' }}
                                            </div>
                                            <div class="mt-1 text-muted" style="font-size: 0.75rem;">
                                                Active: {{ optional($warranty->activated_at)->format('Y-m-d H:i') ?? 'N/A' }}
                                                <span class="mx-2">|</span>
                                                Expires: {{ optional($warranty->expires_at)->format('Y-m-d H:i') ?? 'N/A' }}
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-2 border border-dashed bg-white p-3 text-muted" style="font-size: 0.875rem;">
                                            No warranty serials generated yet.
                                        </div>
                                    @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 rounded-2 shadow-sm">
                    <div class="card-body text-center text-muted p-4">
                        No orders found.
                    </div>
                </div>
            @endforelse

            <div>
                {{ $orders->links() }}
            </div>
        </div>
    </div>
    @endsection