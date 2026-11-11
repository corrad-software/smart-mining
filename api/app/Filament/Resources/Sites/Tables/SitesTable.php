<?php

namespace App\Filament\Resources\Sites\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('site_code')
                    ->searchable(),
                TextColumn::make('region_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('contractor_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('site_type_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('product')
                    ->searchable(),
                TextColumn::make('supervisor')
                    ->searchable(),
                IconColumn::make('is_activated')
                    ->boolean(),
                TextColumn::make('lat')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('lng')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_acreage')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reserve_land')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('remaining_land')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('permit_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('permit_date_end')
                    ->date()
                    ->sortable(),
                TextColumn::make('luas_permit_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('geojson_url')
                    ->searchable(),
                TextColumn::make('legacy_id')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
