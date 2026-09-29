<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            'Piscina', 'Academia', 'Churrasqueira', 'Salão de festas', 'Playground',
            'Portaria 24h', 'Elevador', 'Varanda gourmet', 'Ar condicionado',
            'Armários planejados', 'Aceita pet', 'Mobiliado', 'Quintal', 'Garagem coberta',
            'Vista para o mar', 'Energia solar',
        ];

        foreach ($features as $name) {
            Feature::firstOrCreate(['name' => $name]);
        }
    }
}
