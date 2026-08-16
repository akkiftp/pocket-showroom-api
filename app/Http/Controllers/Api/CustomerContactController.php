<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BusinessContext;
use App\Models\Business;
use App\Models\CustomerContact;
use Illuminate\Http\Request;

class CustomerContactController extends Controller
{
    private function business(Request $request): Business { return BusinessContext::require($request); }

    public function index(Request $request)
    {
        $business=$this->business($request);
        return response()->json(['data'=>$business->customerContacts()->orderByDesc('last_activity_at')->orderByDesc('id')->get()]);
    }

    public function store(Request $request)
    {
        $business=$this->business($request);
        $data=$request->validate(['name'=>['required','string','max:120'],'phone'=>['required','string','max:30'],'email'=>['nullable','email','max:150'],'notes'=>['nullable','string','max:2000']]);
        $phone=preg_replace('/\D+/','',$data['phone']);
        $contact=CustomerContact::updateOrCreate(['business_id'=>$business->id,'phone'=>$phone],[...$data,'phone'=>$phone]);
        return response()->json(['data'=>$contact],$contact->wasRecentlyCreated?201:200);
    }

    public function destroy(Request $request, CustomerContact $customerContact)
    {
        $business=$this->business($request);
        abort_unless($customerContact->business_id===$business->id,404);
        $customerContact->delete();
        return response()->json(['success'=>true]);
    }
}
