<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Setting;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'purpose', 'type', 'city', 'neighborhood', 'bedrooms',
            'parking_spots', 'price_min', 'price_max', 'q',
        ]);

        $properties = Property::published()
            ->with('images')
            ->filter($filters)
            ->orderByDesc('featured')
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        $cities = Property::published()->select('city')->distinct()->whereNotNull('city')->orderBy('city')->pluck('city');

        return view('site.properties.index', compact('properties', 'filters', 'cities'));
    }

    public function show(Property $property)
    {
        abort_if(in_array($property->status, ['inativo']), 404);

        $property->increment('views_count');
        $property->load(['images', 'features', 'agent']);

        $settings = Setting::current();

        $related = Property::published()
            ->where('id', '!=', $property->id)
            ->where('city', $property->city)
            ->where('purpose', $property->purpose)
            ->with('images')
            ->take(4)
            ->get();

        $isFavorited = auth()->check()
            ? auth()->user()->favorites()->where('property_id', $property->id)->exists()
            : false;

        return view('site.properties.show', compact('property', 'related', 'settings', 'isFavorited'));
    }
}
