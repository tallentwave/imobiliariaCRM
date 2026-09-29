<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request, Property $property)
    {
        $user = $request->user();

        $existing = $user->favorites()->where('property_id', $property->id)->exists();

        if ($existing) {
            $user->favorites()->detach($property->id);
            $favorited = false;
        } else {
            $user->favorites()->attach($property->id);
            $favorited = true;
        }

        if ($request->wantsJson()) {
            return response()->json(['favorited' => $favorited]);
        }

        return back()->with('success', $favorited ? 'Imóvel adicionado aos favoritos.' : 'Imóvel removido dos favoritos.');
    }
}
