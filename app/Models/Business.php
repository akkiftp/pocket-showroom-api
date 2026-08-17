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
        'marketplace_category_id',
        'city',
        'location_id',
        'locality',
        'pincode',
        'latitude',
        'longitude',
        'opening_time',
        'closing_time',
        'delivery_available',
        'accepts_orders',
        'is_verified',
        'is_featured',
        'marketplace_views',
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
        'delivery_available' => 'boolean',
        'accepts_orders' => 'boolean',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    protected $appends = [
        'logo_url',
        'banner_url',
        'cover_image_url',
        'showroom_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function marketplaceCategory() { return $this->belongsTo(MarketplaceCategory::class, 'marketplace_category_id'); }
    public function marketplaceLocation() { return $this->belongsTo(MarketplaceLocation::class, 'location_id'); }

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
        if (!$this->logo_path) return null;
        if (str_starts_with($this->logo_path, 'http://') || str_starts_with($this->logo_path, 'https://')) {
            return str_replace('http://', 'https://', $this->logo_path);
        }
        $appUrl = rtrim(config('app.url') ?? 'https://pocket-showroom-api.onrender.com', '/');
        $appUrl = str_replace('http://', 'https://', $appUrl);
        return $appUrl . '/storage/' . ltrim($this->logo_path, '/');
    }

    public function getBannerUrlAttribute(): ?string
    {
        if (!$this->banner_path) return null;
        if (str_starts_with($this->banner_path, 'http://') || str_starts_with($this->banner_path, 'https://')) {
            return str_replace('http://', 'https://', $this->banner_path);
        }
        $appUrl = rtrim(config('app.url') ?? 'https://pocket-showroom-api.onrender.com', '/');
        $appUrl = str_replace('http://', 'https://', $appUrl);
        return $appUrl . '/storage/' . ltrim($this->banner_path, '/');
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->banner_url;
    }

    public function getShowroomUrlAttribute(): string
    {
        $appUrl = rtrim(config('app.url') ?? 'https://pocket-showroom-api.onrender.com', '/');
        $appUrl = str_replace('http://', 'https://', $appUrl);
        return $appUrl . '/showrooms/' . $this->slug;
    }
}
