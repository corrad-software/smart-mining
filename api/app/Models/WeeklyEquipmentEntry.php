<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WeeklyEquipmentEntry extends Model {
    protected $guarded = [];
    protected $casts = ["images" => "array"]; public function weeklyReport(){ return $this->belongsTo(WeeklyReport::class); } public function itemType(){ return $this->belongsTo(EquipmentItemType::class, "equipment_item_type_id"); } public function dailyCounts(){ return $this->hasMany(EquipmentDailyCount::class); }
}
