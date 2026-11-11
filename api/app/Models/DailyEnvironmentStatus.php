<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DailyEnvironmentStatus extends Model {
    protected $guarded = [];
    protected $casts = ["spilltray_images"=>"array","papan_tanda_images"=>"array","tanda_sempadan_images"=>"array","laluan_keluar_masuk_images"=>"array"]; public function dailyReport(){ return $this->belongsTo(DailyReport::class); }
}
