<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proposal extends Model
{
    protected $guarded = [];

    protected $casts = [
        'valid_until' => 'datetime',
    ];

    public const STATUSES = [
        'DRAFT' => 'Rascunho',
        'PRESENTED' => 'Apresentada',
        'COUNTERED' => 'Contraproposta recebida',
        'ACCEPTED' => 'Aceita',
        'REJECTED' => 'Rejeitada',
        'EXPIRED' => 'Expirada',
    ];

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function buyerContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'buyer_contact_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'parent_proposal_id');
    }

    public function counters(): HasMany
    {
        return $this->hasMany(Proposal::class, 'parent_proposal_id');
    }

    public function deal(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    /**
     * Cria uma nova versão (contraproposta) — nunca edita a proposta original,
     * preservando o histórico imutável exigido pela especificação.
     */
    public function createCounter(array $data): self
    {
        $this->update(['status' => 'COUNTERED']);

        return self::create([
            ...$data,
            'opportunity_id' => $this->opportunity_id,
            'property_id' => $this->property_id,
            'buyer_contact_id' => $this->buyer_contact_id,
            'parent_proposal_id' => $this->id,
            'version' => $this->version + 1,
            'status' => 'PRESENTED',
        ]);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast();
    }
}
