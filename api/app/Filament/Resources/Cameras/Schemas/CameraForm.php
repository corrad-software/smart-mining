<?php

namespace App\Filament\Resources\Cameras\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CameraForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('type')
                    ->required()
                    ->default('cctv'),
                TextInput::make('location'),
                TextInput::make('lat')
                    ->numeric(),
                TextInput::make('lng')
                    ->numeric(),
                TextInput::make('hls_url')
                    ->url(),
                TextInput::make('site_id')
                    ->numeric(),
                TextInput::make('legacy_id'),
            ]);
    }
}
