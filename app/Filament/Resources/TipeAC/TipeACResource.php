<?php

namespace App\Filament\Resources\TipeAC;

use App\Filament\Resources\TipeAC\Pages\ListTipeAC;
use App\Filament\Resources\TipeAC\Schemas\TipeACForm;
use App\Filament\Resources\TipeAC\Tables\TipeACTable;
use App\Models\TipeAC;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TipeACResource extends Resource
{
    protected static ?string $model = TipeAC::class;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static ?string $pluralModelLabel = 'tipe AC';

    protected static ?string $modelLabel = 'tipe AC';

    protected static ?string $slug = 'tipe-ac';

    protected static ?string $recordTitleAttribute = 'tipe_ac';

    public static function form(Schema $schema): Schema
    {
        return TipeACForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TipeACTable::configure($table);
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
            'index' => ListTipeAC::route('/'),
        ];
    }
}
