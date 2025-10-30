<?php

namespace App\Filament\Resources\HutangProduk\RelationManagers;

use App\Models\HutangProduk;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HutangProdukCicilanDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'hutangProdukCicilanDetail';

    protected static ?string $pluralLabel = 'cicilan hutang produk';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nominal_cicilan')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->minValue(1)
                    ->maxValue(function () {
                        $owner = $this->getOwnerRecord();
                        if (! $owner) {
                            return null;
                        }
                        $total = (int) ($owner->total_hutang ?? 0);
                        $paid = (int) $owner->hutangProdukCicilanDetail()->sum('nominal_cicilan');

                        return max($total - $paid, 0);
                    })
                    ->rules(function () {
                        $owner = $this->getOwnerRecord();
                        if (! $owner) {
                            return ['numeric', 'min:1'];
                        }
                        $total = (int) ($owner->total_hutang ?? 0);
                        $paid = (int) $owner->hutangProdukCicilanDetail()->sum('nominal_cicilan');
                        $sisa = max($total - $paid, 0);

                        return ['numeric', 'min:1', 'max:'.$sisa];
                    })
                    ->helperText(function () {
                        $owner = $this->getOwnerRecord();
                        if (! $owner) {
                            return null;
                        }
                        $total = (int) ($owner->total_hutang ?? 0);
                        $paid = (int) $owner->hutangProdukCicilanDetail()->sum('nominal_cicilan');
                        $sisa = max($total - $paid, 0);

                        return 'Sisa hutang: Rp '.number_format($sisa, 0, ',', '.');
                    }),
                DatePicker::make('tanggal_cicilan')
                    ->required(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([SoftDeletingScope::class]))
            ->recordTitle(fn ($record): string => "{$record->tanggal_cicilan} ({$record->nominal_cicilan})")
            ->columns([
                TextColumn::make('tanggal_cicilan')
                    ->date()
                    ->sortable(),
                TextColumn::make('nominal_cicilan')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->afterFormValidated(function () {
                        dd('test');
                    })
                    ->after(fn () => $this->recalculateParent()),
                DeleteAction::make()
                    ->after(fn () => $this->recalculateParent()),
                ForceDeleteAction::make()
                    ->after(fn () => $this->recalculateParent()),
                RestoreAction::make()
                    ->after(fn () => $this->recalculateParent()),
            ])
            ->headerActions([
                CreateAction::make()
                    ->afterFormFilled(function () {
                        dd($this->getFormData());
                    })
                    ->after(fn () => $this->recalculateParent()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(fn () => $this->recalculateParent()),
                    ForceDeleteBulkAction::make()
                        ->after(fn () => $this->recalculateParent()),
                ]),
            ]);
    }

    protected function recalculateParent(): void
    {
        $owner = $this->getOwnerRecord();

        if (! $owner instanceof HutangProduk) {
            return;
        }

        $totalCicilan = $owner->hutangProdukCicilanDetail()->sum('nominal_cicilan');
        $totalHutang = (int) ($owner->total_hutang ?? 0);
        $sisa = max($totalHutang - (int) $totalCicilan, 0);

        $status = 'belum lunas';
        if ($sisa <= 0 && $totalHutang > 0) {
            $status = 'sudah lunas';
        } elseif ($sisa < $totalHutang && $sisa > 0) {
            $status = 'tercicil';
        }

        $owner->forceFill([
            'status_pembayaran' => $status,
        ])->save();
    }
}
