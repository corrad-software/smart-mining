<?php

namespace App\Services;

use App\Models\User;
use App\Models\WeeklyReport;
use App\Models\ReportActivity;
use Illuminate\Validation\ValidationException;

/**
 * Server-side F001 approval state machine.
 * States: draft -> pending_review -> pending_approval -> submitted, plus `returned`.
 * Each transition requires a permission; super_admin may override.
 */
class F001Workflow
{
    /** allowed [from => [to => required-permission]] */
    public const TRANSITIONS = [
        'draft'            => ['pending_review'   => 'f001.submit_review'],
        'pending_review'   => ['pending_approval' => 'f001.validate', 'returned' => 'f001.return'],
        'pending_approval' => ['submitted'        => 'f001.final_approve', 'returned' => 'f001.return'],
        'returned'         => ['pending_review'   => 'f001.submit_review'],
    ];

    public function transition(WeeklyReport $report, string $to, User $actor): WeeklyReport
    {
        $from = $report->status ?? 'draft';
        $isOverride = $actor->can('f001.override');
        $allowed = self::TRANSITIONS[$from][$to] ?? null;

        if (! $isOverride) {
            if ($allowed === null) {
                throw ValidationException::withMessages(['status' => "Illegal transition {$from} → {$to}."]);
            }
            if (! $actor->can($allowed)) {
                throw ValidationException::withMessages(['status' => "You are not authorised to {$to} this report."]);
            }
        }

        $report->update(['status' => $to]);

        ReportActivity::create([
            'weekly_report_id' => $report->id,
            'from_status'      => $from,
            'to_status'        => $to,
            'action_by'        => $actor->id,
            'action_by_name'   => $actor->username ?? $actor->name,
            'action_by_role'   => $actor->roles->pluck('name')->first(),
        ]);

        return $report->refresh();
    }
}
