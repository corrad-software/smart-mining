<?php

namespace App\Filament\Resources\WeeklyReports\Pages;

use App\Filament\Resources\WeeklyReports\WeeklyReportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWeeklyReport extends EditRecord
{
    protected static string $resource = WeeklyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
