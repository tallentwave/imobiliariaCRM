<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'featured' => 'boolean',
        'show_exact_address' => 'boolean',
        'published_at' => 'datetime',
        'price' => 'decimal:2',
        'condo_fee' => 'decimal:2',
        'iptu' => 'decimal:2',
        'area_total' => 'decimal:2',
        'area_built' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    protected static function booted(): void
    {
        static::creating(function (Property $property) {
            if (! $property->slug) {
                $property->slug = static::uniqueSlug($property->title);
            }

            if (! $property->reference_code) {
                $property->reference_code = 'IMV-'.strtoupper(Str::random(6));
            }

            if (! $property->published_at && $property->status === 'disponivel') {
                $property->published_at = now();
            }
        });
    }

    public static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('order');
    }

    public function cover()
    {
        return $this->images()->where('is_cover', true)->first() ?? $this->images()->first();
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function scopePublished($query)
    {
        return $query->whereIn('status', ['disponivel', 'reservado']);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['purpose'] ?? null, fn ($q, $v) => $q->where('purpose', $v))
            ->when($filters['type'] ?? null, fn ($q, $v) => $q->where('type', $v))
            ->when($filters['city'] ?? null, fn ($q, $v) => $q->where('city', 'like', "%{$v}%"))
            ->when($filters['neighborhood'] ?? null, fn ($q, $v) => $q->where('neighborhood', 'like', "%{$v}%"))
            ->when($filters['bedrooms'] ?? null, fn ($q, $v) => $q->where('bedrooms', '>=', $v))
            ->when($filters['parking_spots'] ?? null, fn ($q, $v) => $q->where('parking_spots', '>=', $v))
            ->when($filters['price_min'] ?? null, fn ($q, $v) => $q->where('price', '>=', $v))
            ->when($filters['price_max'] ?? null, fn ($q, $v) => $q->where('price', '<=', $v))
            ->when($filters['q'] ?? null, function ($q, $v) {
                $q->where(function ($q) use ($v) {
                    $q->where('title', 'like', "%{$v}%")
                        ->orWhere('neighborhood', 'like', "%{$v}%")
                        ->orWhere('city', 'like', "%{$v}%")
                        ->orWhere('reference_code', 'like', "%{$v}%");
                });
            });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function purposeLabel(): string
    {
        return match ($this->purpose) {
            'venda' => 'Venda',
            'aluguel' => 'Aluguel',
            'temporada' => 'Temporada',
            default => $this->purpose,
        };
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'apartamento' => 'Apartamento',
            'casa' => 'Casa',
            'casa_condominio' => 'Casa em Condomínio',
            'cobertura' => 'Cobertura',
            'terreno' => 'Terreno',
            'comercial' => 'Comercial',
            'sala' => 'Sala Comercial',
            'galpao' => 'Galpão',
            'rural' => 'Imóvel Rural',
            default => 'Outro',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'disponivel' => 'Disponível',
            'reservado' => 'Reservado',
            'vendido' => 'Vendido',
            'alugado' => 'Alugado',
            'inativo' => 'Inativo',
            default => $this->status,
        };
    }
}
