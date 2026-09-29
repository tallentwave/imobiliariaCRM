<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opportunity extends Model
{
    protected $guarded = [];

    protected $casts = [
        'financing_required' => 'boolean',
        'fgts' => 'boolean',
        'trade_in' => 'boolean',
        'timeline' => 'date',
        'closed_at' => 'datetime',
    ];

    public const PURPOSES = [
        'MORAR' => 'Morar',
        'INVESTIR' => 'Investir',
        'RENDA' => 'Renda',
        'COMERCIAL' => 'Comercial',
    ];

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

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(OpportunityRequirement::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function purposeLabel(): string
    {
        return self::PURPOSES[$this->purpose] ?? $this->purpose;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'OPEN' => 'Aberta',
            'WON' => 'Ganha',
            'LOST' => 'Perdida',
            default => $this->status,
        };
    }
}
