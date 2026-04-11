<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Resources\Products\ProductResource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Overview')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('slug'),
                        TextEntry::make('category.name')
                            ->label('Category'),
                        TextEntry::make('brand.name')
                            ->label('Brand'),
                        TextEntry::make('base_warranty_months')
                            ->label('Warranty')
                            ->suffix(' months'),
                        TextEntry::make('has_serial_tracking')
                            ->label('Serial Tracking')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Enabled' : 'Disabled'),
                        TextEntry::make('short_description')
                            ->columnSpanFull(),
                        TextEntry::make('description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Variants')
                    ->schema([
                        RepeatableEntry::make('variants')
                            ->schema([
                                TextEntry::make('variant_name')
                                    ->label('Variant'),
                                TextEntry::make('sku'),
                                TextEntry::make('price')
                                    ->formatStateUsing(fn (?int $state): string => ProductResource::formatMoney($state)),
                                TextEntry::make('sale_price')
                                    ->label('Sale Price')
                                    ->formatStateUsing(fn (?int $state): string => $state ? ProductResource::formatMoney($state) : 'N/A'),
                                TextEntry::make('stock'),
                                TextEntry::make('is_active')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive'),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('Images')
                    ->schema([
                        RepeatableEntry::make('images')
                            ->schema([
                                TextEntry::make('path')
                                    ->label('Path'),
                                TextEntry::make('alt_text')
                                    ->label('Alt Text'),
                                TextEntry::make('is_primary')
                                    ->label('Type')
                                    ->badge()
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Primary' : 'Secondary'),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}