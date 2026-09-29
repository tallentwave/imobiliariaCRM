<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FeatureController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:admin'),
        ];
    }

    public function index()
    {
        $features = Feature::withCount('properties')->orderBy('name')->get();

        return view('admin.features.index', compact('features'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:features,name'],
        ]);

        Feature::create($data);

        return back()->with('success', 'Característica adicionada.');
    }

    public function destroy(Feature $feature)
    {
        $feature->delete();

        return back()->with('success', 'Característica removida.');
    }
}
