<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ReportAttendance extends Model {
    protected $guarded = [];
    protected $casts = ["date"=>"date"]; public function dailyReport(){ return $this->belongsTo(DailyReport::class); } public function employee(){ return $this->belongsTo(Employee::class); }
}
