<?php

namespace App\Filament\Resources\HutangProduks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HutangProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('no_unit_masuk')
                    ->required(),
                TextInput::make('total_hutang')
                    ->required()
                    ->numeric(),
                TextInput::make('nama_principle')
                    ->required(),
                Select::make('status_pembayaran')
                    ->options([
                        'belum lunas' => 'Belum Lunas',
                        'tercicil' => 'Tercicil',
                        'sudah lunas' => 'Sudah Lunas',
                    ])
                    ->native(false)
                    ->default('belum lunas')
                    ->required(),
                DatePicker::make('jatuh_tempo')
                    ->required(),
                Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }
}
