<?php

namespace App\Filament\Resources\HutangProduks\Tables;

use App\Models\DetailHutangProdukCicilan;
use App\Models\HutangProduk;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class HutangProduksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_unit_masuk')
                    ->searchable(),
                TextColumn::make('total_hutang')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sisa_hutang')
                    ->numeric()
                    ->sortable()
                    ->state(fn ($record): int => max(($record->total_hutang ?? 0) - (int) $record->detailHutangProdukCicilan()->sum('nominal_cicilan'), 0)),
                TextColumn::make('nama_principle')
                    ->searchable(),
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
                // Filter rentang tanggal untuk jatuh tempo
                Filter::make('jatuh_tempo')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('jatuh_tempo', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('jatuh_tempo', '<=', $date));
                    }),
                // Filter status pembayaran
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
                        fn (HutangProduk $record): bool => strtolower((string) $record->status_pembayaran) === 'sudah lunas' ||
                        max(((int) ($record->total_hutang ?? 0)) - (int) $record->detailHutangProdukCicilan()->sum('nominal_cicilan'), 0) <= 0
                    )
                    ->modalHeading(fn ($record) => 'Cicilan: '.$record->no_unit_masuk)
                    ->modalSubmitActionLabel('Bayar')
                    ->modalWidth('2xl')
                    ->schema([
                        View::make('filament/hutang-produk/cicilan-table')
                            ->viewData(fn (HutangProduk $record) => ['record' => $record]),
                        TextInput::make('nominal_cicilan')
                            ->label('Nominal Cicilan')
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(1)
                            ->required()
                            ->helperText(fn (HutangProduk $record): string => 'Sisa: Rp '.number_format(max(((int) ($record->total_hutang ?? 0)) - (int) $record->detailHutangProdukCicilan()->sum('nominal_cicilan'), 0), 0, ',', '.')),
                        DatePicker::make('tanggal_cicilan')
                            ->label('Tanggal Cicilan')
                            ->required(),
                    ])
                    ->action(function (array $data, HutangProduk $record): void {
                        $total = (int) ($record->total_hutang ?? 0);
                        $paid = (int) $record->detailHutangProdukCicilan()->sum('nominal_cicilan');
                        $sisa = max($total - $paid, 0);

                        $nominal = (int) ($data['nominal_cicilan'] ?? 0);
                        if ($nominal < 1 || $nominal > $sisa) {
                            throw ValidationException::withMessages([
                                'nominal_cicilan' => 'Nominal melebihi sisa hutang (Rp '.number_format($sisa, 0, ',', '.').').',
                            ]);
                        }

                        DetailHutangProdukCicilan::create([
                            'hutang_produk_id' => $record->id,
                            'nominal_cicilan' => $nominal,
                            'tanggal_cicilan' => $data['tanggal_cicilan'],
                        ]);

                        // Recalculate status pembayaran
                        $paid2 = (int) $record->detailHutangProdukCicilan()->sum('nominal_cicilan');
                        $sisa2 = max((int) ($record->total_hutang ?? 0) - $paid2, 0);
                        $status = 'belum lunas';
                        if ($sisa2 <= 0 && ($record->total_hutang ?? 0) > 0) {
                            $status = 'sudah lunas';
                        } elseif ($sisa2 < ($record->total_hutang ?? 0) && $sisa2 > 0) {
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
                ]),
            ]);
    }
}
