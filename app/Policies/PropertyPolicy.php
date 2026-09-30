<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('properties.view');
    }

    public function view(User $user, Property $property): bool
    {
        return $user->can('properties.view');
    }

    public function create(User $user): bool
    {
        return $user->can('properties.manage');
    }

    public function update(User $user, Property $property): bool
    {
        if (! $user->can('properties.manage')) {
            return false;
        }

        return $user->hasRole('admin')
            || $property->agent_id === $user->id
            || $property->created_by === $user->id
            || $property->listingAgreements()->where('captor_user_id', $user->id)->exists();
    }

    public function delete(User $user, Property $property): bool
    {
        return $this->update($user, $property);
    }
}
