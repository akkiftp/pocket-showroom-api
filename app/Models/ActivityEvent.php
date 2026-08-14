<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityEvent extends Model
{
    protected $fillable = ['business_id','visitor_session_id','product_id','event_type','source','metadata'];
    protected $casts = ['metadata'=>'array'];
    public function business(){ return $this->belongsTo(Business::class); }
    public function visitor(){ return $this->belongsTo(VisitorSession::class, 'visitor_session_id'); }
    public function product(){ return $this->belongsTo(Product::class); }
}
