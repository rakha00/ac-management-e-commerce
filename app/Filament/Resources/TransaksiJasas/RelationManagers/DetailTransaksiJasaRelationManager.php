<?php

namespace App\Filament\Resources\TransaksiJasas\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailTransaksiJasaRelationManager extends RelationManager
{
    protected static string $relationship = 'detailTransaksiJasa';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Jenis data jasa
                TextInput::make('jenis_data')
                    ->label('Jenis Data')
                    ->required()
                    ->maxLength(255),

                // Qty layanan
                TextInput::make('qty')
                    ->label('Qty')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                // Harga jasa (Rp)
                TextInput::make('harga_jasa')
                    ->label('Harga Jasa (Rp)')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                // Keterangan jasa (opsional)
                Textarea::make('keterangan_jasa')
                    ->label('Keterangan Jasa')
                    ->nullable(),

                // Pengeluaran jasa (Rp) dan keterangannya
                TextInput::make('pengeluaran_jasa')
                    ->label('Pengeluaran Jasa (Rp)')
                    ->numeric()
                    ->minValue(0)
                    ->nullable(),

                Textarea::make('keterangan_pengeluaran')
                    ->label('Keterangan Pengeluaran')
                    ->nullable(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('jenis_data')
            ->columns([
                TextColumn::make('jenis_data')
                    ->label('Jenis')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('qty')
                    ->label('Qty')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('harga_jasa')
                    ->label('Harga Jasa')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('pengeluaran_jasa')
                    ->label('Pengeluaran')
                    ->numeric()
                    ->sortable(),

                // Subtotal pendapatan: qty * harga_jasa
                TextColumn::make('subtotal_pendapatan')
                    ->label('Subtotal Pendapatan')
                    ->state(fn($record) => (int) ($record->qty ?? 0) * (float) ($record->harga_jasa ?? 0))
                    ->numeric()
                    ->sortable(),

                // Subtotal keuntungan: (qty * harga_jasa) - pengeluaran_jasa
                TextColumn::make('subtotal_keuntungan')
                    ->label('Subtotal Keuntungan')
                    ->state(fn($record) => ((int) ($record->qty ?? 0) * (float) ($record->harga_jasa ?? 0)) - (float) ($record->pengeluaran_jasa ?? 0))
                    ->numeric()
                    ->sortable(),
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
            ->modifyQueryUsing(fn(Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
