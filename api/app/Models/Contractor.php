<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Contractor extends Model {
    protected $guarded = [];
    public function sites() { return $this->hasMany(Site::class); }
}
