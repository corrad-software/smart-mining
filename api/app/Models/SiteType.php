<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SiteType extends Model { protected $guarded = []; public function sites(){ return $this->hasMany(Site::class); } }
