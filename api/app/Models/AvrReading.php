<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AvrReading extends Model {
    protected $guarded = [];
    public function dailyReport(){ return $this->belongsTo(DailyReport::class); }
}
