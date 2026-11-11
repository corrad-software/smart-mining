<?php

namespace App\Filament\Resources\WeeklyReports\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WeeklyReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('document_no'),
                TextInput::make('site_id')
                    ->numeric(),
                TextInput::make('contractor_id')
                    ->numeric(),
                TextInput::make('prepared_by')
                    ->numeric(),
                TextInput::make('reviewed_by'),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                TextInput::make('cycle_type'),
                DatePicker::make('start_effective_date'),
                DatePicker::make('end_effective_date'),
                TextInput::make('cctv_luar_qty')
                    ->numeric(),
                TextInput::make('cctv_luar_status'),
                TextInput::make('cctv_dalam_kabin_qty')
                    ->numeric(),
                TextInput::make('cctv_dalam_kabin_status'),
                TextInput::make('pricing'),
                TextInput::make('legacy_id'),
            ]);
    }
}
