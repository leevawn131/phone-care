<?php

namespace App\Filament\Resources\WarrantyClaims\Tables;

use App\Filament\Resources\WarrantyClaims\WarrantyClaimResource;
use App\Models\WarrantyClaim;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WarrantyClaimsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['warranty.orderItem', 'warranty.order', 'warranty.user', 'handler']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('claim_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('warranty.warranty_code')
                    ->label('Warranty Code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('warranty.orderItem.product_name')
                    ->label('Product')
                    ->searchable(),
                TextColumn::make('warranty.user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('warranty.order.recipient_phone')
                    ->label('Phone')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (?string $state): string => WarrantyClaimResource::getStatusColor($state)),
                TextColumn::make('handler.name')
                    ->label('Handled By')
                    ->placeholder('N/A'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d/m/Y H:i'),
                TextColumn::make('received_at')
                    ->label('Received')
                    ->dateTime('d/m/Y H:i'),
                TextColumn::make('resolved_at')
                    ->label('Resolved')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(WarrantyClaimResource::getStatusOptions())
                    ->native(false),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
