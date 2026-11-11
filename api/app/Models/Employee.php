<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Employee extends Model {
    protected $guarded = [];
    protected $casts = ['is_activated' => 'boolean', 'is_deleted' => 'boolean'];
    public function sites() { return $this->belongsToMany(Site::class); }
    public function attendances() { return $this->hasMany(ReportAttendance::class); }
}
