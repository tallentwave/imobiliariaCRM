<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Lead;
use App\Models\Property;
use App\Models\Setting;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        Setting::query()->firstOrCreate(['id' => 1], [
            'site_name' => 'Nova Imóveis',
            'slogan' => 'O imóvel certo para o seu próximo capítulo.',
            'phone' => '(11) 4000-1234',
            'whatsapp_number' => '5511999998888',
            'email' => 'contato@novaimoveis.com.br',
            'address' => 'Av. Paulista, 1000',
            'city' => 'São Paulo',
            'state' => 'SP',
            'creci_company' => 'CRECI-J 12345',
            'about_text' => "Há mais de 10 anos ajudando famílias a encontrar o imóvel ideal.\nNossa equipe de corretores especializados oferece atendimento personalizado do início ao fim do processo.",
        ]);
        $agents = collect([
            ['name' => 'Carla Mendes', 'email' => 'carla@novaimoveis.com.br'],
            ['name' => 'Rafael Souza', 'email' => 'rafael@novaimoveis.com.br'],
        ])->map(function ($data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('senha123'),
                    'phone' => '(11) 9'.rand(1000, 9999).'-'.rand(1000, 9999),
                    'creci' => 'CRECI-'.rand(10000, 99999),
                    'active' => true,
                    'default_commission_percent' => 5,
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('corretor');

            return $user;
        });

        $financeiro = User::firstOrCreate(
            ['email' => 'financeiro@novaimoveis.com.br'],
            [
                'name' => 'Ana Financeiro',
                'password' => bcrypt('senha123'),
                'active' => true,
                'email_verified_at' => now(),
            ]
        );
        $financeiro->assignRole('financeiro');

        $cliente = User::firstOrCreate(
            ['email' => 'cliente@example.com'],
            [
                'name' => 'João Cliente',
                'password' => bcrypt('senha123'),
                'active' => true,
                'email_verified_at' => now(),
            ]
        );
        $cliente->assignRole('cliente');

        $features = Feature::all();

        Property::factory()
            ->count(24)
            ->make()
            ->each(function (Property $property) use ($agents, $features) {
                $property->agent_id = $agents->random()->id;
                $property->created_by = $agents->random()->id;
                $property->save();

                $property->features()->sync(
                    $features->random(rand(3, 6))->pluck('id')
                );

                $seed = $property->id + 20;
                foreach (range(1, 4) as $i) {
                    $property->images()->create([
                        'path' => "https://picsum.photos/seed/imovel{$seed}{$i}/1200/800",
                        'order' => $i,
                        'is_cover' => $i === 1,
                    ]);
                }
            });

        $properties = Property::all();

        Lead::factory()
            ->count(30)
            ->make()
            ->each(function (Lead $lead) use ($agents, $properties) {
                $lead->agent_id = $agents->random()->id;
                $lead->property_id = $properties->random()->id;

                if (in_array($lead->stage, ['fechado_ganho', 'fechado_perdido'])) {
                    $lead->closed_at = now()->subDays(rand(1, 60));

                    if ($lead->stage === 'fechado_ganho') {
                        $property = $properties->firstWhere('id', $lead->property_id);
                        $lead->negotiated_value = $property->price;
                        $lead->commission_percent = 5;
                    } else {
                        $lead->lost_reason = collect(['Preço', 'Comprou outro imóvel', 'Desistiu', 'Sem resposta'])->random();
                    }
                }

                $lead->save();

                if (in_array($lead->stage, ['visita_agendada', 'proposta', 'fechado_ganho'])) {
                    Visit::create([
                        'lead_id' => $lead->id,
                        'property_id' => $lead->property_id,
                        'agent_id' => $lead->agent_id,
                        'scheduled_at' => now()->addDays(rand(-10, 10)),
                        'status' => $lead->stage === 'fechado_ganho' ? 'realizada' : 'agendada',
                    ]);
                }
            });
    }
}
