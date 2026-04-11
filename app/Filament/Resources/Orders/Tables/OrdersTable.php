<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['warranties.productSerial']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('recipient_name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('recipient_phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'paid' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                SelectColumn::make('status')
                    ->options(OrderResource::getStatusOptions())
                    ->selectablePlaceholder(false)
                    ->rules(['required']),
                TextColumn::make('grand_total')
                    ->label('Total')
                    ->state(fn (Order $record): string => OrderResource::formatMoney($record->grand_total)),
                TextColumn::make('serial_numbers')
                    ->label('Warranty Serials')
                    ->state(function (Order $record): string {
                        $serials = $record->warranties
                            ->pluck('productSerial.serial_number')
                            ->filter()
                            ->implode(', ');

                        return $serials ?: 'Not generated yet';
                    })
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(OrderResource::getStatusOptions()),
                SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()->label('Update Status'),
            ]);
    }
}