<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Site extends Model {
    protected $guarded = [];
    protected $hidden = ['boundary', 'geom'];
    protected $casts = [
        'is_activated' => 'boolean', 'geojson' => 'array', 'documents' => 'array',
        'permit_date' => 'date', 'permit_date_end' => 'date', 'luas_permit_date' => 'date',
    ];
    public function region() { return $this->belongsTo(Region::class); }
    public function contractor() { return $this->belongsTo(Contractor::class); }
    public function siteType() { return $this->belongsTo(SiteType::class); }
    public function cameras() { return $this->hasMany(Camera::class); }
    public function employees() { return $this->belongsToMany(Employee::class); }
    public function weeklyReports() { return $this->hasMany(WeeklyReport::class); }
    public function reports() { return $this->hasMany(Report::class); }
    public function polygons() { return $this->hasMany(SitePolygon::class); }
    public function standoffs() { return $this->hasMany(SiteStandoff::class); }
    public function galleries() { return $this->hasMany(SiteGallery::class); }
    public function pointClouds() { return $this->hasMany(PointCloud::class); }
    public function tiles() { return $this->hasMany(Tile::class); }

    /** Permit lifecycle helper used by UI (Active / Warning / Expired). */
    public function getPermitStatusAttribute(): string {
        if (!$this->permit_date_end) return 'unknown';
        $days = now()->diffInDays($this->permit_date_end, false);
        if ($days < 0) return 'expired';
        if ($days <= 90) return 'warning';
        return 'active';
    }
}
