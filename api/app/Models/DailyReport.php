<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DailyReport extends Model {
    protected $guarded = [];
    protected $casts = ['weather_slots' => 'array', 'is_public_holiday' => 'boolean', 'date' => 'date'];
    public function weeklyReport() { return $this->belongsTo(WeeklyReport::class); }
    public function systemStatus() { return $this->hasOne(DailySystemStatus::class); }
    public function environmentStatus() { return $this->hasOne(DailyEnvironmentStatus::class); }
    public function salesRecords() { return $this->hasMany(DailySalesRecord::class); }
    public function avrReadings() { return $this->hasMany(AvrReading::class); }
    public function attendances() { return $this->hasMany(ReportAttendance::class); }
}
