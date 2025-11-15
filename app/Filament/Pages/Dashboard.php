<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        // Only show filters for admin users
        if (! auth()->check() || auth()->user()->karyawan->jabatan !== 'admin') {
            return $schema->components([]);
        }

        $months = [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];

        $currentYear = date('Y');
        $years = [];
        for ($i = $currentYear; $i >= 2020; $i--) {
            $years[$i] = $i;
        }

        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('month')
                            ->label('Month')
                            ->options($months)
                            ->placeholder('Select Month'),
                        Select::make('year')
                            ->label('Year')
                            ->options($years)
                            ->placeholder('Select Year'),
                    ])
                    ->columns(2)
                    ->footerActions([
                        Action::make('reset')
                            ->label('Reset Filter')
                            ->action('resetFilters')
                            ->color('danger'),
                    ])
                    ->columnSpan(2),
            ]);
    }

    protected function getFiltersFormActions(): array
    {
        return [
            $this->getResetFormAction(),
        ];
    }

    public function resetFilters(): void
    {
        $this->filtersForm->fill();
    }
}
