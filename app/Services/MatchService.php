<?php

namespace App\Services;

use App\Models\Opportunity;
use App\Models\Property;
use Illuminate\Support\Collection;

class MatchService
{
    public const WEIGHTS = [
        'price' => 25,
        'location' => 20,
        'type' => 15,
        'bedrooms' => 10,
        'area' => 10,
        'parking' => 5,
        'features' => 10,
        'preferences' => 5,
    ];

    /**
     * Calcula o score explicável (0-100) entre uma oportunidade e um imóvel.
     *
     * @return array{score: int, breakdown: array<string, array{weight: int, earned: float, reason: string}>}
     */
    public function score(Opportunity $opportunity, Property $property): array
    {
        $requirements = $opportunity->requirements instanceof Collection
            ? $opportunity->requirements
            : $opportunity->requirements()->get();

        $breakdown = [
            'price' => $this->scorePrice($opportunity, $property),
            'location' => $this->scoreLocation($requirements, $property),
            'type' => $this->scoreType($requirements, $property),
            'bedrooms' => $this->scoreBedrooms($requirements, $property),
            'area' => $this->scoreArea($requirements, $property),
            'parking' => $this->scoreParking($requirements, $property),
            'features' => $this->scoreFeatures($requirements, $property),
            'preferences' => $this->scorePreferences($opportunity, $property),
        ];

        $total = (int) round(collect($breakdown)->sum('earned'));

        return ['score' => min(100, max(0, $total)), 'breakdown' => $breakdown];
    }

    /** @return Collection<int, array{opportunity: Opportunity, score: int, breakdown: array}> */
    public function topMatchesForProperty(Property $property, int $limit = 10): Collection
    {
        return Opportunity::where('organization_id', $property->organization_id)
            ->where('status', 'OPEN')
            ->with(['contact', 'requirements'])
            ->get()
            ->map(fn (Opportunity $opportunity) => [
                'opportunity' => $opportunity,
                ...$this->score($opportunity, $property),
            ])
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    /** @return Collection<int, array{property: Property, score: int, breakdown: array}> */
    public function topMatchesForOpportunity(Opportunity $opportunity, int $limit = 10): Collection
    {
        return Property::published()
            ->where('organization_id', $opportunity->organization_id)
            ->with(['images', 'features'])
            ->get()
            ->map(fn (Property $property) => [
                'property' => $property,
                ...$this->score($opportunity, $property),
            ])
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    private function scorePrice(Opportunity $opportunity, Property $property): array
    {
        $weight = self::WEIGHTS['price'];

        if (! $property->price || (! $opportunity->budget_min && ! $opportunity->budget_max)) {
            return ['weight' => $weight, 'earned' => $weight * 0.5, 'reason' => 'Orçamento não informado'];
        }

        $price = (float) $property->price;
        $min = (float) ($opportunity->budget_min ?? 0);
        $max = (float) ($opportunity->budget_max ?? $price);

        if ($price >= $min && $price <= $max) {
            return ['weight' => $weight, 'earned' => $weight, 'reason' => 'Dentro do orçamento'];
        }

        $reference = $price > $max ? $max : $min;
        $diffPercent = $reference > 0 ? abs($price - $reference) / $reference : 1;
        $earned = max(0, $weight * (1 - min(1, $diffPercent * 2)));

        return ['weight' => $weight, 'earned' => $earned, 'reason' => $price > $max ? 'Acima do orçamento' : 'Abaixo do orçamento'];
    }

    private function scoreLocation(Collection $requirements, Property $property): array
    {
        $weight = self::WEIGHTS['location'];
        $neighborhood = $requirements->firstWhere('feature_key', 'neighborhood');
        $city = $requirements->firstWhere('feature_key', 'city');

        if ($neighborhood && strcasecmp($neighborhood->feature_value, (string) $property->neighborhood) === 0) {
            return ['weight' => $weight, 'earned' => $weight, 'reason' => 'Bairro desejado'];
        }

        if ($city && strcasecmp($city->feature_value, (string) $property->city) === 0) {
            return ['weight' => $weight, 'earned' => $weight * 0.7, 'reason' => 'Mesma cidade'];
        }

        if (! $neighborhood && ! $city) {
            return ['weight' => $weight, 'earned' => $weight * 0.5, 'reason' => 'Localização não informada'];
        }

        return ['weight' => $weight, 'earned' => 0, 'reason' => 'Fora da região desejada'];
    }

    private function scoreType(Collection $requirements, Property $property): array
    {
        $weight = self::WEIGHTS['type'];
        $type = $requirements->firstWhere('feature_key', 'type');

        if (! $type) {
            return ['weight' => $weight, 'earned' => $weight * 0.5, 'reason' => 'Tipo não informado'];
        }

        $match = $type->feature_value === $property->type;

        return ['weight' => $weight, 'earned' => $match ? $weight : 0, 'reason' => $match ? 'Tipologia compatível' : 'Tipologia diferente'];
    }

    private function scoreBedrooms(Collection $requirements, Property $property): array
    {
        $weight = self::WEIGHTS['bedrooms'];
        $req = $requirements->firstWhere('feature_key', 'bedrooms');

        if (! $req) {
            return ['weight' => $weight, 'earned' => $weight * 0.5, 'reason' => 'Não informado'];
        }

        $desired = (int) $req->feature_value;
        $has = (int) $property->bedrooms;

        if ($has >= $desired) {
            return ['weight' => $weight, 'earned' => $weight, 'reason' => "{$has} dormitório(s) atende"];
        }

        $earned = max(0, $weight * (1 - (($desired - $has) / max(1, $desired))));

        return ['weight' => $weight, 'earned' => $earned, 'reason' => "{$has} de {$desired} dormitórios desejados"];
    }

    private function scoreArea(Collection $requirements, Property $property): array
    {
        $weight = self::WEIGHTS['area'];
        $req = $requirements->firstWhere('feature_key', 'area_min');

        if (! $req || ! $property->area_total) {
            return ['weight' => $weight, 'earned' => $weight * 0.5, 'reason' => 'Não informado'];
        }

        $desired = (float) $req->feature_value;
        $has = (float) $property->area_total;

        if ($has >= $desired) {
            return ['weight' => $weight, 'earned' => $weight, 'reason' => 'Área atende'];
        }

        $earned = max(0, $weight * ($has / max(1, $desired)));

        return ['weight' => $weight, 'earned' => $earned, 'reason' => 'Área abaixo do desejado'];
    }

    private function scoreParking(Collection $requirements, Property $property): array
    {
        $weight = self::WEIGHTS['parking'];
        $req = $requirements->firstWhere('feature_key', 'parking_spots');

        if (! $req) {
            return ['weight' => $weight, 'earned' => $weight * 0.5, 'reason' => 'Não informado'];
        }

        $match = (int) $property->parking_spots >= (int) $req->feature_value;

        return ['weight' => $weight, 'earned' => $match ? $weight : 0, 'reason' => $match ? 'Vagas suficientes' : 'Vagas insuficientes'];
    }

    private function scoreFeatures(Collection $requirements, Property $property): array
    {
        $weight = self::WEIGHTS['features'];
        $desired = $requirements->where('feature_key', 'feature')->pluck('feature_value');

        if ($desired->isEmpty()) {
            return ['weight' => $weight, 'earned' => $weight * 0.5, 'reason' => 'Não informado'];
        }

        $propertyFeatures = $property->relationLoaded('features')
            ? $property->features->pluck('name')
            : $property->features()->pluck('name');

        $matched = $desired->filter(fn ($name) => $propertyFeatures->contains($name))->count();
        $ratio = $matched / max(1, $desired->count());

        return ['weight' => $weight, 'earned' => $weight * $ratio, 'reason' => "{$matched}/{$desired->count()} características"];
    }

    private function scorePreferences(Opportunity $opportunity, Property $property): array
    {
        $weight = self::WEIGHTS['preferences'];

        $purposeMap = ['MORAR' => 'venda', 'INVESTIR' => 'venda', 'RENDA' => 'aluguel', 'COMERCIAL' => 'venda'];
        $expected = $purposeMap[$opportunity->purpose] ?? null;

        if (! $expected) {
            return ['weight' => $weight, 'earned' => $weight * 0.5, 'reason' => 'Preferência genérica'];
        }

        $match = $property->purpose === $expected;

        return ['weight' => $weight, 'earned' => $match ? $weight : $weight * 0.3, 'reason' => $match ? 'Finalidade compatível' : 'Finalidade diferente'];
    }
}
