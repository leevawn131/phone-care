<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Status Management')
                    ->schema([
                        Placeholder::make('order_number')
                            ->label('Order Number')
                            ->content(fn (?Order $record): string => $record?->order_number ?? 'Will be generated automatically'),
                        Select::make('status')
                            ->options(OrderResource::getStatusOptions())
                            ->native(false)
                            ->required()
                            ->helperText('Changing the status to completed will save the order normally, so the OrderObserver can generate warranty serials.'),
                        Placeholder::make('customer')
                            ->content(fn (?Order $record): string => $record ? sprintf('%s (%s)', $record->recipient_name, $record->recipient_phone) : 'N/A'),
                        Placeholder::make('payment_status')
                            ->label('Payment Status')
                            ->content(fn (?Order $record): string => $record?->payment_status ? ucfirst($record->payment_status) : 'N/A'),
                        Placeholder::make('grand_total')
                            ->label('Grand Total')
                            ->content(fn (?Order $record): string => $record ? OrderResource::formatMoney($record->grand_total) : OrderResource::formatMoney(0)),
                        Placeholder::make('placed_at')
                            ->label('Placed At')
                            ->content(fn (?Order $record): string => $record?->placed_at?->format('d/m/Y H:i') ?? 'N/A'),
                        Placeholder::make('generated_serials')
                            ->label('Generated Warranty Serials')
                            ->content(function (?Order $record): string {
                                if (! $record) {
                                    return 'No serials generated yet.';
                                }

                                $serials = $record->loadMissing('warranties.productSerial')
                                    ->warranties
                                    ->pluck('productSerial.serial_number')
                                    ->filter()
                                    ->implode(', ');

                                return $serials ?: 'No serials generated yet.';
                            })
                            ->columnSpanFull(),
                        Placeholder::make('order_items')
                            ->label('Order Items')
                            ->content(function (?Order $record): string {
                                if (! $record) {
                                    return 'No items.';
                                }

                                $items = $record->loadMissing('items')
                                    ->items
                                    ->map(fn ($item): string => sprintf('%s x%d', $item->product_name, $item->qty))
                                    ->implode(', ');

                                return $items ?: 'No items.';
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}