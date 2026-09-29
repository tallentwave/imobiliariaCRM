<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealParty extends Model
{
    protected $guarded = [];

    public const ROLES = [
        'BUYER' => 'Comprador',
        'SELLER' => 'Vendedor',
        'BUYER_REPRESENTATIVE' => 'Representante do comprador',
        'SELLER_REPRESENTATIVE' => 'Representante do vendedor',
        'WITNESS' => 'Testemunha',
        'OTHER' => 'Outro',
    ];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }
}
