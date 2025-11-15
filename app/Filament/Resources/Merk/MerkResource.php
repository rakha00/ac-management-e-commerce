<?php

namespace App\Filament\Resources\Merk;

use App\Filament\Resources\Merk\Pages\ListMerk;
use App\Filament\Resources\Merk\Schemas\MerkForm;
use App\Filament\Resources\Merk\Tables\MerkTable;
use App\Models\Merk;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MerkResource extends Resource
{
    protected static ?string $model = Merk::class;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static ?string $pluralModelLabel = 'merk AC';

    protected static ?string $modelLabel = 'merk AC';

    protected static ?string $slug = 'merk-ac';

    protected static ?string $recordTitleAttribute = 'merk';

    public static function form(Schema $schema): Schema
    {
        return MerkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MerkTable::configure($table);
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
            'index' => ListMerk::route('/'),
        ];
    }
}
