<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityEvent extends Model
{
    protected $fillable = [
        'business_id',
        'user_id',
        'visitor_uuid',
        'visitor_session_id',
        'product_id',
        'share_id',
        'event_type',
        'source',
        'platform',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function visitor()
    {
        return $this->belongsTo(VisitorSession::class, 'visitor_session_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shareLink()
    {
        return $this->belongsTo(ShareLink::class, 'share_id');
    }
}
