<?php

namespace App\Http\Controllers;

use App\Models\SavedSearch;
use Illuminate\Http\Request;

class SavedSearchController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'filters' => ['required', 'array'],
        ]);

        $request->user()->savedSearches()->create([
            'name' => $data['name'] ?? 'Minha busca',
            'filters' => $data['filters'],
        ]);

        return back()->with('success', 'Busca salva! Você receberá novidades por e-mail.');
    }

    public function destroy(SavedSearch $savedSearch)
    {
        abort_unless($savedSearch->user_id === auth()->id(), 403);

        $savedSearch->delete();

        return back()->with('success', 'Busca removida.');
    }
}
