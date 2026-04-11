<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CartController extends Controller
{
    private const CART_KEY = 'cart';

    public function index(): View
    {
        $cartItems = collect(session(self::CART_KEY, []))->values();
        $cartTotal = $this->calculateTotal($cartItems);

        return view('cart.index', compact('cartItems', 'cartTotal'));
    }

    public function addToCart(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $validated = $request->validate([
            'variant_id' => ['nullable', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $product->loadMissing(['images', 'variants']);

        $variant = $validated['variant_id']
            ? $product->variants->firstWhere('id', (int) $validated['variant_id'])
            : $product->getDefaultVariant();

        if (! $variant || ! $variant->is_active) {
            return back()->with('error', 'Phiên bản sản phẩm không khả dụng.');
        }

        if ($variant->stock < 1) {
            return back()->with('error', 'Sản phẩm hiện đang tạm hết hàng.');
        }

        $quantity = (int) ($validated['quantity'] ?? 1);
        $itemKey = (string) $variant->id;
        $cart = $this->getCart();
        $newQuantity = ($cart[$itemKey]['quantity'] ?? 0) + $quantity;
        $message = 'Sản phẩm đã được thêm vào giỏ hàng.';

        if ($newQuantity > $variant->stock) {
            $newQuantity = $variant->stock;
            $message = 'Số lượng trong giỏ đã được giới hạn theo tồn kho hiện tại.';
        }

        $cart[$itemKey] = $this->buildCartItem($product, $variant, $newQuantity);
        session([self::CART_KEY => $cart]);

        return back()->with('success', $message);
    }

    public function updateCart(Request $request, string $itemId): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $cart = $this->getCart();

        if (! Arr::has($cart, $itemId)) {
            return redirect()->route('cart.index')->with('error', 'Sản phẩm không còn trong giỏ hàng.');
        }

        $variant = ProductVariant::query()->with('product.images')->find($itemId);

        if (! $variant || ! $variant->is_active || ! $variant->product?->is_active) {
            unset($cart[$itemId]);
            session([self::CART_KEY => $cart]);

            return redirect()->route('cart.index')->with('error', 'Sản phẩm đã bị gỡ khỏi giỏ vì không còn khả dụng.');
        }

        if ($variant->stock < 1) {
            unset($cart[$itemId]);
            session([self::CART_KEY => $cart]);

            return redirect()->route('cart.index')->with('error', 'Sản phẩm này hiện đã hết hàng.');
        }

        $quantity = min((int) $validated['quantity'], $variant->stock);
        $message = $quantity === (int) $validated['quantity']
            ? 'Giỏ hàng đã được cập nhật.'
            : 'Số lượng đã được điều chỉnh theo tồn kho hiện tại.';

        $cart[$itemId] = $this->buildCartItem($variant->product, $variant, $quantity);
        session([self::CART_KEY => $cart]);

        return redirect()->route('cart.index')->with('success', $message);
    }

    public function remove(string $itemId): RedirectResponse
    {
        $cart = $this->getCart();

        if (Arr::has($cart, $itemId)) {
            unset($cart[$itemId]);
            session([self::CART_KEY => $cart]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    private function getCart(): array
    {
        return session(self::CART_KEY, []);
    }

    private function buildCartItem(Product $product, ProductVariant $variant, int $quantity): array
    {
        return [
            'id' => $variant->id,
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'slug' => $product->slug,
            'name' => $product->name,
            'variant_name' => $variant->variant_name,
            'price' => (int) ($variant->sale_price ?? $variant->price),
            'quantity' => $quantity,
            'image' => $product->getPrimaryImageUrl(),
            'sku' => $variant->sku,
            'warranty_months' => $product->base_warranty_months,
        ];
    }

    private function calculateTotal(Collection $cartItems): int
    {
        return (int) $cartItems->sum(fn (array $item): int => $item['price'] * $item['quantity']);
    }
}