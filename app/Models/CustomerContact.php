<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerContact extends Model
{
    protected $fillable = ['business_id','name','phone','email','notes','last_activity_at'];
    protected $casts = ['last_activity_at' => 'datetime'];
    public function business() { return $this->belongsTo(Business::class); }
}
