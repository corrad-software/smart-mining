<?php

namespace App\Filament\Resources\Reports\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
                DatePicker::make('date'),
                TextInput::make('type'),
                TextInput::make('severity'),
                TextInput::make('status')
                    ->required()
                    ->default('New'),
                Textarea::make('scope')
                    ->columnSpanFull(),
                Textarea::make('issue')
                    ->columnSpanFull(),
                Textarea::make('action')
                    ->columnSpanFull(),
                Textarea::make('remarks')
                    ->columnSpanFull(),
                TextInput::make('media'),
                TextInput::make('site_id')
                    ->numeric(),
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('legacy_id'),
            ]);
    }
}
