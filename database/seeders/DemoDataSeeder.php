<?php

namespace Database\Seeders;

use App\Models\CommissionEvent;
use App\Models\CommissionPlan;
use App\Models\CommissionSplit;
use App\Models\Consent;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\DealChecklist;
use App\Models\DealParty;
use App\Models\Feature;
use App\Models\Lead;
use App\Models\ListingAgreement;
use App\Models\Opportunity;
use App\Models\Organization;
use App\Models\PrivacyRequest;
use App\Models\Property;
use App\Models\PropertyOwner;
use App\Models\Proposal;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::first();
        $unit = Unit::first();

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
            ['name' => 'Carla Mendes', 'email' => 'carla@novaimoveis.com.br', 'role' => 'corretor'],
            ['name' => 'Rafael Souza', 'email' => 'rafael@novaimoveis.com.br', 'role' => 'corretor'],
        ])->map(function ($data) use ($organization, $unit) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('senha123'),
                    'phone' => '(11) 9'.rand(1000, 9999).'-'.rand(1000, 9999),
                    'creci' => 'CRECI-'.rand(10000, 99999),
                    'organization_id' => $organization->id,
                    'unit_id' => $unit->id,
                    'active' => true,
                    'default_commission_percent' => 5,
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole($data['role']);

            return $user;
        });

        $captador = User::firstOrCreate(
            ['email' => 'captador@novaimoveis.com.br'],
            [
                'name' => 'Marcos Captador',
                'password' => bcrypt('senha123'),
                'phone' => '(11) 98888-0000',
                'creci' => 'CRECI-'.rand(10000, 99999),
                'organization_id' => $organization->id,
                'unit_id' => $unit->id,
                'active' => true,
                'email_verified_at' => now(),
            ]
        );
        $captador->assignRole('captador');

        $financeiro = User::firstOrCreate(
            ['email' => 'financeiro@novaimoveis.com.br'],
            [
                'name' => 'Ana Financeiro',
                'password' => bcrypt('senha123'),
                'organization_id' => $organization->id,
                'unit_id' => $unit->id,
                'active' => true,
                'email_verified_at' => now(),
            ]
        );
        $financeiro->assignRole('financeiro');

        $compliance = User::firstOrCreate(
            ['email' => 'compliance@novaimoveis.com.br'],
            [
                'name' => 'Paula Compliance',
                'password' => bcrypt('senha123'),
                'organization_id' => $organization->id,
                'unit_id' => $unit->id,
                'active' => true,
                'email_verified_at' => now(),
            ]
        );
        $compliance->assignRole('compliance');

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

        $clienteContact = Contact::firstOrCreate(
            ['organization_id' => $organization->id, 'email' => $cliente->email],
            [
                'unit_id' => $unit->id,
                'type' => 'PERSON',
                'full_name' => $cliente->name,
                'mobile' => '(11) 91234-5678',
                'owner_user_id' => $agents->random()->id,
                'user_id' => $cliente->id,
                'status' => 'ACTIVE',
            ]
        );
        Consent::firstOrCreate([
            'contact_id' => $clienteContact->id,
            'purpose' => 'marketing_email',
        ], [
            'channel' => 'site',
            'version' => '1.0',
            'granted_at' => now(),
        ]);

        $commissionPlan = CommissionPlan::firstOrCreate(
            ['organization_id' => $organization->id, 'name' => 'Plano padrão'],
            ['description' => 'Rateio padrão: captador, corretor comprador e empresa.', 'is_default' => true, 'active' => true]
        );

        if ($commissionPlan->rules()->count() === 0) {
            $commissionPlan->rules()->createMany([
                ['dimension' => 'CAP', 'percentage' => 40, 'order' => 1],
                ['dimension' => 'BUY', 'percentage' => 40, 'order' => 2],
                ['dimension' => 'COMPANY', 'percentage' => 20, 'order' => 3],
            ]);
        }

        $features = Feature::all();

        // Contatos vendedores (proprietários) — um por imóvel
        $sellerNames = collect(range(1, 24))->map(fn ($i) => fake('pt_BR')->name());

        Property::factory()
            ->count(24)
            ->make()
            ->each(function (Property $property, $index) use ($agents, $captador, $features, $organization, $unit, $sellerNames) {
                $property->organization_id = $organization->id;
                $property->unit_id = $unit->id;
                $property->agent_id = $agents->random()->id;
                $property->created_by = $agents->random()->id;
                $property->minimum_authorized_price = $property->price ? $property->price * 0.9 : null;
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

                $seller = Contact::create([
                    'organization_id' => $organization->id,
                    'unit_id' => $unit->id,
                    'type' => 'PERSON',
                    'full_name' => $sellerNames[$index],
                    'mobile' => fake('pt_BR')->numerify('(##) 9####-####'),
                    'email' => fake()->unique()->safeEmail(),
                    'owner_user_id' => $captador->id,
                    'status' => 'ACTIVE',
                ]);

                PropertyOwner::create([
                    'property_id' => $property->id,
                    'contact_id' => $seller->id,
                    'ownership_percentage' => 100,
                    'primary_contact' => true,
                    'authorization_status' => 'AUTHORIZED',
                ]);

                ListingAgreement::create([
                    'property_id' => $property->id,
                    'captor_user_id' => $captador->id,
                    'listing_type' => fake()->randomElement(['OPEN', 'EXCLUSIVE', 'EXCLUSIVE', 'SIGNATURE']),
                    'starts_at' => now()->subDays(rand(10, 120)),
                    'ends_at' => now()->addDays(rand(30, 180)),
                    'commission_percent' => 5,
                    'authorizations' => ['publicidade' => true, 'fotografia' => true, 'placa' => fake()->boolean()],
                    'status' => 'ACTIVE',
                ]);
            });

        $properties = Property::all();

        // Contatos compradores (leads)
        $buyers = collect(range(1, 30))->map(function () use ($organization, $unit, $agents) {
            return Contact::create([
                'organization_id' => $organization->id,
                'unit_id' => $unit->id,
                'type' => 'PERSON',
                'full_name' => fake('pt_BR')->name(),
                'email' => fake()->unique()->safeEmail(),
                'mobile' => fake('pt_BR')->numerify('(##) 9####-####'),
                'owner_user_id' => $agents->random()->id,
                'status' => 'ACTIVE',
            ]);
        });

        $stages = array_keys(Lead::STAGES);

        $buyers->each(function (Contact $buyer) use ($agents, $properties, $stages) {
            $property = $properties->random();
            $stage = collect($stages)->random();
            $isLost = in_array($stage, Lead::LOST_STAGES);

            $lead = Lead::create([
                'contact_id' => $buyer->id,
                'organization_id' => $buyer->organization_id,
                'unit_id' => $buyer->unit_id,
                'name' => $buyer->full_name,
                'email' => $buyer->email,
                'phone' => $buyer->mobile,
                'message' => fake('pt_BR')->sentence(12),
                'source' => fake()->randomElement(['site', 'whatsapp', 'indicacao', 'portal']),
                'property_id' => $property->id,
                'agent_id' => $agents->random()->id,
                'stage' => $stage,
                'temperature' => fake()->randomElement(['COLD', 'WARM', 'HOT']),
                'lead_score' => rand(0, 100),
                'lost_reason' => $isLost ? collect(['Preço', 'Comprou outro imóvel', 'Desistiu', 'Sem resposta'])->random() : null,
                'first_response_at' => in_array($stage, ['NEW']) ? null : now()->subHours(rand(1, 48)),
                'last_activity_at' => now()->subDays(rand(0, 10)),
            ]);

            if ($stage === 'OPPORTUNITY' || rand(0, 1) === 1 && ! $isLost) {
                $opportunity = Opportunity::create([
                    'organization_id' => $lead->organization_id,
                    'unit_id' => $lead->unit_id,
                    'contact_id' => $buyer->id,
                    'lead_id' => $lead->id,
                    'assigned_user_id' => $lead->agent_id,
                    'purpose' => fake()->randomElement(array_keys(Opportunity::PURPOSES)),
                    'budget_min' => $property->price ? $property->price * 0.8 : null,
                    'budget_max' => $property->price ? $property->price * 1.15 : null,
                    'financing_required' => fake()->boolean(60),
                    'status' => 'OPEN',
                ]);

                Visit::create([
                    'lead_id' => $lead->id,
                    'opportunity_id' => $opportunity->id,
                    'contact_id' => $buyer->id,
                    'property_id' => $property->id,
                    'agent_id' => $lead->agent_id,
                    'scheduled_at' => now()->addDays(rand(-10, 10)),
                    'status' => fake()->randomElement(['agendada', 'realizada']),
                    'feedback_intent' => fake()->randomElement(['MUITO_INTERESSADO', 'INTERESSADO', 'NEUTRO']),
                ]);

                $proposal = Proposal::create([
                    'opportunity_id' => $opportunity->id,
                    'property_id' => $property->id,
                    'buyer_contact_id' => $buyer->id,
                    'created_by' => $lead->agent_id,
                    'version' => 1,
                    'price' => $property->price ? $property->price * 0.95 : 100000,
                    'down_payment' => $property->price ? $property->price * 0.2 : null,
                    'valid_until' => now()->addDays(10),
                    'status' => fake()->randomElement(['PRESENTED', 'COUNTERED', 'ACCEPTED']),
                ]);

                if ($proposal->status === 'ACCEPTED') {
                    $seller = $property->owners()->first();

                    $deal = Deal::create([
                        'organization_id' => $lead->organization_id,
                        'unit_id' => $lead->unit_id,
                        'property_id' => $property->id,
                        'opportunity_id' => $opportunity->id,
                        'proposal_id' => $proposal->id,
                        'buyer_contact_id' => $buyer->id,
                        'seller_contact_id' => $seller?->id,
                        'agent_user_id' => $lead->agent_id,
                        'captor_user_id' => $property->listingAgreements()->first()?->captor_user_id,
                        'value' => $proposal->price,
                        'status' => fake()->randomElement(['NEGOTIATION', 'CONTRACT', 'CLOSED_WON']),
                    ]);

                    DealParty::create(['deal_id' => $deal->id, 'contact_id' => $buyer->id, 'role' => 'BUYER']);
                    if ($seller) {
                        DealParty::create(['deal_id' => $deal->id, 'contact_id' => $seller->id, 'role' => 'SELLER']);
                    }

                    foreach (['Assinatura do contrato', 'Envio de documentação', 'Registro em cartório', 'Liberação de chaves'] as $i => $title) {
                        DealChecklist::create([
                            'deal_id' => $deal->id,
                            'title' => $title,
                            'status' => $deal->status === 'CLOSED_WON' || $i === 0 ? 'DONE' : 'PENDING',
                            'order' => $i,
                        ]);
                    }

                    if ($deal->status === 'CLOSED_WON') {
                        $opportunity->update(['status' => 'WON', 'closed_at' => now()]);
                        $property->update(['status' => $property->purpose === 'aluguel' ? 'alugado' : 'vendido']);

                        $grossValue = (float) $deal->value;
                        $commissionPercent = 5;
                        $totalCommission = round($grossValue * $commissionPercent / 100, 2);

                        $commissionPlan = \App\Models\CommissionPlan::where('is_default', true)->first();

                        $event = CommissionEvent::create([
                            'deal_id' => $deal->id,
                            'commission_plan_id' => $commissionPlan?->id,
                            'gross_value' => $grossValue,
                            'total_commission_percent' => $commissionPercent,
                            'total_commission_value' => $totalCommission,
                            'status' => fake()->randomElement(['PENDING', 'APPROVED', 'PAID']),
                        ]);

                        foreach ($commissionPlan->rules as $rule) {
                            $value = round($totalCommission * $rule->percentage / 100, 2);
                            $userId = match ($rule->dimension) {
                                'CAP' => $deal->captor_user_id,
                                'BUY' => $deal->agent_user_id,
                                default => null,
                            };

                            CommissionSplit::create([
                                'commission_event_id' => $event->id,
                                'user_id' => $userId,
                                'dimension' => $rule->dimension,
                                'percentage' => $rule->percentage,
                                'value' => $value,
                                'paid' => $event->status === 'PAID',
                            ]);
                        }
                    }
                }
            }
        });

        PrivacyRequest::create([
            'contact_id' => $buyers->first()->id,
            'requester_name' => $buyers->first()->full_name,
            'requester_email' => $buyers->first()->email,
            'type' => 'ACCESS',
            'status' => 'RECEIVED',
        ]);
    }
}
