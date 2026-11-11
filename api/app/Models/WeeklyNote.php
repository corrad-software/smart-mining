<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WeeklyNote extends Model {
    protected $guarded = [];
    public function weeklyReport(){ return $this->belongsTo(WeeklyReport::class); }
}
