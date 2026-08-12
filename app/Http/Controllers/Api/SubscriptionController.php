<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function status(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'status' => true,
            'subscription_status' => $user->subscription_status ?? 'trial',
            'trial_expires_at' => $user->trial_expires_at?->toIso8601String(),
            'subscription_expires_at' => $user->subscription_expires_at?->toIso8601String(),
            'is_trial_active' => $user->is_trial_active ?? true,
            'days_remaining_in_trial' => $user->days_remaining_in_trial ?? 7,
            'is_expired' => $user->is_expired ?? false,
            'is_admin' => $user->is_admin ?? false,
        ]);
    }
}
