<?php

namespace App\Filament\Resources\WarrantyClaims;

use App\Filament\Resources\WarrantyClaims\Pages\EditWarrantyClaim;
use App\Filament\Resources\WarrantyClaims\Pages\ListWarrantyClaims;
use App\Filament\Resources\WarrantyClaims\Pages\ViewWarrantyClaim;
use App\Filament\Resources\WarrantyClaims\Schemas\WarrantyClaimForm;
use App\Filament\Resources\WarrantyClaims\Schemas\WarrantyClaimInfolist;
use App\Filament\Resources\WarrantyClaims\Tables\WarrantyClaimsTable;
use App\Models\WarrantyClaim;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class WarrantyClaimResource extends Resource
{
    protected static ?string $model = WarrantyClaim::class;

    protected static ?string $recordTitleAttribute = 'claim_number';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'After Sales';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return WarrantyClaimForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WarrantyClaimInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WarrantyClaimsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWarrantyClaims::route('/'),
            'view' => ViewWarrantyClaim::route('/{record}'),
            'edit' => EditWarrantyClaim::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['warranty.productSerial', 'warranty.orderItem', 'warranty.order', 'warranty.user', 'handler']);
    }

    public static function getStatusOptions(): array
    {
        return WarrantyClaim::statusOptions();
    }

    public static function getStatusColor(?string $state): string
    {
        return WarrantyClaim::statusColor($state);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return true;
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
