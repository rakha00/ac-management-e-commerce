<?php

namespace App\Filament\Resources\MerkSpareparts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MerkSparepartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('merk_spareparts')
                    ->label('Merk Spareparts')
                    ->required(),
            ]);
    }
}
