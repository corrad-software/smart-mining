<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DailySalesRecord extends Model {
    protected $guarded = [];
    public function dailyReport(){ return $this->belongsTo(DailyReport::class); } public function salesProduct(){ return $this->belongsTo(WeeklySalesProduct::class, "weekly_sales_product_id"); }
}
