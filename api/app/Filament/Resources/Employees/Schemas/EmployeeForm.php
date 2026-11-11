<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('type'),
                TextInput::make('job_level'),
                Toggle::make('is_activated')
                    ->required(),
                Toggle::make('is_deleted')
                    ->required(),
                TextInput::make('legacy_id'),
            ]);
    }
}
