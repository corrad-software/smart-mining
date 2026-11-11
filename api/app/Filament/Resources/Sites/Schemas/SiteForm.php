<?php

namespace App\Filament\Resources\Sites\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('site_code'),
                TextInput::make('region_id')
                    ->numeric(),
                TextInput::make('contractor_id')
                    ->numeric(),
                TextInput::make('site_type_id')
                    ->numeric(),
                TextInput::make('type'),
                TextInput::make('product'),
                TextInput::make('supervisor'),
                Toggle::make('is_activated')
                    ->required(),
                TextInput::make('lat')
                    ->numeric(),
                TextInput::make('lng')
                    ->numeric(),
                TextInput::make('total_acreage')
                    ->numeric(),
                TextInput::make('reserve_land')
                    ->numeric(),
                TextInput::make('remaining_land')
                    ->numeric(),
                DatePicker::make('permit_date'),
                DatePicker::make('permit_date_end'),
                DatePicker::make('luas_permit_date'),
                TextInput::make('geojson'),
                TextInput::make('geojson_url')
                    ->url(),
                TextInput::make('legacy_id'),
            ]);
    }
}
