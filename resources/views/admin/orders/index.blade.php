<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin Order Management
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg px-4 py-3 shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            @forelse ($orders as $order)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6 space-y-5">
                        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $order->order_number }}</h3>
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gray-700">
                                        {{ $order->status }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">
                                    Customer: <span class="font-medium text-gray-800">{{ $order->recipient_name }}</span>
                                    <span class="mx-2 text-gray-300">|</span>
                                    {{ $order->recipient_phone }}
                                </p>
                                <p class="text-sm text-gray-600">Address: {{ $order->address_line }}</p>
                                <p class="text-sm text-gray-600">Total: {{ number_format($order->grand_total) }} VND</p>
                            </div>

                            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="flex flex-col sm:flex-row gap-3 xl:w-auto">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-w-[180px]">
                                    @foreach (['pending', 'processing', 'completed', 'cancelled'] as $status)
                                        <option value="{{ $status }}" @selected($order->status === $status)>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-primary-button>
                                    Update Status
                                </x-primary-button>
                            </form>
                        </div>

                        <div class="grid gap-6 lg:grid-cols-2">
                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <h4 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Order Items</h4>
                                <div class="mt-3 space-y-3">
                                    @foreach ($order->items as $item)
                                        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3">
                                            <div class="font-medium text-gray-900">{{ $item->product_name }}</div>
                                            <div class="mt-1 text-sm text-gray-600">
                                                Qty: {{ $item->qty }}
                                                <span class="mx-2 text-gray-300">|</span>
                                                Price: {{ number_format($item->unit_price) }} VND
                                                <span class="mx-2 text-gray-300">|</span>
                                                Warranty: {{ $item->warranty_months }} months
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <h4 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Generated Warranty Serials</h4>
                                <div class="mt-3 space-y-3">
                                    @forelse ($order->warranties as $warranty)
                                        <div class="rounded-lg border border-indigo-100 bg-white px-4 py-3">
                                            <div class="font-mono text-sm font-semibold text-indigo-700">
                                                {{ $warranty->productSerial?->serial_number ?? 'N/A' }}
                                            </div>
                                            <div class="mt-1 text-xs text-gray-500">
                                                Active: {{ optional($warranty->activated_at)->format('Y-m-d H:i') ?? 'N/A' }}
                                                <span class="mx-2 text-gray-300">|</span>
                                                Expires: {{ optional($warranty->expires_at)->format('Y-m-d H:i') ?? 'N/A' }}
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-lg border border-dashed border-gray-300 bg-white px-4 py-6 text-sm text-gray-500">
                                            No warranty serials generated yet.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-8 text-center text-gray-500">
                        No orders found.
                    </div>
                </div>
            @endforelse

            <div>
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>