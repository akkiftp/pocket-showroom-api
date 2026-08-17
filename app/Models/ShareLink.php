<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareLink extends Model
{
    protected $fillable = [
        'code',
        'user_id',
        'business_id',
        'product_id',
        'platform',
        'source',
        'click_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function activityEvents()
    {
        return $this->hasMany(ActivityEvent::class, 'share_id');
    }
}
