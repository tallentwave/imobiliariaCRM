<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deal extends Model
{
    protected $guarded = [];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public const STATUSES = [
        'NEGOTIATION' => 'Negociação',
        'CONTRACT' => 'Contrato',
        'DOCUMENTATION' => 'Documentação',
        'CLOSED_WON' => 'Fechado (ganho)',
        'CLOSED_LOST' => 'Fechado (perdido)',
    ];

    protected static function booted(): void
    {
        static::creating(function (Deal $deal) {
            if ($deal->status === 'CLOSED_LOST' && ! $deal->lost_reason) {
                throw new \InvalidArgumentException('Motivo de perda é obrigatório ao fechar o negócio como perdido.');
            }

            if (in_array($deal->status, ['CLOSED_WON', 'CLOSED_LOST']) && ! $deal->closed_at) {
                $deal->closed_at = now();
            }
        });

        static::updating(function (Deal $deal) {
            if ($deal->isDirty('status') && $deal->status === 'CLOSED_LOST' && ! $deal->lost_reason) {
                throw new \InvalidArgumentException('Motivo de perda é obrigatório ao fechar o negócio como perdido.');
            }

            if ($deal->isDirty('status') && in_array($deal->status, ['CLOSED_WON', 'CLOSED_LOST']) && ! $deal->closed_at) {
                $deal->closed_at = now();
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

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function buyerContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'buyer_contact_id');
    }

    public function sellerContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'seller_contact_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_user_id');
    }

    public function captor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captor_user_id');
    }

    public function parties(): HasMany
    {
        return $this->hasMany(DealParty::class);
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(DealChecklist::class)->orderBy('order');
    }

    public function commissionEvent(): HasMany
    {
        return $this->hasMany(CommissionEvent::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function checklistProgress(): array
    {
        $total = $this->checklistItems->count();
        $done = $this->checklistItems->where('status', 'DONE')->count();

        return ['done' => $done, 'total' => $total];
    }
}
