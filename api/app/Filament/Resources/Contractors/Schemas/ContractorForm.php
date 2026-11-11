<?php

namespace App\Filament\Resources\Contractors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContractorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('legacy_id'),
            ]);
    }
}
