<?php

namespace App\Filament\Resources\Warranties\Tables;

use App\Filament\Resources\Warranties\WarrantyResource;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WarrantiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['productSerial', 'order', 'orderItem']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('warranty_code')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('productSerial.serial_number')
                    ->label('Serial Number')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('order.order_number')
                    ->label('Order')
                    ->searchable(),
                TextColumn::make('orderItem.product_name')
                    ->label('Product')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('order.recipient_phone')
                    ->label('Customer Phone')
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (?string $state): string => WarrantyResource::getStatusColor($state)),
                TextColumn::make('activated_at')
                    ->label('Activated At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Expires At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'claimed' => 'Claimed',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}