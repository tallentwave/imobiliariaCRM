<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'negotiated_value' => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'commission_paid' => 'boolean',
        'closed_at' => 'datetime',
    ];

    public const STAGES = [
        'novo' => 'Novo',
        'em_contato' => 'Em contato',
        'visita_agendada' => 'Visita agendada',
        'proposta' => 'Proposta',
        'fechado_ganho' => 'Fechado (ganho)',
        'fechado_perdido' => 'Fechado (perdido)',
    ];

    protected static function booted(): void
    {
        static::saving(function (Lead $lead) {
            if ($lead->isDirty('stage') && in_array($lead->stage, ['fechado_ganho', 'fechado_perdido'])) {
                $lead->closed_at = $lead->closed_at ?? now();
            }

            if ($lead->negotiated_value && $lead->commission_percent && ! $lead->isDirty('commission_value')) {
                $lead->commission_value = round($lead->negotiated_value * $lead->commission_percent / 100, 2);
            }
        });
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function stageLabel(): string
    {
        return self::STAGES[$this->stage] ?? $this->stage;
    }
}
