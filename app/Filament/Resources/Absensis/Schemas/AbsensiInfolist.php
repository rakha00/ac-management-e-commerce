<?php

namespace App\Filament\Resources\Absensis\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AbsensiInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('karyawan.id')
                    ->label('Karyawan'),
                TextEntry::make('tanggal')
                    ->date(),
                TextEntry::make('waktu_absen')
                    ->dateTime(),
                IconEntry::make('telat')
                    ->boolean(),
                TextEntry::make('keterangan')
                    ->placeholder('-'),
                IconEntry::make('terkonfirmasi')
                    ->boolean(),
                TextEntry::make('dikonfirmasi_oleh')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('waktu_konfirmasi')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
