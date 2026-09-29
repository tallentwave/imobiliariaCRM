<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:admin|financeiro'),
        ];
    }

    public function index(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $agentId = $request->input('agent_id');

        $deals = $this->baseQuery($month, $year, $agentId)->get();

        $summary = [
            'deals_count' => $deals->count(),
            'total_negotiated' => $deals->sum('negotiated_value'),
            'total_commission' => $deals->sum('commission_value'),
            'commission_paid' => $deals->where('commission_paid', true)->sum('commission_value'),
            'commission_pending' => $deals->where('commission_paid', false)->sum('commission_value'),
        ];

        $byAgent = $deals->groupBy(fn ($lead) => $lead->agent->name ?? 'Sem corretor')
            ->map(function ($group) {
                return [
                    'deals_count' => $group->count(),
                    'total_negotiated' => $group->sum('negotiated_value'),
                    'total_commission' => $group->sum('commission_value'),
                    'commission_paid' => $group->where('commission_paid', true)->sum('commission_value'),
                ];
            })
            ->sortByDesc('total_commission');

        $agents = User::role('corretor')->orderBy('name')->get();

        return view('admin.reports.index', compact('deals', 'summary', 'byAgent', 'agents', 'month', 'year', 'agentId'));
    }

    public function export(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $agentId = $request->input('agent_id');

        $deals = $this->baseQuery($month, $year, $agentId)->get();

        $filename = "comissoes-{$year}-{$month}.csv";

        $callback = function () use ($deals) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Data fechamento', 'Corretor', 'Imóvel', 'Cliente', 'Valor negociado', 'Comissão (%)', 'Comissão (R$)', 'Paga?']);

            foreach ($deals as $deal) {
                fputcsv($handle, [
                    optional($deal->closed_at)->format('d/m/Y'),
                    $deal->agent->name ?? '—',
                    $deal->property->title ?? '—',
                    $deal->name,
                    number_format((float) $deal->negotiated_value, 2, ',', '.'),
                    $deal->commission_percent,
                    number_format((float) $deal->commission_value, 2, ',', '.'),
                    $deal->commission_paid ? 'Sim' : 'Não',
                ]);
            }

            fclose($handle);
        };

        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function markPaid(Lead $lead)
    {
        abort_unless(auth()->user()->can('finance.manage') || auth()->user()->hasRole('admin'), 403);

        $lead->update(['commission_paid' => ! $lead->commission_paid]);

        return back()->with('success', 'Status de pagamento atualizado.');
    }

    private function baseQuery(int $month, int $year, ?string $agentId)
    {
        return Lead::query()
            ->where('stage', 'fechado_ganho')
            ->whereMonth('closed_at', $month)
            ->whereYear('closed_at', $year)
            ->when($agentId, fn ($q, $v) => $q->where('agent_id', $v))
            ->with(['agent', 'property']);
    }
}
