<?php

namespace App\Filament\Resources\TransaksiProduk\RelationManagers;

use App\Models\UnitAC;
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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransaksiProdukDetailRelationManager extends RelationManager
{
    // Relationship name must match the Eloquent relation on parent model
    protected static string $relationship = 'transaksiProdukDetail';

    protected static ?string $recordTitleAttribute = 'sku';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Pilihan Unit AC')
                    ->description('Pilih unit AC untuk mengisi data otomatis')
                    ->components([
                        Select::make('unit_ac_id')
                            ->label('SKU')
                            ->options(fn () => UnitAC::query()
                                ->orderBy('sku')
                                ->pluck('sku', 'id')
                                ->toArray())
                            ->searchable()
                            ->reactive()
                            ->disabled(fn (?Model $record) => $record !== null)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $unit = UnitAC::find($state);
                                if ($unit) {
                                    $set('nama_unit', $unit->nama_unit);
                                    $set('harga_dealer', number_format($unit->harga_dealer));
                                    $set('harga_ecommerce', number_format($unit->harga_ecommerce));
                                    $set('harga_retail', number_format($unit->harga_retail));
                                }
                            }),
                        TextInput::make('sku')
                            ->hidden()
                            ->disabled(),
                        TextInput::make('nama_unit')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columnSpanFull(),
                Section::make('Detail Transaksi')
                    ->description('Isi jumlah dan harga transaksi')
                    ->components([
                        TextInput::make('jumlah_keluar')
                            ->numeric()
                            ->required()
                            ->hint(function (callable $get) {
                                $unitId = $get('unit_ac_id');
                                if ($unitId) {
                                    $unit = UnitAC::find($unitId);

                                    return 'Sisa unit saat ini: '.($unit ? $unit->stok_akhir : 'N/A');
                                }

                                return 'Sisa unit saat ini: ';
                            }),
                        TextInput::make('harga_modal')
                            ->numeric()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->required()
                            ->placeholder(0),
                        TextInput::make('harga_jual')
                            ->numeric()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->required()
                            ->placeholder(0),
                    ]),
                Section::make('Harga Referensi')
                    ->description('Data harga referensi dari sistem')
                    ->components([
                        TextInput::make('harga_dealer')
                            ->prefix('Rp')
                            ->disabled()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->dehydrated()
                            ->belowContent(fn (?Model $record) => $record ? 'Harga terakhir disimpan: Rp'.number_format($record->getOriginal('harga_dealer')) : null),
                        TextInput::make('harga_ecommerce')
                            ->prefix('Rp')
                            ->disabled()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->dehydrated()
                            ->belowContent(fn (?Model $record) => $record ? 'Harga terakhir disimpan: Rp'.number_format($record->getOriginal('harga_ecommerce')) : null),
                        TextInput::make('harga_retail')
                            ->prefix('Rp')
                            ->disabled()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->dehydrated()
                            ->belowContent(fn (?Model $record) => $record ? 'Harga terakhir disimpan: Rp'.number_format($record->getOriginal('harga_retail')) : null),
                    ]),
                Section::make('Informasi Tambahan')
                    ->components([
                        Textarea::make('keterangan')
                            ->nullable(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([SoftDeletingScope::class]))
            ->recordTitle(fn ($record): string => "{$record->sku} ({$record->jumlah_keluar})")
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nama_unit')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jumlah_keluar')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('harga_modal')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),
                TextColumn::make('harga_jual')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),
                TextColumn::make('subtotal_modal')
                    ->label('Subtotal Modal')
                    ->state(fn ($record) => (float) $record->harga_modal * (int) $record->jumlah_keluar)
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable()
                    ->summarize(Summarizer::make()->using(fn ($query) => $query->get()->sum(fn ($record) => (float) $record->harga_modal * (int) $record->jumlah_keluar))->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')),
                TextColumn::make('subtotal_penjualan')
                    ->label('Subtotal Penjualan')
                    ->state(fn ($record) => (float) $record->harga_jual * (int) $record->jumlah_keluar)
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable()
                    ->summarize(Summarizer::make()->using(fn ($query) => $query->get()->sum(fn ($record) => (float) $record->harga_jual * (int) $record->jumlah_keluar))->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')),
                TextColumn::make('subtotal_keuntungan')
                    ->label('Subtotal Keuntungan')
                    ->state(fn ($record) => max(((float) $record->harga_jual - (float) $record->harga_modal) * (int) $record->jumlah_keluar, 0))
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable()
                    ->summarize(Summarizer::make()->using(fn ($query) => $query->get()->sum(fn ($record) => max(((float) $record->harga_jual - (float) $record->harga_modal) * (int) $record->jumlah_keluar, 0)))->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
