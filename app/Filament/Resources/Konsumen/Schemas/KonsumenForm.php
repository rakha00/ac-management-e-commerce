<?php

namespace App\Filament\Resources\Konsumen\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KonsumenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Konsumen')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Konsumen')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('telepon')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(255)
                            ->nullable(),
                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->maxLength(255)
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
