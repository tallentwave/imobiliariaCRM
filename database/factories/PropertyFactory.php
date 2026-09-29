<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    private array $cities = [
        ['city' => 'São Paulo', 'state' => 'SP', 'neighborhoods' => ['Moema', 'Pinheiros', 'Vila Mariana', 'Itaim Bibi', 'Tatuapé']],
        ['city' => 'Rio de Janeiro', 'state' => 'RJ', 'neighborhoods' => ['Copacabana', 'Barra da Tijuca', 'Tijuca', 'Botafogo']],
        ['city' => 'Curitiba', 'state' => 'PR', 'neighborhoods' => ['Batel', 'Água Verde', 'Centro Cívico']],
        ['city' => 'Florianópolis', 'state' => 'SC', 'neighborhoods' => ['Jurerê', 'Lagoa da Conceição', 'Centro']],
    ];

    public function definition(): array
    {
        $location = $this->faker->randomElement($this->cities);
        $type = $this->faker->randomElement(['apartamento', 'casa', 'casa_condominio', 'cobertura', 'terreno', 'comercial']);
        $purpose = $this->faker->randomElement(['venda', 'venda', 'aluguel', 'aluguel', 'temporada']);
        $bedrooms = $type === 'terreno' ? 0 : $this->faker->numberBetween(1, 5);
        $areaTotal = $this->faker->numberBetween(35, 450);

        $title = match ($type) {
            'apartamento' => "Apartamento com {$bedrooms} quartos em {$location['neighborhoods'][array_rand($location['neighborhoods'])]}",
            'casa' => "Casa com {$bedrooms} quartos em {$location['neighborhoods'][array_rand($location['neighborhoods'])]}",
            'casa_condominio' => 'Casa em condomínio fechado',
            'cobertura' => 'Cobertura duplex com vista panorâmica',
            'terreno' => "Terreno de {$areaTotal}m² pronto para construir",
            'comercial' => 'Excelente sala comercial',
            default => 'Imóvel disponível',
        };

        return [
            'title' => $title,
            'description' => $this->faker->paragraphs(3, true),
            'purpose' => $purpose,
            'type' => $type,
            'price' => $purpose === 'aluguel'
                ? $this->faker->numberBetween(1200, 8000)
                : $this->faker->numberBetween(180000, 2500000),
            'condo_fee' => $type === 'terreno' ? null : $this->faker->numberBetween(200, 1800),
            'iptu' => $this->faker->numberBetween(50, 900),
            'bedrooms' => $bedrooms,
            'suites' => min($bedrooms, $this->faker->numberBetween(0, 2)),
            'bathrooms' => max(1, $bedrooms),
            'parking_spots' => $this->faker->numberBetween(0, 4),
            'area_total' => $areaTotal,
            'area_built' => $type === 'terreno' ? null : $areaTotal * 0.8,
            'zipcode' => $this->faker->numerify('#####-###'),
            'address' => $this->faker->streetName(),
            'number' => (string) $this->faker->numberBetween(10, 2000),
            'neighborhood' => $this->faker->randomElement($location['neighborhoods']),
            'city' => $location['city'],
            'state' => $location['state'],
            'latitude' => $this->faker->latitude(-27, -22),
            'longitude' => $this->faker->longitude(-49, -43),
            'status' => 'disponivel',
            'featured' => $this->faker->boolean(25),
            'published_at' => now(),
        ];
    }
}
