<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingAgreement extends Model
{
    protected $guarded = [];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'authorizations' => 'array',
    ];

    public const STAGES = [
        'PROSPECT' => 'Prospecção',
        'CONTACTED' => 'Contatado',
        'MEETING' => 'Reunião',
        'VALUATION' => 'Avaliação',
        'PROPOSAL' => 'Proposta de captação',
        'CONTRACT' => 'Contrato',
        'ONBOARDING' => 'Onboarding',
        'ACTIVE' => 'Ativo',
        'EXPIRED' => 'Expirado',
        'CANCELLED' => 'Cancelado',
    ];

    public const TYPES = [
        'OPEN' => 'Aberta',
        'EXCLUSIVE' => 'Exclusiva',
        'SIGNATURE' => 'Assinatura (Premium)',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function captor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captor_user_id');
    }

    public function stageLabel(): string
    {
        return self::STAGES[$this->status] ?? $this->status;
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->listing_type] ?? $this->listing_type;
    }

    public function isExpired(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }
}
