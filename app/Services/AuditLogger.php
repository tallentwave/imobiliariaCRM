<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public static function log(
        string $event,
        ?Model $auditable = null,
        ?string $field = null,
        mixed $oldValue = null,
        mixed $newValue = null,
        ?string $justification = null,
    ): AuditLog {
        return AuditLog::create([
            'organization_id' => Auth::user()->organization_id ?? null,
            'user_id' => Auth::id(),
            'event' => $event,
            'auditable_type' => $auditable ? $auditable->getMorphClass() : null,
            'auditable_id' => $auditable?->getKey(),
            'field' => $field,
            'old_value' => is_scalar($oldValue) ? (string) $oldValue : json_encode($oldValue),
            'new_value' => is_scalar($newValue) ? (string) $newValue : json_encode($newValue),
            'justification' => $justification,
            'ip_address' => Request::ip(),
        ]);
    }
}
