<?php

namespace App\Filament\Resources\SparepartKeluar\RelationManagers;

use App\Models\Sparepart;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SparepartKeluarDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'detailSparepartKeluar';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pilihan Sparepart')
                    ->description('Pilih sparepart untuk mengisi data otomatis')
                    ->components([
                        Select::make('sparepart_id')
                            ->label('Kode Sparepart')
                            ->options(fn () => Sparepart::query()
                                ->orderBy('kode_sparepart')
                                ->pluck('kode_sparepart', 'id')
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->required()
                            ->afterStateUpdated(function ($state, Set $set) {
                                $sp = Sparepart::find($state);
                                if ($sp) {
                                    $set('nama_sparepart', $sp->nama_sparepart);
                                    $set('referensi_harga_modal', number_format($sp->harga_modal, 0, ',', ','));
                                } else {
                                    $set('nama_sparepart', null);
                                    $set('referensi_harga_modal', null);
                                }
                            }),

                        TextInput::make('nama_sparepart')
                            ->label('Nama Sparepart')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('referensi_harga_modal')
                            ->label('Harga Modal')
                            ->disabled()
                            ->prefix('Rp')
                            ->formatStateUsing(function ($state, Get $get) {
                                $sparepart_id = $get('sparepart_id');
                                $sp = Sparepart::find($sparepart_id);

                                return $sp ? number_format($sp->harga_modal, 0, ',', ',') : null;
                            })
                            ->hint('Referensi dari sistem')
                            ->dehydrated(false),
                    ])
                    ->columns(1),

                Section::make('Detail Transaksi')
                    ->description('Isi jumlah dan harga transaksi')
                    ->components([
                        TextInput::make('jumlah_keluar')
                            ->label('Jumlah Keluar')
                            ->numeric()
                            ->minValue(1)
                            ->hint(function (Get $get) {
                                $sparepart_id = $get('sparepart_id');
                                $sp = Sparepart::find($sparepart_id);

                                return $sp ? 'Stok tersedia: '.$sp->stok_akhir : null;
                            })
                            ->required(),

                        TextInput::make('harga_modal')
                            ->label('Harga Modal')
                            ->numeric()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->minValue(0)
                            ->required(),

                        TextInput::make('harga_jual')
                            ->label('Harga Jual')
                            ->numeric()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->minValue(0)
                            ->required(),
                    ])
                    ->columns(1),

                Section::make('Informasi Tambahan')
                    ->components([
                        TextInput::make('keterangan')
                            ->label('Keterangan')
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_sparepart')
            ->columns([
                TextColumn::make('sparepart.kode_sparepart')
                    ->label('Kode Sparepart')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sparepart.nama_sparepart')
                    ->label('Nama Sparepart')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jumlah_keluar')
                    ->label('Jumlah Keluar')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('harga_modal')
                    ->label('Harga Modal')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable(),
                TextColumn::make('harga_jual')
                    ->label('Harga Jual')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
