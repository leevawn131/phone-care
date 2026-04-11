<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\ProductVariant;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Information')
                    ->schema([
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),
                        Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->alphaDash()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('base_warranty_months')
                            ->label('Warranty Months')
                            ->numeric()
                            ->default(12)
                            ->minValue(0)
                            ->maxValue(60)
                            ->required(),
                        Toggle::make('has_serial_tracking')
                            ->label('Serial Tracking')
                            ->default(true),
                        Textarea::make('short_description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->rows(6)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Variants')
                    ->schema([
                        Repeater::make('variants')
                            ->relationship()
                            ->defaultItems(1)
                            ->minItems(1)
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['variant_name'] ?? $state['sku'] ?? null)
                            ->schema([
                                TextInput::make('variant_name')
                                    ->maxLength(255),
                                TextInput::make('sku')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('barcode')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('price')
                                    ->label('Price (VND)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required(),
                                TextInput::make('sale_price')
                                    ->label('Sale Price (VND)')
                                    ->numeric()
                                    ->minValue(0),
                                TextInput::make('cost_price')
                                    ->label('Cost Price (VND)')
                                    ->numeric()
                                    ->minValue(0),
                                TextInput::make('stock')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->required(),
                                Toggle::make('is_active')
                                    ->label('Variant Active')
                                    ->default(true),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),
                Section::make('Images')
                    ->schema([
                        Repeater::make('images')
                            ->relationship()
                            ->defaultItems(1)
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['alt_text'] ?? 'Product image')
                            ->schema([
                                Hidden::make('product_variant_id')->default(null),
                                TextInput::make('path')
                                    ->label('Image URL / Path')
                                    ->required()
                                    ->maxLength(2048)
                                    ->helperText('Paste an external URL or upload a file below.'),
                                FileUpload::make('uploaded_path')
                                    ->label('Upload Image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('products')
                                    ->dehydrated(false)
                                    ->afterStateUpdated(function (Set $set, string|array|null $state): void {
                                        if (is_array($state)) {
                                            $state = $state[0] ?? null;
                                        }

                                        if ($state) {
                                            $set('path', $state);
                                        }
                                    }),
                                TextInput::make('alt_text')
                                    ->maxLength(255),
                                TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                                Toggle::make('is_primary')
                                    ->label('Primary Image')
                                    ->default(false)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}