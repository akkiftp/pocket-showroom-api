<?php

namespace App\Services;

use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;

class BusinessContext
{
    public static function forUser(User $user, ?int $requestedBusinessId = null): ?Business
    {
        if ($user->isSuperAdmin()) {
            if ($requestedBusinessId) {
                return Business::query()->find($requestedBusinessId);
            }
            return null;
        }

        if ($user->role === User::ROLE_SHOP_ADMIN) {
            return $user->business_id ? Business::query()->find($user->business_id) : null;
        }

        return Business::query()->where('user_id', $user->id)->first();
    }

    public static function require(Request $request): Business
    {
        $user = $request->user();
        abort_unless($user && $user->is_active, 403, 'Your account is inactive.');

        $requestedBusinessId = $request->integer('business_id') ?: null;
        $business = self::forUser($user, $requestedBusinessId);

        abort_unless($business, 404, $user->isSuperAdmin()
            ? 'Choose a business using business_id.'
            : 'Business not found for this account.');

        abort_unless($business->is_active || $user->isSuperAdmin(), 403, 'This showroom is inactive.');
        return $business;
    }
}
