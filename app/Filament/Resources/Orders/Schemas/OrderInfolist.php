<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Overview')
                    ->schema([
                        TextEntry::make('order_number'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (?string $state): string => OrderResource::getStatusColor($state)),
                        TextEntry::make('payment_status')
                            ->label('Payment Status')
                            ->badge(),
                        TextEntry::make('payment_method')
                            ->label('Payment Method'),
                        TextEntry::make('recipient_name')
                            ->label('Customer'),
                        TextEntry::make('recipient_phone')
                            ->label('Phone'),
                        TextEntry::make('address_line')
                            ->label('Address')
                            ->columnSpanFull(),
                        TextEntry::make('grand_total')
                            ->label('Grand Total')
                            ->formatStateUsing(fn (?int $state): string => OrderResource::formatMoney($state)),
                        TextEntry::make('placed_at')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('completed_at')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
                Section::make('Order Items')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->schema([
                                TextEntry::make('product_name')
                                    ->label('Product'),
                                TextEntry::make('variant_name')
                                    ->label('Variant'),
                                TextEntry::make('qty')
                                    ->label('Qty'),
                                TextEntry::make('unit_price')
                                    ->label('Unit Price')
                                    ->formatStateUsing(fn (?int $state): string => OrderResource::formatMoney($state)),
                                TextEntry::make('line_total')
                                    ->label('Line Total')
                                    ->formatStateUsing(fn (?int $state): string => OrderResource::formatMoney($state)),
                                TextEntry::make('warranty_months')
                                    ->label('Warranty')
                                    ->suffix(' months'),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('Generated Warranties')
                    ->schema([
                        RepeatableEntry::make('warranties')
                            ->schema([
                                TextEntry::make('warranty_code')
                                    ->label('Warranty Code'),
                                TextEntry::make('productSerial.serial_number')
                                    ->label('Serial Number'),
                                TextEntry::make('status')
                                    ->badge(),
                                TextEntry::make('activated_at')
                                    ->label('Activated At')
                                    ->dateTime('d/m/Y H:i'),
                                TextEntry::make('expires_at')
                                    ->label('Expires At')
                                    ->dateTime('d/m/Y H:i'),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}