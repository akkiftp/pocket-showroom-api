<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable=['business_id','visitor_session_id','customer_name','phone','address','total','status','source'];
    protected $casts=['total'=>'decimal:2'];
    public function business(){return $this->belongsTo(Business::class);}
    public function items(){return $this->hasMany(OrderItem::class);}
    public function visitor(){return $this->belongsTo(VisitorSession::class,'visitor_session_id');}
}
