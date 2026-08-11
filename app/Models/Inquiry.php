<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'business_id',
        'product_id',
        'customer_name',
        'phone',
        'email',
        'message',
        'status',
        'source',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
