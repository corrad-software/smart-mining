<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WeeklyReport extends Model {
    protected $guarded = [];
    protected $casts = [
        'pricing' => 'array', 'start_effective_date' => 'date', 'end_effective_date' => 'date',
    ];
    public const FLOW = ['draft', 'pending_review', 'pending_approval', 'submitted'];

    public function site() { return $this->belongsTo(Site::class); }
    public function contractor() { return $this->belongsTo(Contractor::class); }
    public function preparedBy() { return $this->belongsTo(User::class, 'prepared_by'); }
    public function dailyReports() { return $this->hasMany(DailyReport::class); }
    public function assignments() { return $this->hasMany(WeeklyAssignment::class); }
    public function salesProducts() { return $this->hasMany(WeeklySalesProduct::class); }
    public function notes() { return $this->hasMany(WeeklyNote::class); }
    public function nonCompliances() { return $this->hasMany(NonCompliance::class); }
    public function activities() { return $this->hasMany(ReportActivity::class); }
    public function equipmentEntries() { return $this->hasMany(WeeklyEquipmentEntry::class); }
}
