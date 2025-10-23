<?php

namespace App\Filament\Resources\PiutangProduks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PiutangProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('no_invoice')
                    ->required(),
                TextInput::make('total_piutang')
                    ->required()
                    ->numeric(),
                TextInput::make('sisa_piutang')
                    ->required()
                    ->numeric(),
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
