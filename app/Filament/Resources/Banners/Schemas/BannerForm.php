<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Banner')
                    ->schema([
                        FileUpload::make('image')
                            ->disk('public')
                            ->directory('banner')
                            ->image()
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1280')
                            ->imageResizeTargetHeight('720')
                            ->required(),
                        Toggle::make('active')
                            ->required(),
                    ])
                    ->columnSpan('full'),
            ]);
    }
}
