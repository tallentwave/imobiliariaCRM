<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('leads.view');
    }

    public function view(User $user, Lead $lead): bool
    {
        if (! $user->can('leads.view')) {
            return false;
        }

        return $user->hasRole('admin') || $user->hasRole('financeiro') || $lead->agent_id === $user->id;
    }

    public function update(User $user, Lead $lead): bool
    {
        if (! $user->can('leads.manage')) {
            return false;
        }

        return $user->hasRole('admin') || $lead->agent_id === $user->id;
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->hasRole('admin');
    }
}
