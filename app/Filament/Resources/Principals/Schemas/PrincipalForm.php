<?php

namespace App\Filament\Resources\Principals\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PrincipalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Principal')
                    ->columns(1)
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('sales')
                            ->label('Sales Principal/Subdealer')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('nomor_hp')
                            ->label('No. HP')
                            ->required()
                            ->maxLength(20),
                    ]),
                Section::make('Keterangan')
                    ->schema([
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
