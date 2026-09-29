<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientAreaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $favorites = $user->favorites()->with('images')->latest()->get();
        $savedSearches = $user->savedSearches()->latest()->get();

        $visits = \App\Models\Visit::whereHas('lead', fn ($q) => $q->where('user_id', $user->id))
            ->with(['property', 'agent'])
            ->orderByDesc('scheduled_at')
            ->get();

        $leads = \App\Models\Lead::where('user_id', $user->id)
            ->with(['property', 'agent'])
            ->latest()
            ->get();

        return view('client.index', compact('favorites', 'savedSearches', 'visits', 'leads'));
    }
}
