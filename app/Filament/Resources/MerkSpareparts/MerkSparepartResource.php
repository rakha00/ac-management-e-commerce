<?php

namespace App\Filament\Resources\MerkSpareparts;

use App\Filament\Resources\MerkSpareparts\Pages\ListMerkSpareparts;
use App\Filament\Resources\MerkSpareparts\Schemas\MerkSparepartForm;
use App\Filament\Resources\MerkSpareparts\Tables\MerkSparepartsTable;
use App\Models\MerkSparepart;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MerkSparepartResource extends Resource
{
    protected static ?string $model = MerkSparepart::class;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static ?string $recordTitleAttribute = 'merk_spareparts';

    public static function form(Schema $schema): Schema
    {
        return MerkSparepartForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MerkSparepartsTable::configure($table);
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
            'index' => ListMerkSpareparts::route('/'),
        ];
    }
}
