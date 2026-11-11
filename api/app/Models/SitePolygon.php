<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SitePolygon extends Model {
    protected $guarded = [];
    protected $casts = ["features" => "array"]; public function site(){ return $this->belongsTo(Site::class); }
}
