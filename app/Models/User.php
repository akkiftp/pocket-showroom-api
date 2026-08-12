<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'subscription_status',
        'trial_expires_at',
        'subscription_expires_at',
        'is_admin',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'trial_expires_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    protected $appends = [
        'is_trial_active',
        'days_remaining_in_trial',
        'is_expired',
    ];

    public function getIsTrialActiveAttribute(): bool
    {
        if ($this->is_admin) return true;
        if ($this->subscription_status === 'active') return true;
        if ($this->subscription_status === 'blocked' || $this->subscription_status === 'expired') return false;
        return $this->trial_expires_at ? now()->isBefore($this->trial_expires_at) : true;
    }

    public function getDaysRemainingInTrialAttribute(): int
    {
        if ($this->is_admin || $this->subscription_status === 'active') return 365;
        if (!$this->trial_expires_at || $this->subscription_status === 'blocked' || $this->subscription_status === 'expired') return 0;
        $diff = now()->diffInDays($this->trial_expires_at, false);
        return $diff < 0 ? 0 : (int)$diff + 1;
    }

    public function getIsExpiredAttribute(): bool
    {
        if ($this->is_admin) return false;
        if ($this->subscription_status === 'active') {
            if (!$this->subscription_expires_at) return false;
            return now()->isAfter($this->subscription_expires_at);
        }
        if ($this->subscription_status === 'blocked') return true;
        return $this->trial_expires_at ? now()->isAfter($this->trial_expires_at) : false;
    }

    public function business()
    {
        return $this->hasOne(Business::class);
    }
}
