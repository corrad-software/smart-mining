<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SiteStandoff extends Model {
    protected $guarded = [];
    protected $casts = ["media" => "array"]; public function site(){ return $this->belongsTo(Site::class); }
}
