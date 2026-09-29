<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Property;
use App\Models\Visit;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');
        $isFinance = $user->hasRole('financeiro');

        $propertiesQuery = Property::query();
        $leadsQuery = Lead::query();

        if (! $isAdmin && ! $isFinance) {
            $propertiesQuery->where('agent_id', $user->id);
            $leadsQuery->where('agent_id', $user->id);
        }

        $stats = [
            'properties_total' => (clone $propertiesQuery)->count(),
            'properties_available' => (clone $propertiesQuery)->where('status', 'disponivel')->count(),
            'leads_month' => (clone $leadsQuery)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'leads_open' => (clone $leadsQuery)->whereNotIn('stage', ['fechado_ganho', 'fechado_perdido'])->count(),
            'deals_won_month' => (clone $leadsQuery)->where('stage', 'fechado_ganho')
                ->whereMonth('closed_at', now()->month)->whereYear('closed_at', now()->year)->count(),
            'commission_month' => (clone $leadsQuery)->where('stage', 'fechado_ganho')
                ->whereMonth('closed_at', now()->month)->whereYear('closed_at', now()->year)
                ->sum('commission_value'),
        ];

        $leadsByStage = (clone $leadsQuery)
            ->selectRaw('stage, count(*) as total')
            ->groupBy('stage')
            ->pluck('total', 'stage');

        $upcomingVisits = Visit::query()
            ->when(! $isAdmin && ! $isFinance, fn ($q) => $q->where('agent_id', $user->id))
            ->where('status', 'agendada')
            ->where('scheduled_at', '>=', now())
            ->with(['property', 'agent'])
            ->orderBy('scheduled_at')
            ->take(8)
            ->get();

        $recentLeads = (clone $leadsQuery)->with(['property', 'agent'])->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'leadsByStage', 'upcomingVisits', 'recentLeads'));
    }
}
