<?php

namespace App\Filament\Resources\TransaksiJasa\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransaksiJasaDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'detailTransaksiJasa';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Jasa')
                    ->description('Detail layanan jasa yang diberikan.')
                    ->schema([
                        TextInput::make('jenis_jasa')
                            ->label('Jenis Jasa')
                            ->required(),

                        TextInput::make('qty')
                            ->label('Qty')
                            ->numeric(),

                        TextInput::make('harga_jasa')
                            ->label('Harga Jasa')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->live(true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $subtotal = $get('qty') * (int) str_replace(',', '', $state);
                                $set('subtotal_pendapatan', number_format($subtotal));
                            }),
                        TextInput::make('subtotal_pendapatan')
                            ->label('Subtotal Pendapatan')
                            ->prefix('Rp')
                            ->disabled(),
                        Textarea::make('keterangan_jasa')
                            ->label('Keterangan Jasa'),
                    ])
                    ->columnSpanFull(),

                Section::make('Pengeluaran Jasa')
                    ->description('Detail biaya pengeluaran terkait jasa.')
                    ->schema([
                        TextInput::make('pengeluaran_jasa')
                            ->label('Pengeluaran Jasa')
                            ->numeric()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),

                        Textarea::make('keterangan_pengeluaran')
                            ->label('Keterangan Pengeluaran'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('jenis_jasa')
            ->columns([
                TextColumn::make('jenis_jasa')
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
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),

                TextColumn::make('pengeluaran_jasa')
                    ->label('Pengeluaran')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable()
                    ->summarize(Sum::make()
                        ->label('Total Pengeluaran')
                        ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')),

                TextColumn::make('subtotal_pendapatan')
                    ->label('Subtotal Pendapatan')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable()
                    ->summarize(Sum::make()
                        ->label('Total Pendapatan')
                        ->using(fn (Builder $query) => $query->sum('subtotal_pendapatan'))
                        ->numeric()
                        ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')),

                TextColumn::make('subtotal_keuntungan')
                    ->label('Subtotal Keuntungan')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')

                    ->sortable()
                    ->summarize(Sum::make()
                        ->label('Total Keuntungan')
                        ->using(fn (Builder $query) => $query->sum('subtotal_keuntungan'))
                        ->numeric()
                        ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')),
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
