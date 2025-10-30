<?php

namespace App\Filament\Resources\PiutangJasa\Tables;

use App\Models\PiutangJasa;
use App\Models\PiutangJasaCicilanDetail;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\View;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;

class PiutangJasaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaksiJasa.kode_jasa')
                    ->label('Kode Jasa')
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
                    ->sortable()
                    ->state(fn ($record): int => max(($record->total_piutang ?? 0) - (int) $record->piutangJasaCicilanDetail()->sum('nominal_cicilan'), 0)),
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
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('bayarCicilan')
                    ->label('Bayar Cicilan')
                    ->color('primary')
                    ->icon('heroicon-o-banknotes')
                    ->hidden(
                        fn (PiutangJasa $record): bool => strtolower((string) $record->status_pembayaran) === 'sudah lunas' ||
                        max(((int) ($record->total_piutang ?? 0)) - (int) $record->piutangJasaCicilanDetail()->sum('nominal_cicilan'), 0) <= 0
                    )
                    ->modalHeading(fn ($record) => 'Cicilan: '.$record->transaksiJasa->kode_jasa)
                    ->modalSubmitActionLabel('Bayar')
                    ->modalWidth('2xl')
                    ->schema([
                        View::make('filament/components/cicilan-table')
                            ->viewData(fn (PiutangJasa $record) => [
                                'record' => $record,
                                'relationName' => 'piutangJasaCicilanDetail',
                                'totalFieldName' => 'total_piutang',
                                'sisaLabel' => 'Sisa Piutang',
                            ]),
                        TextInput::make('nominal_cicilan')
                            ->label('Nominal Cicilan')
                            ->numeric()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->minValue(1)
                            ->required()
                            ->helperText(fn (PiutangJasa $record): string => 'Sisa: Rp '.number_format(max(((int) ($record->total_piutang ?? 0)) - (int) $record->piutangJasaCicilanDetail()->sum('nominal_cicilan'), 0), 0, ',', '.')),
                        DatePicker::make('tanggal_cicilan')
                            ->label('Tanggal Cicilan')
                            ->required(),
                    ])
                    ->action(function (array $data, PiutangJasa $record): void {
                        $total = (int) ($record->total_piutang ?? 0);
                        $paid = (int) $record->piutangJasaCicilanDetail()->sum('nominal_cicilan');
                        $sisa = max($total - $paid, 0);

                        $nominal = (int) ($data['nominal_cicilan'] ?? 0);
                        if ($nominal < 1 || $nominal > $sisa) {
                            throw ValidationException::withMessages([
                                'nominal_cicilan' => 'Nominal melebihi sisa piutang (Rp '.number_format($sisa, 0, ',', '.').').',
                            ]);
                        }
                        PiutangJasaCicilanDetail::create([
                            'piutang_jasa_id' => $record->id,
                            'nominal_cicilan' => $nominal,
                            'tanggal_cicilan' => $data['tanggal_cicilan'],
                        ]);
                        // Recalculate status pembayaran
                        $paid2 = (int) $record->piutangJasaCicilanDetail()->sum('nominal_cicilan');
                        $sisa2 = max((int) ($record->total_piutang ?? 0) - $paid2, 0);
                        $status = 'belum lunas';
                        if ($sisa2 <= 0 && ($record->total_piutang ?? 0) > 0) {
                            $status = 'sudah lunas';
                        } elseif ($sisa2 < ($record->total_piutang ?? 0) && $sisa2 > 0) {
                            $status = 'tercicil';
                        }
                        $record->forceFill([
                            'status_pembayaran' => $status,
                        ])->save();
                        Notification::make()
                            ->title('Pembayaran cicilan berhasil')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
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
