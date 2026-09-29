<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->numerify('(##) 9####-####'),
            'message' => $this->faker->sentence(12),
            'source' => $this->faker->randomElement(['site', 'whatsapp', 'indicacao', 'portal']),
            'stage' => $this->faker->randomElement(array_keys(\App\Models\Lead::STAGES)),
        ];
    }
}
