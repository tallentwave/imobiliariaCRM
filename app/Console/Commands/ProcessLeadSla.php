<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\User;
use App\Notifications\LeadSlaAlert;
use App\Services\AuditLogger;
use App\Services\LeadAssignmentService;
use Illuminate\Console\Command;

class ProcessLeadSla extends Command
{
    protected $signature = 'app:process-lead-sla';

    protected $description = 'Processa marcos de SLA dos leads em aberto: lembrete (T+3/T+5), escalonamento (T+10) e redistribuição (T+15)';

    public function handle(LeadAssignmentService $assignmentService): int
    {
        $leads = Lead::query()
            ->whereIn('stage', Lead::OPEN_STAGES)
            ->whereNull('first_response_at')
            ->get();

        $processed = 0;

        foreach ($leads as $lead) {
            $minutes = $lead->created_at->diffInMinutes(now());
            $notified = $lead->sla_milestones_notified ?? [];

            foreach (Lead::SLA_MINUTES as $milestone => $threshold) {
                if ($minutes < $threshold || in_array($milestone, $notified, true)) {
                    continue;
                }

                match ($milestone) {
                    'T+3', 'T+5' => $this->remindAgent($lead),
                    'T+10' => $this->escalate($lead),
                    'T+15' => $this->redistribute($lead, $assignmentService),
                    default => null,
                };

                $notified[] = $milestone;
                $processed++;
            }

            if ($notified !== ($lead->sla_milestones_notified ?? [])) {
                $lead->timestamps = false;
                $lead->update(['sla_milestones_notified' => $notified]);
            }
        }

        $this->info("SLA processado: {$processed} marco(s) disparado(s) em ".$leads->count().' lead(s) em aberto.');

        return self::SUCCESS;
    }

    private function remindAgent(Lead $lead): void
    {
        if ($lead->agent) {
            $this->notifySafely($lead->agent, new LeadSlaAlert($lead, 'reminder'), $lead);
        }
    }

    private function escalate(Lead $lead): void
    {
        $lead->update(['escalated_at' => now()]);

        $leaders = User::role('admin')->get();

        foreach ($leaders as $leader) {
            $this->notifySafely($leader, new LeadSlaAlert($lead, 'escalation'), $lead);
        }

        AuditLogger::log('lead.sla_escalated', $lead, 'stage', null, $lead->stage, 'Sem primeira resposta em 10 minutos');
    }

    private function redistribute(Lead $lead, LeadAssignmentService $assignmentService): void
    {
        $newAgent = $assignmentService->leastLoadedAgent(excludeUserId: $lead->agent_id);

        if (! $newAgent || $newAgent->id === $lead->agent_id) {
            return;
        }

        $oldAgentId = $lead->agent_id;

        $lead->update([
            'agent_id' => $newAgent->id,
            'redistributed_at' => now(),
        ]);

        AuditLogger::log('lead.auto_redistributed', $lead, 'agent_id', $oldAgentId, $newAgent->id, 'Sem primeira resposta em 15 minutos (SLA)');

        $this->notifySafely($newAgent, new LeadSlaAlert($lead, 'redistributed'), $lead);
    }

    /**
     * Um e-mail que falha (SMTP fora do ar, mal configurado) nunca pode travar o
     * processamento dos demais leads em aberto no mesmo lote do cron.
     */
    private function notifySafely(User $user, $notification, Lead $lead): void
    {
        try {
            $user->notify($notification);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Falha ao enviar alerta de SLA do lead #'.$lead->id.' para o usuário #'.$user->id.': '.$e->getMessage());
        }
    }
}
