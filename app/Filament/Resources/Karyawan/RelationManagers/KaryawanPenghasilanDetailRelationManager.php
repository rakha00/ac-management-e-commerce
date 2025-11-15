<?php

namespace App\Filament\Resources\Karyawan\RelationManagers;

use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
                    ->stripCharacters(','),
                TextInput::make('lembur')
                    ->numeric()
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(','),
                TextInput::make('bonus')
                    ->numeric()
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(','),
                TextInput::make('potongan')
                    ->numeric()
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(','),
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
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari')
                            ->default(now()->startOfMonth()),
                        DatePicker::make('sampai')
                            ->default(now()->endOfMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['dari'], fn ($q, $date) => $q->whereDate('tanggal', '>=', $date))
                            ->when($data['sampai'], fn ($q, $date) => $q->whereDate('tanggal', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['dari'] ?? null) {
                            $indicators[] = Indicator::make('Dari '.Carbon::parse($data['dari'])->toFormattedDateString())
                                ->removeField('dari');
                        }

                        if ($data['sampai'] ?? null) {
                            $indicators[] = Indicator::make('Sampai '.Carbon::parse($data['sampai'])->toFormattedDateString())
                                ->removeField('sampai');
                        }

                        return $indicators;
                    }),
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
                ExcelExportAction::make('export_excel')
                    ->exports([
                        ExcelExport::make('table')
                            ->withColumns([
                                Column::make('tanggal')
                                    ->formatStateUsing(fn (string $state): string => Carbon::parse($state)->format('d-m-Y')),
                            ])
                            ->withFilename(fn () => 'detail_penghasilan_'.$this->ownerRecord->nama.'_'.now()->format('Ymd_His'))
                            ->fromTable(),
                    ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
