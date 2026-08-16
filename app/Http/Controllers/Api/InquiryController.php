<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BusinessContext;
use App\Models\Business;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    private function business(Request $request): Business { return BusinessContext::require($request); }

    private function owned(Request $request, Inquiry $inquiry): Inquiry
    {
        $business = $this->business($request);
        abort_unless($inquiry->business_id === $business->id, 404);
        return $inquiry;
    }

    public function index(Request $request)
    {
        $business = $this->business($request);

        $query = $business->inquiries()
            ->with('product:id,name');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        return response()->json(
            $query->latest()->paginate(min(max($request->integer('per_page', 20), 1), 100))
        );
    }

    public function show(Request $request, Inquiry $inquiry)
    {
        $inquiry = $this->owned($request, $inquiry);

        return response()->json([
            'inquiry' => $inquiry->load('product'),
        ]);
    }

    public function handled(Request $request, Inquiry $inquiry)
    {
        $inquiry = $this->owned($request, $inquiry);
        $inquiry->update(['status' => 'handled']);

        return response()->json([
            'message' => 'Inquiry marked handled.',
            'inquiry' => $inquiry->fresh()->load('product:id,name'),
        ]);
    }

    public function pending(Request $request, Inquiry $inquiry)
    {
        $inquiry = $this->owned($request, $inquiry);
        $inquiry->update(['status' => 'pending']);

        return response()->json([
            'message' => 'Inquiry marked pending.',
            'inquiry' => $inquiry->fresh()->load('product:id,name'),
        ]);
    }

    public function destroy(Request $request, Inquiry $inquiry)
    {
        $inquiry = $this->owned($request, $inquiry);
        $inquiry->delete();

        return response()->json([
            'message' => 'Inquiry deleted.',
        ]);
    }
}
