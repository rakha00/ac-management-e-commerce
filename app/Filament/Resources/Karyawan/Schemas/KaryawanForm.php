<?php

namespace App\Filament\Resources\Karyawan\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Validation\Rule;

class KaryawanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun')
                    ->relationship('user')
                    ->schema([
                        TextInput::make('email')
                            ->email()
                            ->rules([
                                function ($livewire) {
                                    $userId = data_get($livewire, 'record.user.id');

                                    return Rule::unique('users', 'email')->ignore($userId);
                                },
                            ])
                            ->required(),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->minLength(6)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create'),
                    ])
                    ->columnSpanFull(),
                Section::make('Data Karyawan')
                    ->schema([
                        TextInput::make('nama')
                            ->required(),
                        Select::make('jabatan')
                            ->options([
                                'admin' => 'Admin',
                                'gudang' => 'Gudang',
                                'helper' => 'Helper',
                                'teknisi' => 'Teknisi',
                                'staff' => 'Staff',
                                'sales' => 'Sales',
                            ])
                            ->required(),
                        TextInput::make('nomor_hp')
                            ->label('No. HP'),
                        TextInput::make('gaji_pokok')
                            ->numeric()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),
                        Textarea::make('alamat')
                            ->columnSpanFull(),
                        FileUpload::make('path_foto_ktp')
                            ->label('Foto KTP')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                            ->directory('karyawan/ktp')
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),
                        FileUpload::make('path_dokumen_tambahan')
                            ->label('Dokumen Tambahan')
                            ->multiple()
                            ->directory('karyawan/dokumen')
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),
                        TextInput::make('kontak_darurat_serumah')
                            ->label('Kontak Darurat (Serumah)'),
                        TextInput::make('kontak_darurat_tidak_serumah')
                            ->label('Kontak Darurat (Tidak Serumah)'),
                        Toggle::make('status_aktif')
                            ->default(true)
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?bool $state): void {
                                $set('tanggal_terakhir_aktif', $state ? null : now());
                            }),
                        DatePicker::make('tanggal_terakhir_aktif')
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Diisi otomatis ketika status diubah menjadi non-aktif'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
