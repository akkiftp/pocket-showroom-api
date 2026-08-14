<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorSession extends Model
{
    protected $fillable = ['business_id','visitor_token','customer_name','phone','email','source','referrer','ip_hash','user_agent','events_count','first_seen_at','last_seen_at'];
    protected $casts = ['first_seen_at'=>'datetime','last_seen_at'=>'datetime'];
    public function business(){ return $this->belongsTo(Business::class); }
    public function events(){ return $this->hasMany(ActivityEvent::class); }
}
