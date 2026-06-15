<?php

namespace App\Filament\Resources\Warranties;

use App\Filament\Resources\Warranties\Pages\ListWarranties;
use App\Filament\Resources\Warranties\Pages\ViewWarranty;
use App\Filament\Resources\Warranties\Schemas\WarrantyForm;
use App\Filament\Resources\Warranties\Schemas\WarrantyInfolist;
use App\Filament\Resources\Warranties\Tables\WarrantiesTable;
use App\Models\Warranty;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class WarrantyResource extends Resource
{
    protected static ?string $model = Warranty::class;

    protected static ?string $recordTitleAttribute = 'warranty_code';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'After Sales';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return WarrantyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WarrantyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WarrantiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWarranties::route('/'),
            'view' => ViewWarranty::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['productSerial', 'order', 'orderItem', 'user']);
    }

    public static function getStatusColor(?string $state): string
    {
        return match ($state) {
            'active' => 'success',
            'expired' => 'danger',
            'pending', 'claimed' => 'warning',
            'approved', 'received', 'in_progress' => 'info',
            'rejected' => 'danger',
            'completed' => 'success',
            default => 'gray',
        };
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}