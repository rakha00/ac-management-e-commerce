<?php

namespace App\Filament\Resources\Karyawans\RelationManagers;

use Filament\Actions\Action;
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

class DetailPenghasilanRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPenghasilan';

    protected static ?string $recordTitleAttribute = 'tanggal';

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
                Action::make('export_excel')
                    ->label('Export Excel')
                    ->form([
                        DatePicker::make('from')->label('Tanggal dari'),
                        DatePicker::make('until')->label('Tanggal sampai'),
                    ])
                    ->action(function (array $data) {
                        $owner = $this->getOwnerRecord();

                        $query = $owner->detailPenghasilan()
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $q, $date) => $q->whereDate('tanggal', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $q, $date) => $q->whereDate('tanggal', '<=', $date)
                            );

                        $rows = $query->get([
                            'tanggal',
                            'kasbon',
                            'lembur',
                            'bonus',
                            'potongan',
                            'keterangan',
                        ]);

                        $filename = 'detail_penghasilan_'.($owner->nama ?? 'karyawan').'_'.now()->format('Ymd_His').'.csv';

                        $handle = fopen('php://temp', 'r+');

                        // Header
                        fputcsv($handle, [
                            'tanggal',
                            'kasbon',
                            'lembur',
                            'bonus',
                            'potongan',
                            'keterangan',
                        ]);

                        foreach ($rows as $row) {
                            fputcsv($handle, [
                                optional($row->tanggal)->format('Y-m-d'),
                                $row->kasbon,
                                $row->lembur,
                                $row->bonus,
                                $row->potongan,
                                $row->keterangan,
                            ]);
                        }

                        rewind($handle);

                        return response()->streamDownload(function () use ($handle) {
                            fpassthru($handle);
                            fclose($handle);
                        }, $filename, [
                            'Content-Type' => 'text/csv',
                        ]);
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
