<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommissionEvent extends Model
{
    protected $guarded = [];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public const STATUSES = [
        'PENDING' => 'Pendente',
        'APPROVED' => 'Aprovada',
        'PAID' => 'Paga',
        'CANCELLED' => 'Cancelada',
    ];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(CommissionPlan::class, 'commission_plan_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function splits(): HasMany
    {
        return $this->hasMany(CommissionSplit::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function paidValue(): float
    {
        return (float) $this->splits()->where('paid', true)->sum('value');
    }

    public function pendingValue(): float
    {
        return (float) $this->splits()->where('paid', false)->sum('value');
    }
}
