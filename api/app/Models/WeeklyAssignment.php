<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WeeklyAssignment extends Model {
    protected $guarded = [];
    public function weeklyReport(){ return $this->belongsTo(WeeklyReport::class); } public function employee(){ return $this->belongsTo(Employee::class); }
}
