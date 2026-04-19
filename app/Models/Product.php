<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'short_description',
        'description',
        'base_warranty_months',
        'has_serial_tracking',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'base_warranty_months' => 'integer',
            'has_serial_tracking' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants(): HasMany
    {
        return $this->variants()->where('is_active', true);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getDefaultVariant(): ?ProductVariant
    {
        if ($this->relationLoaded('variants')) {
            return $this->variants->where('is_active', true)->sortBy('id')->first()
                ?? $this->variants->sortBy('id')->first();
        }

        return $this->variants()->where('is_active', true)->orderBy('id')->first()
            ?? $this->variants()->orderBy('id')->first();
    }

    public function getPrimaryImage(): ?ProductImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images
                ->sortBy(fn (ProductImage $image): string => sprintf(
                    '%d-%06d-%06d',
                    $image->is_primary ? 0 : 1,
                    $image->sort_order,
                    $image->id
                ))
                ->first();
        }

        return $this->images()
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();
    }

    public function getPrimaryImageUrl(): string
    {
        return $this->getPrimaryImage()?->url
            ?? 'https://placehold.co/900x900/f8fafc/0f172a?text=Hinh+san+pham';
    }

    public function getDisplayPrice(): int
    {
        $variant = $this->getDefaultVariant();

        return (int) ($variant?->sale_price ?? $variant?->price ?? 0);
    }

    public function getOriginalPrice(): ?int
    {
        $variant = $this->getDefaultVariant();

        return $variant?->price;
    }
}