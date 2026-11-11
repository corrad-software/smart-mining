<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class IsoFile extends Model {
    protected $guarded = [];
    protected $casts = ["files"=>"array"];
}
