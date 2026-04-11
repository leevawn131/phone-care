<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $sort = (string) $request->query('sort', 'newest');
        $allowedSorts = ['discount', 'price_asc', 'price_desc', 'newest', 'popular'];
        $sort = in_array($sort, $allowedSorts, true) ? $sort : 'newest';

        $selectedCategoryIds = collect($request->input('categories', []))
            ->map(fn ($value): int => (int) $value)
            ->filter(fn (int $value): bool => $value > 0)
            ->values()
            ->all();

        $selectedBrandIds = collect($request->input('brands', []))
            ->map(fn ($value): int => (int) $value)
            ->filter(fn (int $value): bool => $value > 0)
            ->values()
            ->all();

        $selectedWarrantyMonths = collect($request->input('warranties', []))
            ->map(fn ($value): int => (int) $value)
            ->filter(fn (int $value): bool => $value >= 0)
            ->values()
            ->all();

        $minPrice = $request->filled('min_price') ? max(0, (int) $request->input('min_price')) : null;
        $maxPrice = $request->filled('max_price') ? max(0, (int) $request->input('max_price')) : null;

        if ($minPrice !== null && $maxPrice !== null && $maxPrice < $minPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }

        $effectivePriceExpression = "COALESCE((SELECT COALESCE(sale_price, price) FROM product_variants WHERE product_variants.product_id = products.id AND product_variants.is_active = 1 ORDER BY id ASC LIMIT 1), 0)";
        $maxPriceLimit = (int) Product::query()
            ->where('is_active', true)
            ->selectRaw("COALESCE(MAX({$effectivePriceExpression}), 0) as max_price_limit")
            ->value('max_price_limit');

        $products = Product::query()
            ->with([
                'category',
                'brand',
                'images',
                'variants' => fn ($query) => $query->where('is_active', true)->orderBy('id'),
            ])
            ->where('is_active', true)
            ->when($selectedCategoryIds !== [], fn ($query) => $query->whereIn('category_id', $selectedCategoryIds))
            ->when($selectedBrandIds !== [], fn ($query) => $query->whereIn('brand_id', $selectedBrandIds))
            ->when($selectedWarrantyMonths !== [], fn ($query) => $query->whereIn('base_warranty_months', $selectedWarrantyMonths))
            ->when($minPrice !== null, fn ($query) => $query->whereRaw("{$effectivePriceExpression} >= ?", [$minPrice]))
            ->when($maxPrice !== null, fn ($query) => $query->whereRaw("{$effectivePriceExpression} <= ?", [$maxPrice]))
            ->when($sort === 'discount', function ($query) {
                $query->orderByRaw('COALESCE((SELECT MAX(GREATEST(COALESCE(price, 0) - COALESCE(sale_price, price), 0)) FROM product_variants WHERE product_variants.product_id = products.id AND product_variants.is_active = 1), 0) DESC')
                    ->orderByDesc('created_at');
            })
            ->when($sort === 'price_asc', function ($query) {
                $query->orderByRaw('COALESCE((SELECT MIN(COALESCE(sale_price, price)) FROM product_variants WHERE product_variants.product_id = products.id AND product_variants.is_active = 1), 0) ASC')
                    ->orderByDesc('created_at');
            })
            ->when($sort === 'price_desc', function ($query) {
                $query->orderByRaw('COALESCE((SELECT MAX(COALESCE(sale_price, price)) FROM product_variants WHERE product_variants.product_id = products.id AND product_variants.is_active = 1), 0) DESC')
                    ->orderByDesc('created_at');
            })
            ->when($sort === 'popular', function ($query) {
                $query->withSum([
                    'orderItems as sold_qty' => function ($orderItemQuery) {
                        $orderItemQuery->whereHas('order', function ($orderQuery) {
                            $orderQuery->where('status', 'completed');
                        });
                    },
                ], 'qty')
                ->orderByDesc('sold_qty')
                ->orderByDesc('created_at');
            })
            ->when($sort === 'newest' || ! in_array($sort, ['discount', 'price_asc', 'price_desc', 'popular'], true), function ($query) {
                $query->latest();
            })
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        $brands = Brand::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $warrantyOptions = Product::query()
            ->where('is_active', true)
            ->distinct()
            ->orderBy('base_warranty_months')
            ->pluck('base_warranty_months')
            ->values();

        return view('products.index', compact(
            'products',
            'sort',
            'categories',
            'brands',
            'warrantyOptions',
            'selectedCategoryIds',
            'selectedBrandIds',
            'selectedWarrantyMonths',
            'minPrice',
            'maxPrice',
            'maxPriceLimit'
        ));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load([
            'category',
            'brand',
            'images',
            'variants' => fn ($query) => $query->where('is_active', true)->orderBy('id'),
        ]);

        $relatedProducts = Product::query()
            ->with([
                'category',
                'images',
                'variants' => fn ($query) => $query->where('is_active', true)->orderBy('id'),
            ])
            ->where('is_active', true)
            ->whereKeyNot($product->id)
            ->when(
                $product->category_id,
                fn ($query) => $query->where('category_id', $product->category_id),
                fn ($query) => $query->whereNotNull('id')
            )
            ->latest()
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}