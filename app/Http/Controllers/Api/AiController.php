<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Services\BusinessContext;
use App\Services\OpenAiProductAssistant;
use Illuminate\Http\Request;
class AiController extends Controller
{
    public function productDraft(Request $request, OpenAiProductAssistant $assistant)
    {
        abort_unless($request->user()->canDo('ai.use'),403,'AI permission required.');
        $business=BusinessContext::require($request);
        $data=$request->validate(['instruction'=>['required','string','min:3','max:4000']]);
        try {
            $draft=$assistant->parseProduct($data['instruction'],$business->categories()->pluck('name')->all());
            return response()->json(['success'=>true,'draft'=>$draft]);
        } catch(\Throwable $e) {
            report($e);
            return response()->json(['success'=>false,'message'=>$e->getMessage()],503);
        }
    }
}
