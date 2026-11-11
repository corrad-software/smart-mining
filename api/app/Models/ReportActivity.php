<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ReportActivity extends Model {
    protected $guarded = [];
    public function weeklyReport(){ return $this->belongsTo(WeeklyReport::class); } public function actionBy(){ return $this->belongsTo(User::class, "action_by"); }
}
