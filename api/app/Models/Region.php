<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Region extends Model {
    protected $guarded = [];
    public function sites() { return $this->hasMany(Site::class); }
    public function users() { return $this->belongsToMany(User::class); }
}
