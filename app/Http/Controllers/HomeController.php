<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        $settings = Setting::current();

        $featured = Property::published()
            ->featured()
            ->with('images')
            ->latest('published_at')
            ->take(6)
            ->get();

        $latest = Property::published()
            ->with('images')
            ->latest('published_at')
            ->take(8)
            ->get();

        $cities = Property::published()
            ->select('city')
            ->distinct()
            ->whereNotNull('city')
            ->orderBy('city')
            ->pluck('city');

        return view('site.home', compact('settings', 'featured', 'latest', 'cities'));
    }
}
