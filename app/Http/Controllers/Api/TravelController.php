<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TravelVehicle;
use App\Models\TravelRoute;
use App\Models\TravelBooking;
use App\Services\BusinessContext;
use Illuminate\Http\Request;

class TravelController extends Controller
{
    public function publicSearch(Request $r)
    {
        $q = TravelRoute::query()->where('is_active', true)->whereHas('business', fn($x) => $x->where('is_active', true))->with(['business:id,name,slug,logo_path,phone,whatsapp,city', 'vehicle']);
        if ($r->filled('from')) $q->where('from_city', 'like', '%' . trim($r->from) . '%');
        if ($r->filled('to')) $q->where('to_city', 'like', '%' . trim($r->to) . '%');
        return $q->latest('id')->paginate(30);
    }

    public function vehicles(Request $r)
    {
        $b = BusinessContext::require($r);
        return $b->travelVehicles()->latest('id')->paginate(50);
    }

    public function storeVehicle(Request $r)
    {
        $b = BusinessContext::require($r);
        $d = $r->validate([
            'name' => 'required|string|max:150',
            'vehicle_type' => 'required|in:car,taxi,bus,van,tempo,other',
            'registration_number' => 'nullable|string|max:50',
            'seats' => 'nullable|integer|min:1|max:100',
            'ac' => 'nullable|boolean',
            'price_per_km' => 'nullable|numeric|min:0',
            'price_per_day' => 'nullable|numeric|min:0',
            'image_url' => 'nullable|url|max:700',
            'is_active' => 'nullable|boolean',
        ]);
        return response()->json(['success' => true, 'vehicle' => $b->travelVehicles()->create($d)], 201);
    }

    public function destroyVehicle(Request $r, TravelVehicle $vehicle)
    {
        $b = BusinessContext::require($r);
        abort_unless($vehicle->business_id === $b->id, 404);
        $vehicle->delete();
        return ['success' => true];
    }

    public function routes(Request $r)
    {
        $b = BusinessContext::require($r);
        return $b->travelRoutes()->with('vehicle')->latest('id')->paginate(50);
    }

    public function storeRoute(Request $r)
    {
        $b = BusinessContext::require($r);
        $d = $r->validate([
            'vehicle_id' => 'nullable|integer|exists:travel_vehicles,id',
            'from_city' => 'required|string|max:120',
            'to_city' => 'required|string|max:120',
            'departure_time' => 'nullable|string|max:20',
            'arrival_time' => 'nullable|string|max:20',
            'fare' => 'nullable|numeric|min:0',
            'days_of_week' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);
        if (!empty($d['vehicle_id'])) {
            abort_unless($b->travelVehicles()->whereKey($d['vehicle_id'])->exists(), 422);
        }
        return response()->json(['success' => true, 'route' => $b->travelRoutes()->create($d)], 201);
    }

    public function destroyRoute(Request $r, TravelRoute $route)
    {
        $b = BusinessContext::require($r);
        abort_unless($route->business_id === $b->id, 404);
        $route->delete();
        return ['success' => true];
    }

    public function book(Request $r)
    {
        $d = $r->validate([
            'business_id' => 'required|integer|exists:businesses,id',
            'vehicle_id' => 'nullable|integer|exists:travel_vehicles,id',
            'route_id' => 'nullable|integer|exists:travel_routes,id',
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'required|string|max:25',
            'pickup' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'travel_date' => 'required|date|after_or_equal:today',
            'travel_time' => 'nullable|string|max:20',
            'passengers' => 'required|integer|min:1|max:100',
            'notes' => 'nullable|string|max:2000',
        ]);
        $d['user_id'] = $r->user()?->id;
        $booking = TravelBooking::create($d);
        return response()->json(['success' => true, 'message' => 'Travel booking request sent.', 'booking' => $booking], 201);
    }

    public function bookings(Request $r)
    {
        $b = BusinessContext::require($r);
        return TravelBooking::where('business_id', $b->id)->with(['vehicle', 'route'])->latest('id')->paginate(30);
    }

    public function status(Request $r, TravelBooking $booking)
    {
        $b = BusinessContext::require($r);
        abort_unless($booking->business_id === $b->id, 404);
        $d = $r->validate([
            'status' => 'required|in:pending,accepted,confirmed,in_transit,completed,cancelled',
            'quoted_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
        ]);
        $booking->update($d);
        return ['success' => true, 'booking' => $booking->fresh(['vehicle', 'route'])];
    }
}
