<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MonitoringReport extends Model {
    protected $guarded = [];
    protected $casts = ["files"=>"array"]; public function uploadBy(){ return $this->belongsTo(User::class, "upload_by"); }
}
