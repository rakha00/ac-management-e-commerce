<?php

namespace App\Filament\Resources\PiutangJasas\RelationManagers;

use App\Models\PiutangJasa;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailPiutangJasaCicilanRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPiutangJasaCicilan';

    protected static ?string $recordTitleAttribute = 'tanggal_cicilan';

    protected static ?string $pluralLabel = 'cicilan piutang jasa';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nominal_cicilan')
                    ->numeric()
                    ->required()
                    ->default(0),
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

        if (! $owner instanceof PiutangJasa) {
            return;
        }

        // Sum cicilan yang belum di-soft delete (default relasi exclude trashed)
        $totalCicilan = $owner->detailPiutangJasaCicilan()->sum('nominal_cicilan');
        $totalPiutang = (int) ($owner->total_piutang ?? 0);
        $sisa = max($totalPiutang - (int) $totalCicilan, 0);

        // Update status pembayaran otomatis
        $status = 'belum lunas';
        if ($sisa <= 0 && $totalPiutang > 0) {
            $status = 'sudah lunas';
        } elseif ($sisa < $totalPiutang && $sisa > 0) {
            $status = 'tercicil';
        }

        $owner->forceFill([
            'sisa_piutang' => $sisa,
            'status_pembayaran' => $status,
        ])->save();
    }
}
