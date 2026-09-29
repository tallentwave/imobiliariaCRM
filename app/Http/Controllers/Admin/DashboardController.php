<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionEvent;
use App\Models\Deal;
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
        $isBackOffice = $isAdmin || $isFinance || $user->hasRole('captador') || $user->hasRole('compliance');

        $propertiesQuery = Property::query()->where('organization_id', $user->organization_id);
        $leadsQuery = Lead::query()->where('organization_id', $user->organization_id);
        $dealsQuery = Deal::query()->where('organization_id', $user->organization_id);

        if (! $isBackOffice) {
            $propertiesQuery->where('agent_id', $user->id);
            $leadsQuery->where('agent_id', $user->id);
            $dealsQuery->where('agent_user_id', $user->id);
        }

        $stats = [
            'properties_total' => (clone $propertiesQuery)->count(),
            'properties_available' => (clone $propertiesQuery)->where('status', 'disponivel')->count(),
            'leads_month' => (clone $leadsQuery)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'leads_open' => (clone $leadsQuery)->whereIn('stage', Lead::OPEN_STAGES)->count(),
            'sla_overdue' => (clone $leadsQuery)->whereIn('stage', Lead::OPEN_STAGES)->where('sla_due_at', '<', now())->count(),
            'deals_won_month' => (clone $dealsQuery)->where('status', 'CLOSED_WON')
                ->whereMonth('closed_at', now()->month)->whereYear('closed_at', now()->year)->count(),
            'commission_month' => CommissionEvent::whereHas('deal', fn ($q) => $q->where('organization_id', $user->organization_id))
                ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)
                ->sum('total_commission_value'),
        ];

        $leadsByStage = (clone $leadsQuery)
            ->selectRaw('stage, count(*) as total')
            ->groupBy('stage')
            ->pluck('total', 'stage');

        $upcomingVisits = Visit::query()
            ->whereHas('property', fn ($q) => $q->where('organization_id', $user->organization_id))
            ->when(! $isBackOffice, fn ($q) => $q->where('agent_id', $user->id))
            ->where('status', 'agendada')
            ->where('scheduled_at', '>=', now())
            ->with(['property', 'agent'])
            ->orderBy('scheduled_at')
            ->take(8)
            ->get();

        $recentLeads = (clone $leadsQuery)->with(['property', 'agent', 'contact'])->latest()->take(8)->get();

        $monthlySeries = $this->buildMonthlySeries($leadsQuery, $dealsQuery);

        return view('admin.dashboard', compact('stats', 'leadsByStage', 'upcomingVisits', 'recentLeads', 'monthlySeries'));
    }

    private function buildMonthlySeries($leadsQuery, $dealsQuery): array
    {
        $months = 6;
        $labels = [];
        $leads = [];
        $deals = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = ucfirst($date->translatedFormat('M/y'));

            $leads[] = (clone $leadsQuery)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $deals[] = (clone $dealsQuery)
                ->where('status', 'CLOSED_WON')
                ->whereYear('closed_at', $date->year)
                ->whereMonth('closed_at', $date->month)
                ->count();
        }

        return ['labels' => $labels, 'leads' => $leads, 'deals' => $deals];
    }
}
