<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'category_id',
        'name',
        'slug',
        'sku',
        'price',
        'offer_price',
        'description',
        'in_stock',
        'featured',
        'is_active',
        'sort_order',
        'views_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'in_stock' => 'boolean',
        'featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderByDesc('is_primary')->orderBy('sort_order')->orderBy('id');
    }

    public function getSellingPriceAttribute()
    {
        return $this->offer_price ?: $this->price;
    }
}
