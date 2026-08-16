<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_SHOP_OWNER = 'shop_owner';
    public const ROLE_SHOP_ADMIN = 'shop_admin';

    public const SHOP_ADMIN_PERMISSIONS = [
        'dashboard.view','products.view','products.create','products.update','products.delete',
        'categories.manage','orders.view','orders.manage','inquiries.view','inquiries.manage',
        'customers.view','customers.manage','analytics.view','business.view','business.update',
        'showroom.share','ai.use',
    ];

    protected $fillable = [
        'firebase_uid','auth_provider','name','phone','email','email_verified_at','avatar_url',
        'subscription_status','trial_expires_at','subscription_expires_at','is_admin',
        'role','business_id','permissions','is_active','created_by','password',
    ];

    protected $hidden = ['password','remember_token','firebase_uid'];

    protected $casts = [
        'email_verified_at'=>'datetime','trial_expires_at'=>'datetime','subscription_expires_at'=>'datetime',
        'is_admin'=>'boolean','is_active'=>'boolean','permissions'=>'array',
    ];

    protected $appends = ['is_trial_active','days_remaining_in_trial','is_expired','role_label'];

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->is_admin) $user->role = self::ROLE_SUPER_ADMIN;
            $user->is_admin = $user->role === self::ROLE_SUPER_ADMIN;
        });
    }

    public function isSuperAdmin(): bool { return $this->role === self::ROLE_SUPER_ADMIN || (bool)$this->is_admin; }
    public function isShopOwner(): bool { return $this->role === self::ROLE_SHOP_OWNER; }
    public function isShopAdmin(): bool { return $this->role === self::ROLE_SHOP_ADMIN; }

    public function canDo(string $permission): bool
    {
        if ($this->isSuperAdmin() || $this->isShopOwner()) return true;
        if (!$this->is_active) return false;
        return in_array($permission, $this->permissions ?? [], true);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_SHOP_ADMIN => 'Shop Admin',
            default => 'Shop Owner',
        };
    }

    public function getIsTrialActiveAttribute(): bool
    {
        if ($this->isSuperAdmin() || $this->role === self::ROLE_SHOP_ADMIN) return true;
        if ($this->subscription_status === 'active') return true;
        if (in_array($this->subscription_status, ['blocked','expired'], true)) return false;
        return $this->trial_expires_at ? now()->isBefore($this->trial_expires_at) : true;
    }

    public function getDaysRemainingInTrialAttribute(): int
    {
        if ($this->isSuperAdmin() || $this->role === self::ROLE_SHOP_ADMIN || $this->subscription_status === 'active') return 365;
        if (!$this->trial_expires_at || in_array($this->subscription_status,['blocked','expired'],true)) return 0;
        $diff = now()->diffInDays($this->trial_expires_at, false);
        return $diff < 0 ? 0 : (int)$diff + 1;
    }

    public function getIsExpiredAttribute(): bool
    {
        if ($this->isSuperAdmin() || $this->role === self::ROLE_SHOP_ADMIN) return false;
        if ($this->subscription_status === 'active') return $this->subscription_expires_at ? now()->isAfter($this->subscription_expires_at) : false;
        if ($this->subscription_status === 'blocked') return true;
        return $this->trial_expires_at ? now()->isAfter($this->trial_expires_at) : false;
    }

    // Legacy relation: an owner's business.
    public function business() { return $this->hasOne(Business::class, 'user_id'); }
    public function ownedBusiness() { return $this->hasOne(Business::class, 'user_id'); }
    public function assignedBusiness() { return $this->belongsTo(Business::class, 'business_id'); }
    public function createdStaff() { return $this->hasMany(User::class, 'created_by'); }
}
