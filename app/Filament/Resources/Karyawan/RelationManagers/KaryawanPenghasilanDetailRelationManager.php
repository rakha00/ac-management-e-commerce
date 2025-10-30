<?php

namespace App\Filament\Resources\Karyawan\RelationManagers;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction as ExcelExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class KaryawanPenghasilanDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'karyawanPenghasilanDetail';

    protected static ?string $pluralLabel = 'detail penghasilan karyawan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kasbon')
                    ->numeric()
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->default(0),
                TextInput::make('lembur')
                    ->numeric()
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->default(0),
                TextInput::make('bonus')
                    ->numeric()
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->default(0),
                TextInput::make('potongan')
                    ->numeric()
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->default(0),
                Textarea::make('keterangan')
                    ->columnSpanFull(),
                DatePicker::make('tanggal')
                    ->required(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('kasbon')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable()
                    ->summarize(Sum::make()
                        ->label('Total Kasbon')
                        ->numeric()
                        ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')),
                TextColumn::make('lembur')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable()
                    ->summarize(Sum::make()
                        ->label('Total Lembur')
                        ->numeric()
                        ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')),
                TextColumn::make('bonus')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable()
                    ->summarize(Sum::make()
                        ->label('Total Bonus')
                        ->numeric()
                        ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')),
                TextColumn::make('potongan')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable()
                    ->summarize(Sum::make()
                        ->label('Total Potongan')
                        ->numeric()
                        ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')),
                TextColumn::make('keterangan')
                    ->limit(30)
                    ->wrap(),
            ])
            ->filters([
                Filter::make('tanggal')
                    ->schema([
                        Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ]),
                        Select::make('tahun')
                            ->label('Tahun')
                            ->options(function (): array {
                                $currentYear = now()->year;
                                $years = range($currentYear - 5, $currentYear + 1);

                                return array_combine($years, array_map('strval', $years));
                            }),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $bulan = $data['bulan'] ?? null;
                        $tahun = $data['tahun'] ?? null;

                        if ($bulan && $tahun) {
                            $start = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->toDateString();
                            $end = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->toDateString();

                            return $query
                                ->whereDate('tanggal', '>=', $start)
                                ->whereDate('tanggal', '<=', $end);
                        }

                        return $query;
                    })
                    ->indicateUsing(function (array $data): array {
                        $bulan = $data['bulan'] ?? null;
                        $tahun = $data['tahun'] ?? null;

                        $indicators = [];

                        if ($bulan && $tahun) {
                            $monthNames = [
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ];

                            $label = 'Periode: '.($monthNames[$bulan] ?? $bulan).' '.$tahun;

                            $indicators[] = Indicator::make($label)
                                ->removeField('bulan')
                                ->removeField('tahun');
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->toolbarActions([
                ExcelExportAction::make('export_excel')
                    ->label('Export Excel')
                    ->exports([
                        ExcelExport::make('table')
                            ->withColumns([
                                Column::make('tanggal')
                                    ->formatStateUsing(fn (string $state): string => Carbon::parse($state)->format('d-m-Y')),
                                Column::make('kasbon'),
                                Column::make('lembur'),
                                Column::make('bonus'),
                                Column::make('potongan'),
                                Column::make('keterangan'),
                            ])
                            ->withFilename(fn () => 'detail_penghasilan_'.$this->ownerRecord->nama.'_'.now()->format('Ymd_His'))
                            ->fromTable(),
                    ]),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
