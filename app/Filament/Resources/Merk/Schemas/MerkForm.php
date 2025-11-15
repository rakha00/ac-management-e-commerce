<?php

namespace App\Filament\Resources\Merk\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MerkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('merk')
                    ->label('Merk AC')
                    ->required(),
            ]);
    }
}
