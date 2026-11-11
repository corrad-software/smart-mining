<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ComplaintFile extends Model {
    protected $guarded = [];
    protected $casts = ["files"=>"array"]; public function site(){ return $this->belongsTo(Site::class); }
}
