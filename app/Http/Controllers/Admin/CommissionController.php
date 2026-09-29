<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionEvent;
use App\Models\CommissionPayment;
use App\Models\CommissionSplit;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class CommissionController extends Controller implements HasMiddleware
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

        $events = CommissionEvent::query()
            ->whereHas('deal', fn ($q) => $q->where('organization_id', Auth::user()->organization_id))
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with(['deal.property', 'splits.user'])
            ->latest()
            ->get();

        $summary = [
            'total' => $events->sum('total_commission_value'),
            'paid' => $events->sum(fn ($e) => $e->paidValue()),
            'pending' => $events->sum(fn ($e) => $e->pendingValue()),
        ];

        $byUser = $events->flatMap->splits
            ->groupBy(fn ($split) => $split->user->name ?? 'Empresa')
            ->map(fn ($splits) => [
                'total' => $splits->sum('value'),
                'paid' => $splits->where('paid', true)->sum('value'),
            ])
            ->sortByDesc('total');

        return view('admin.commissions.index', compact('events', 'summary', 'byUser', 'month', 'year'));
    }

    public function approve(CommissionEvent $event)
    {
        $event->update(['status' => 'APPROVED', 'approved_by' => Auth::id(), 'approved_at' => now()]);

        AuditLogger::log('commission.approved', $event, null, null, $event->total_commission_value);

        return back()->with('success', 'Comissão aprovada.');
    }

    public function markSplitPaid(Request $request, CommissionSplit $split)
    {
        $split->update(['paid' => true]);

        CommissionPayment::create([
            'commission_split_id' => $split->id,
            'amount' => $split->value,
            'paid_at' => now(),
            'payment_method' => $request->input('payment_method', 'transferência'),
            'recorded_by' => Auth::id(),
        ]);

        if ($split->event->splits()->where('paid', false)->doesntExist()) {
            $split->event->update(['status' => 'PAID']);
        }

        return back()->with('success', 'Pagamento registrado.');
    }

    public function export(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $events = CommissionEvent::query()
            ->whereHas('deal', fn ($q) => $q->where('organization_id', Auth::user()->organization_id))
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with(['deal.property', 'splits.user'])
            ->get();

        $filename = "comissoes-{$year}-{$month}.csv";

        $callback = function () use ($events) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Negócio', 'Beneficiário', 'Dimensão', 'Percentual', 'Valor', 'Pago?']);

            foreach ($events as $event) {
                foreach ($event->splits as $split) {
                    fputcsv($handle, [
                        $event->deal->property->title ?? '—',
                        $split->user->name ?? 'Empresa',
                        $split->dimensionLabel(),
                        $split->percentage,
                        number_format((float) $split->value, 2, ',', '.'),
                        $split->paid ? 'Sim' : 'Não',
                    ]);
                }
            }

            fclose($handle);
        };

        return Response::streamDownload($callback, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
