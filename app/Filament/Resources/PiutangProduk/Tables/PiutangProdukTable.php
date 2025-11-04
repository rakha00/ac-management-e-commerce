<?php

namespace App\Filament\Resources\PiutangProduk\Tables;

use App\Models\PiutangProduk;
use App\Models\PiutangProdukCicilanDetail;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\View;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class PiutangProdukTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaksiProduk.nomor_invoice')
                    ->label('No Invoice')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_piutang')
                    ->label('Total Piutang')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),
                TextColumn::make('sisa_piutang')
                    ->label('Sisa Piutang')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),
                TextColumn::make('status_pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'belum lunas' => 'danger',
                        'tercicil' => 'warning',
                        'sudah lunas' => 'success',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jatuh_tempo')
                    ->date()
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updatedBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deletedBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->deferFilters(false)
            ->deferColumnManager(false)
            ->filters([
                TrashedFilter::make(),
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('dari')
                            ->maxDate(fn (callable $get) => $get('sampai') ?? null),
                        DatePicker::make('sampai')
                            ->minDate(fn (callable $get) => $get('dari')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['dari'] ?? null, fn (Builder $q, $date) => $q->whereDate('jatuh_tempo', '>=', $date))
                            ->when($data['sampai'] ?? null, fn (Builder $q, $date) => $q->whereDate('jatuh_tempo', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari'] ?? null) {
                            $indicators['dari'] = 'Dari '.Carbon::parse($data['dari'])->toFormattedDateString();
                        }
                        if ($data['sampai'] ?? null) {
                            $indicators['sampai'] = 'Sampai '.Carbon::parse($data['sampai'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
                SelectFilter::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->options([
                        'belum lunas' => 'Belum Lunas',
                        'tercicil' => 'Tercicil',
                        'sudah lunas' => 'Sudah Lunas',
                    ])
                    ->multiple()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('bayarCicilan')
                    ->label('Bayar Cicilan')
                    ->color('primary')
                    ->icon('heroicon-o-banknotes')
                    ->hidden(
                        fn (PiutangProduk $record): bool => $record->status_pembayaran === 'sudah lunas' || (int) ($record->sisa_piutang <= 0)
                    )
                    ->modalHeading(fn ($record) => 'Cicilan: '.$record->transaksiProduk->nomor_invoice)
                    ->modalSubmitActionLabel('Bayar')
                    ->modalWidth('2xl')
                    ->schema([
                        View::make('filament/components/cicilan-table')
                            ->viewData(fn (PiutangProduk $record) => [
                                'record' => $record,
                                'relationName' => 'piutangProdukCicilanDetail',
                                'totalFieldName' => 'total_piutang',
                                'sisaLabel' => 'Sisa Piutang',
                            ]),
                        TextInput::make('nominal_cicilan')
                            ->label('Nominal Cicilan')
                            ->numeric()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->maxValue(fn ($record) => $record->sisa_piutang)
                            ->helperText(fn ($record) => 'Sisa piutang: Rp '.number_format($record->sisa_piutang)),
                        DatePicker::make('tanggal_cicilan')
                            ->label('Tanggal Cicilan')
                            ->required(),
                    ])
                    ->action(function (array $data, PiutangProduk $record): void {
                        $nominal = (int) ($data['nominal_cicilan'] ?? 0);
                        PiutangProdukCicilanDetail::create([
                            'piutang_produk_id' => $record->id,
                            'nominal_cicilan' => $nominal,
                            'tanggal_cicilan' => $data['tanggal_cicilan'],
                        ]);
                        Notification::make()
                            ->title('Pembayaran cicilan berhasil')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
