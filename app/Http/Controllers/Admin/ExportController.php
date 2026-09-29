<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Setting;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ExportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:admin'),
        ];
    }

    public function index()
    {
        return view('admin.export.index');
    }

    public function zapVivaReal(): Response
    {
        $properties = $this->exportableProperties();
        $settings = Setting::current();

        $xml = view('admin.export.zap-vivareal', compact('properties', 'settings'))->render();

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function olx(): Response
    {
        $properties = $this->exportableProperties();
        $settings = Setting::current();

        $xml = view('admin.export.olx', compact('properties', 'settings'))->render();

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    private function exportableProperties()
    {
        return Property::query()
            ->whereIn('purpose', ['venda', 'aluguel'])
            ->whereIn('status', ['disponivel', 'reservado'])
            ->with(['images', 'features', 'agent'])
            ->orderBy('id')
            ->get();
    }
}
