<?php

namespace App\Filament\Resources\Karyawans\RelationManagers;

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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction as ExcelExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class DetailPenghasilanKaryawanRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPenghasilanKaryawan';

    protected static ?string $pluralLabel = 'detail penghasilan karyawan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kasbon')
                    ->numeric()
                    ->required()
                    ->default(0),
                TextInput::make('lembur')
                    ->numeric()
                    ->required()
                    ->default(0),
                TextInput::make('bonus')
                    ->numeric()
                    ->required()
                    ->default(0),
                TextInput::make('potongan')
                    ->numeric()
                    ->required()
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
                    ->sortable(),
                TextColumn::make('lembur')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bonus')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('potongan')
                    ->numeric()
                    ->sortable(),
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
                            ])
                            ->default(now()->month),
                        Select::make('tahun')
                            ->label('Tahun')
                            ->options(function (): array {
                                $currentYear = now()->year;
                                $years = range($currentYear - 5, $currentYear + 1);

                                return array_combine($years, array_map('strval', $years));
                            })
                            ->default(now()->year),
                        DatePicker::make('dari')->label('Dari'),
                        DatePicker::make('sampai')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $bulan = $data['bulan'] ?? null;
                        $tahun = $data['tahun'] ?? null;
                        $dari = $data['dari'] ?? null;
                        $sampai = $data['sampai'] ?? null;

                        // Jika filter bulan/tahun aktif, non-aktifkan (abaikan) filter tanggal dari-sampai
                        if ($bulan && $tahun) {
                            $start = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->toDateString();
                            $end = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->toDateString();

                            return $query
                                ->whereDate('tanggal', '>=', $start)
                                ->whereDate('tanggal', '<=', $end);
                        }

                        // Jika bulan/tahun tidak aktif, gunakan filter dari-sampai (opsional)
                        return $query
                            ->when(
                                $dari,
                                fn (Builder $q, $date): Builder => $q->whereDate('tanggal', '>=', $date)
                            )
                            ->when(
                                $sampai,
                                fn (Builder $q, $date): Builder => $q->whereDate('tanggal', '<=', $date)
                            );
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

                            // Saat indikator periode dihapus, bersihkan bulan & tahun.
                            $indicators[] = Indicator::make($label)
                                ->removeField('bulan')
                                ->removeField('tahun');

                            return $indicators;
                        }

                        if ($data['dari'] ?? null) {
                            $indicators[] = Indicator::make('Dari: '.Carbon::parse($data['dari'])->toFormattedDateString())
                                ->removeField('dari');
                        }

                        if ($data['sampai'] ?? null) {
                            $indicators[] = Indicator::make('Sampai: '.Carbon::parse($data['sampai'])->toFormattedDateString())
                                ->removeField('sampai');
                        }

                        return $indicators;
                    })
                    ->default([
                        'bulan' => now()->month,
                        'tahun' => now()->year,
                    ]),
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
                                Column::make('tanggal'),
                                Column::make('kasbon'),
                                Column::make('lembur'),
                                Column::make('bonus'),
                                Column::make('potongan'),
                                Column::make('keterangan'),
                            ])
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                            ->withFilename(fn ($resource) => 'detail_penghasilan_'.now()->format('Ymd_His'))
                            ->askForFilename()
                            ->askForWriterType(),
                    ]),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
