<?php

namespace App\Filament\Resources\Warranties\Schemas;

use App\Filament\Resources\Warranties\WarrantyResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WarrantyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Warranty Overview')
                    ->schema([
                        TextEntry::make('warranty_code')
                            ->label('Warranty Code'),
                        TextEntry::make('productSerial.serial_number')
                            ->label('Serial Number'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (?string $state): string => WarrantyResource::getStatusColor($state)),
                        TextEntry::make('warranty_months')
                            ->label('Warranty')
                            ->suffix(' months'),
                        TextEntry::make('order.order_number')
                            ->label('Order Number'),
                        TextEntry::make('orderItem.product_name')
                            ->label('Product')
                            ->columnSpanFull(),
                        TextEntry::make('order.recipient_name')
                            ->label('Customer'),
                        TextEntry::make('order.recipient_phone')
                            ->label('Customer Phone'),
                        TextEntry::make('activated_at')
                            ->label('Start Date')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('expires_at')
                            ->label('End Date')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }
}