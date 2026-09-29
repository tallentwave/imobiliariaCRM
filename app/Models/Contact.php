<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $guarded = [];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(ContactAddress::class);
    }

    public function relationships(): HasMany
    {
        return $this->hasMany(ContactRelationship::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function ownedProperties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'property_owners')
            ->withPivot(['ownership_percentage', 'primary_contact', 'authorization_status'])
            ->withTimestamps();
    }

    public function consents(): HasMany
    {
        return $this->hasMany(Consent::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function displayName(): string
    {
        return $this->type === 'COMPANY' ? ($this->legal_name ?? $this->full_name) : $this->full_name;
    }

    public function timeline()
    {
        return collect()
            ->concat($this->leads()->with('property')->get()->map(fn ($l) => [
                'type' => 'lead', 'at' => $l->created_at, 'model' => $l,
            ]))
            ->concat($this->opportunities()->get()->map(fn ($o) => [
                'type' => 'opportunity', 'at' => $o->created_at, 'model' => $o,
            ]))
            ->sortByDesc('at')
            ->values();
    }
}
