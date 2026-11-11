<?php

namespace App\Filament\Resources\WeeklyReports\Tables;

use App\Services\F001Workflow;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class WeeklyReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_no')->label('Document')->searchable(),
                TextColumn::make('site.name')->label('Site')->searchable()->limit(30),
                TextColumn::make('contractor.name')->label('Contractor')->limit(20)->toggleable(),
                TextColumn::make('preparedBy.username')->label('Prepared by')->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'submitted' => 'success',
                        'pending_approval' => 'info',
                        'pending_review' => 'warning',
                        'returned' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('start_effective_date')->label('Period')->date()->sortable(),
                TextColumn::make('end_effective_date')->date()->sortable()->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->recordActions([
                ActionGroup::make([
                    self::flow('submit_for_review', 'Submit for Review', 'pending_review', 'draft', 'f001.submit_review', 'heroicon-o-paper-airplane'),
                    self::flow('resubmit', 'Resubmit', 'pending_review', 'returned', 'f001.submit_review', 'heroicon-o-paper-airplane'),
                    self::flow('validate', 'Validate → Manager', 'pending_approval', 'pending_review', 'f001.validate', 'heroicon-o-check'),
                    self::flow('final_approve', 'Final Approve', 'submitted', 'pending_approval', 'f001.final_approve', 'heroicon-o-check-badge'),
                    self::returnAction(),
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    /** A workflow transition action, shown only from `$fromStatus` and when the user may perform it. */
    protected static function flow(string $name, string $label, string $to, string $fromStatus, string $perm, string $icon): Action
    {
        return Action::make($name)
            ->label($label)
            ->icon($icon)
            ->color('primary')
            ->requiresConfirmation()
            ->visible(fn ($record) => $record->status === $fromStatus && (Auth::user()?->can($perm) || Auth::user()?->can('f001.override')))
            ->action(function ($record) use ($to) {
                app(F001Workflow::class)->transition($record, $to, Auth::user());
                Notification::make()->title("Report moved to {$to}")->success()->send();
            });
    }

    protected static function returnAction(): Action
    {
        return Action::make('return')
            ->label('Return for Correction')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('danger')
            ->requiresConfirmation()
            ->visible(fn ($record) => in_array($record->status, ['pending_review', 'pending_approval'])
                && (Auth::user()?->can('f001.return') || Auth::user()?->can('f001.override')))
            ->action(function ($record) {
                app(F001Workflow::class)->transition($record, 'returned', Auth::user());
                Notification::make()->title('Report returned for correction')->warning()->send();
            });
    }
}
