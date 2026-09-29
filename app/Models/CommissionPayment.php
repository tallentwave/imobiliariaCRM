<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionPayment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'paid_at' => 'date',
    ];

    public function split(): BelongsTo
    {
        return $this->belongsTo(CommissionSplit::class, 'commission_split_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
