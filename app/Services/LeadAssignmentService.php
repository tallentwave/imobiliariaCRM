<?php

namespace App\Services;

use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Carbon;

class LeadAssignmentService
{
    /**
     * Decide qual corretor deve receber um novo lead.
     *
     * Ordem de prioridade:
     * 1. Corretor responsável pelo imóvel de origem (se houver e estiver ativo).
     * 2. Corretor ativo com menos leads recebidos nas últimas 24h (balanceamento de carga).
     * 3. Round-robin simples entre corretores ativos, se houver empate.
     */
    public function pickAgent(?Property $property = null): ?User
    {
        if ($property?->agent_id) {
            $propertyAgent = User::role('corretor')->where('id', $property->agent_id)->where('active', true)->first();

            if ($propertyAgent) {
                return $propertyAgent;
            }
        }

        return $this->leastLoadedAgent();
    }

    public function leastLoadedAgent(?int $excludeUserId = null): ?User
    {
        $since = Carbon::now()->subDay();

        return User::role('corretor')
            ->where('active', true)
            ->when($excludeUserId, fn ($q, $id) => $q->where('id', '!=', $id))
            ->withCount(['leads as recent_leads_count' => fn ($q) => $q->where('created_at', '>=', $since)])
            ->orderBy('recent_leads_count')
            ->orderBy('id')
            ->first();
    }
}
