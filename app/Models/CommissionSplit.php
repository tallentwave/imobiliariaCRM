<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommissionSplit extends Model
{
    protected $guarded = [];

    protected $casts = [
        'paid' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(CommissionEvent::class, 'commission_event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CommissionPayment::class);
    }

    public function dimensionLabel(): string
    {
        return CommissionRule::DIMENSIONS[$this->dimension] ?? $this->dimension;
    }
}
