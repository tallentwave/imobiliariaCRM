<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionRule extends Model
{
    protected $guarded = [];

    public const DIMENSIONS = [
        'CAP' => 'Captador',
        'BUY' => 'Corretor comprador',
        'REF' => 'Indicação',
        'CORP' => 'Corporativo',
        'TEAM' => 'Equipe',
        'LEADER' => 'Líder de equipe',
        'UNIT' => 'Unidade',
        'COMPANY' => 'Empresa',
        'PARTNER' => 'Parceiro externo',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(CommissionPlan::class, 'commission_plan_id');
    }

    public function dimensionLabel(): string
    {
        return self::DIMENSIONS[$this->dimension] ?? $this->dimension;
    }
}
