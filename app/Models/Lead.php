<?php

namespace App\Models;

use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'first_response_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'next_activity_at' => 'datetime',
        'sla_due_at' => 'datetime',
        'escalated_at' => 'datetime',
        'redistributed_at' => 'datetime',
        'sla_milestones_notified' => 'array',
    ];

    public const STAGES = [
        'NEW' => 'Novo',
        'ATTEMPTING_CONTACT' => 'Tentando contato',
        'CONTACTED' => 'Contatado',
        'QUALIFYING' => 'Qualificando',
        'QUALIFIED' => 'Qualificado',
        'OPPORTUNITY' => 'Virou oportunidade',
        'NURTURE' => 'Nutrição',
        'LOST' => 'Perdido',
        'SPAM' => 'Spam',
        'DUPLICATE' => 'Duplicado',
        'INVALID' => 'Inválido',
    ];

    public const OPEN_STAGES = ['NEW', 'ATTEMPTING_CONTACT', 'CONTACTED', 'QUALIFYING', 'QUALIFIED'];

    public const LOST_STAGES = ['LOST', 'SPAM', 'DUPLICATE', 'INVALID'];

    public const SLA_MINUTES = [
        'T+3' => 3,
        'T+5' => 5,
        'T+10' => 10,
        'T+15' => 15,
    ];

    protected static function booted(): void
    {
        static::creating(function (Lead $lead) {
            if (! $lead->sla_due_at) {
                $lead->sla_due_at = now()->addMinutes(15);
            }

            if (in_array($lead->stage, self::LOST_STAGES) && ! $lead->lost_reason) {
                throw new \InvalidArgumentException('Motivo de perda é obrigatório ao mover o lead para uma etapa de saída.');
            }
        });

        static::updating(function (Lead $lead) {
            if ($lead->isDirty('agent_id') && $lead->getOriginal('agent_id')) {
                AuditLogger::log('lead.transferred', $lead, 'agent_id', $lead->getOriginal('agent_id'), $lead->agent_id);
            }

            if ($lead->isDirty('stage') && in_array($lead->stage, self::LOST_STAGES) && ! $lead->lost_reason) {
                throw new \InvalidArgumentException('Motivo de perda é obrigatório ao mover o lead para uma etapa de saída.');
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function assignedTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'assigned_team_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function stageLabel(): string
    {
        return self::STAGES[$this->stage] ?? $this->stage;
    }

    public function isSlaOverdue(): bool
    {
        return $this->sla_due_at && $this->sla_due_at->isPast() && in_array($this->stage, self::OPEN_STAGES);
    }
}
