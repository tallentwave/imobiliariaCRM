<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivacyRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public const TYPES = [
        'ACCESS' => 'Acesso aos dados',
        'DELETION' => 'Exclusão dos dados',
        'CORRECTION' => 'Correção de dados',
        'PORTABILITY' => 'Portabilidade',
    ];

    public const STATUSES = [
        'RECEIVED' => 'Recebida',
        'IDENTITY_VERIFICATION' => 'Verificando identidade',
        'ANALYSIS' => 'Em análise',
        'IN_PROGRESS' => 'Em andamento',
        'ANSWERED' => 'Respondida',
        'CLOSED' => 'Concluída',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
