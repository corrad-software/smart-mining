<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EquipmentDailyCount extends Model {
    protected $guarded = [];
    public function entry(){ return $this->belongsTo(WeeklyEquipmentEntry::class, "weekly_equipment_entry_id"); } public function dailyReport(){ return $this->belongsTo(DailyReport::class); }
}
