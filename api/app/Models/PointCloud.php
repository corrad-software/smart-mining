<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PointCloud extends Model {
    protected $guarded = [];
    public function site(){ return $this->belongsTo(Site::class); }
}
