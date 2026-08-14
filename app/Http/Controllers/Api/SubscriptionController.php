<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function status(Request $request)
    {
        $user = $request->user();
        $freeMode = (bool) config('pocket_showroom.free_mode', true);

        return response()->json([
            'status' => true,
            'free_mode' => $freeMode,
            'subscription_status' => $freeMode && $user->subscription_status !== 'blocked'
                ? 'active'
                : ($user->subscription_status ?? 'trial'),
            'trial_expires_at' => $user->trial_expires_at?->toIso8601String(),
            'subscription_expires_at' => $user->subscription_expires_at?->toIso8601String(),
            'is_trial_active' => $freeMode ? true : $user->is_trial_active,
            'days_remaining_in_trial' => $freeMode ? 365 : $user->days_remaining_in_trial,
            'is_expired' => $freeMode ? false : $user->is_expired,
            'is_admin' => (bool) $user->is_admin,
        ]);
    }
}
