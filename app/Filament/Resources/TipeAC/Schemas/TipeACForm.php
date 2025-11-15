<?php

namespace App\Filament\Resources\TipeAC\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TipeACForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tipe_ac')
                    ->label('Tipe AC')
                    ->required(),
            ]);
    }
}
