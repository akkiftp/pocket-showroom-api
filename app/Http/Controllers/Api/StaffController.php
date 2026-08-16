<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BusinessContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $owner = $request->user();
        abort_unless($owner->isShopOwner() || $owner->isSuperAdmin(), 403);
        $business = BusinessContext::require($request);
        $rows = User::query()->where('role', User::ROLE_SHOP_ADMIN)->where('business_id', $business->id)
            ->orderByDesc('id')->get(['id','name','email','phone','role','business_id','permissions','is_active','created_by','created_at']);
        return response()->json(['data'=>$rows,'available_permissions'=>User::SHOP_ADMIN_PERMISSIONS]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isShopOwner() || $request->user()->isSuperAdmin(), 403);
        $business = BusinessContext::require($request);
        $data = $request->validate([
            'name'=>['required','string','max:120'],
            'email'=>['required','email','max:190','unique:users,email'],
            'phone'=>['nullable','string','max:20','unique:users,phone'],
            'password'=>['nullable','string','min:8','max:100'],
            'permissions'=>['nullable','array'],
            'permissions.*'=>['string', Rule::in(User::SHOP_ADMIN_PERMISSIONS)],
        ]);
        $permissions = $data['permissions'] ?? User::SHOP_ADMIN_PERMISSIONS;
        $user = User::create([
            'name'=>$data['name'],'email'=>strtolower($data['email']),'phone'=>$data['phone']??null,
            'password'=>isset($data['password']) ? Hash::make($data['password']) : null,
            'role'=>User::ROLE_SHOP_ADMIN,'business_id'=>$business->id,'permissions'=>$permissions,
            'is_active'=>true,'created_by'=>$request->user()->id,'subscription_status'=>'active',
        ]);
        return response()->json(['message'=>'Shop admin created.','user'=>$user],201);
    }

    public function update(Request $request, User $staff)
    {
        abort_unless($request->user()->isShopOwner() || $request->user()->isSuperAdmin(),403);
        $business = BusinessContext::require($request);
        abort_unless($staff->role===User::ROLE_SHOP_ADMIN && (int)$staff->business_id===(int)$business->id,404);
        $data=$request->validate([
            'name'=>['sometimes','required','string','max:120'],
            'email'=>['sometimes','required','email','max:190',Rule::unique('users','email')->ignore($staff->id)],
            'phone'=>['nullable','string','max:20',Rule::unique('users','phone')->ignore($staff->id)],
            'password'=>['nullable','string','min:8','max:100'],
            'permissions'=>['nullable','array'],
            'permissions.*'=>['string',Rule::in(User::SHOP_ADMIN_PERMISSIONS)],
            'is_active'=>['sometimes','boolean'],
        ]);
        if(isset($data['password'])) $data['password']=Hash::make($data['password']);
        $staff->update($data);
        return response()->json(['message'=>'Shop admin updated.','user'=>$staff->fresh()]);
    }

    public function destroy(Request $request, User $staff)
    {
        abort_unless($request->user()->isShopOwner() || $request->user()->isSuperAdmin(),403);
        $business = BusinessContext::require($request);
        abort_unless($staff->role===User::ROLE_SHOP_ADMIN && (int)$staff->business_id===(int)$business->id,404);
        $staff->tokens()->delete();
        $staff->delete();
        return response()->json(['message'=>'Shop admin deleted.']);
    }
}
