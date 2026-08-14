<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users(Request $request)
    {
        $users = User::with('business')->orderBy('id', 'desc')->get();

        $data = $users->map(function ($u) {
            return [
                'id' => (string) $u->id,
                'name' => $u->name ?? 'Shop Owner',
                'phone' => $u->phone ?? '',
                'email' => $u->email ?? '',
                'authProvider' => $u->auth_provider ?? '',
                'avatarUrl' => $u->avatar_url,
                'businessName' => $u->business?->name ?? ($u->name . ' Showroom'),
                'city' => $u->business?->city ?? 'Varanasi',
                'registeredAt' => $u->created_at?->toIso8601String() ?? now()->toIso8601String(),
                'subscriptionStatus' => $u->subscription_status ?? 'trial',
                'trialExpiresAt' => $u->trial_expires_at?->toIso8601String() ?? now()->addDays(7)->toIso8601String(),
                'subscriptionExpiresAt' => $u->subscription_expires_at?->toIso8601String(),
                'isAdmin' => (bool) $u->is_admin,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function activate(Request $request, $id)
    {
        $months = (int) ($request->input('months') ?: 1);
        $user = User::findOrFail($id);

        $user->subscription_status = 'active';
        $user->subscription_expires_at = now()->addDays(30 * $months);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => "User subscription activated for {$months} month(s).",
            'user' => $user,
        ]);
    }

    public function extendTrial(Request $request, $id)
    {
        $days = (int) ($request->input('days') ?: 7);
        $user = User::findOrFail($id);

        $user->subscription_status = 'trial';
        $user->trial_expires_at = now()->addDays($days);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => "User free trial extended by {$days} days.",
            'user' => $user,
        ]);
    }

    public function block(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->subscription_status = 'blocked';
        $user->save();

        return response()->json([
            'status' => true,
            'message' => "User has been blocked.",
            'user' => $user,
        ]);
    }
}
