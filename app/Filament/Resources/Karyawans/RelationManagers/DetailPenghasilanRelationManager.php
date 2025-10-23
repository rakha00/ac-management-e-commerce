<?php

namespace App\Filament\Resources\Karyawans\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction as ExcelExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class DetailPenghasilanRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPenghasilan';

    protected static ?string $recordTitleAttribute = 'tanggal';

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
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('tanggal', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('tanggal', '<=', $date)
                            );
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
