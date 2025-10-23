<?php

namespace App\Filament\Resources\Karyawans\Schemas;

use App\Filament\Resources\Karyawans\Pages\CreateKaryawan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
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
                            ->label('Email')
                            ->email()
                            ->rules([
                                fn ($livewire) => $livewire instanceof CreateKaryawan
                                ? Rule::unique('users', 'email')
                                : Rule::unique('users', 'email')->ignore(data_get($livewire, 'record.user.id')),
                            ])
                            ->default(fn ($livewire) => old('email', data_get($livewire, 'record.user.email')))
                            ->dehydrated(true)
                            ->required(fn ($livewire) => $livewire instanceof CreateKaryawan),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->minLength(6)
                            ->rules(['nullable'])
                            ->default(fn () => old('password'))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn ($livewire) => $livewire instanceof CreateKaryawan),
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
                        TextInput::make('no_hp')
                            ->required(),
                        TextInput::make('gaji_pokok')
                            ->required()
                            ->numeric(),
                        Textarea::make('alamat')
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('foto_ktp')
                            ->image()
                            ->disk('public')
                            ->directory('karyawan/ktp')
                            ->downloadable()
                            ->openable()
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('dokumen_tambahan')
                            ->disk('public')
                            ->directory('karyawan/dokumen')
                            ->downloadable()
                            ->openable()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('kontak_darurat_serumah')
                            ->required(),
                        TextInput::make('kontak_darurat_tidak_serumah')
                            ->required(),
                        Toggle::make('status_aktif')
                            ->default(true)
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?bool $state): void {
                                $set('tanggal_terakhir_aktif', $state ? null : now());
                            })
                            ->required(),
                        DatePicker::make('tanggal_terakhir_aktif')
                            ->readOnly()
                            ->helperText('Diisi otomatis ketika status diubah menjadi non-aktif'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
