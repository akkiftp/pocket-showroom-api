<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'business_type',
        'city',
        'address',
        'whatsapp',
        'phone',
        'email',
        'about',
        'logo_path',
        'banner_path',
        'theme_primary',
        'theme_secondary',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'logo_url',
        'banner_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class)->orderBy('sort_order')->orderBy('name');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function customerContacts() { return $this->hasMany(CustomerContact::class); }
    public function visitorSessions() { return $this->hasMany(VisitorSession::class); }
    public function activityEvents() { return $this->hasMany(ActivityEvent::class); }
    public function orders() { return $this->hasMany(Order::class); }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/'.$this->logo_path) : null;
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner_path ? asset('storage/'.$this->banner_path) : null;
    }
}
