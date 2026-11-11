<?php

namespace App\Filament\Resources\WeeklyReports;

use App\Filament\Resources\WeeklyReports\Pages\CreateWeeklyReport;
use App\Filament\Resources\WeeklyReports\Pages\EditWeeklyReport;
use App\Filament\Resources\WeeklyReports\Pages\ListWeeklyReports;
use App\Filament\Resources\WeeklyReports\Schemas\WeeklyReportForm;
use App\Filament\Resources\WeeklyReports\Tables\WeeklyReportsTable;
use App\Models\WeeklyReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WeeklyReportResource extends Resource
{
    protected static ?string $model = WeeklyReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return WeeklyReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WeeklyReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWeeklyReports::route('/'),
            'create' => CreateWeeklyReport::route('/create'),
            'edit' => EditWeeklyReport::route('/{record}/edit'),
        ];
    }
}
