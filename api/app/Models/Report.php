<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Report extends Model {
    protected $guarded = [];
    protected $hidden = ['polygon'];
    protected $casts = ['media' => 'array', 'date' => 'date'];
    public function site() { return $this->belongsTo(Site::class); }
    public function user() { return $this->belongsTo(User::class); }
}
