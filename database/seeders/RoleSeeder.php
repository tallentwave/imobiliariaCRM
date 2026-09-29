<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Team;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::firstOrCreate(
            ['slug' => 'nova-imoveis'],
            ['name' => 'Nova Imóveis', 'creci_company' => 'CRECI-J 12345']
        );

        $unit = Unit::firstOrCreate(
            ['organization_id' => $organization->id, 'slug' => 'matriz'],
            ['name' => 'Matriz', 'city' => 'São Paulo', 'state' => 'SP']
        );

        $team = Team::firstOrCreate(
            ['unit_id' => $unit->id, 'name' => 'Equipe Comercial']
        );

        $permissions = [
            'properties.view', 'properties.manage',
            'leads.view', 'leads.manage',
            'opportunities.view', 'opportunities.manage',
            'listings.view', 'listings.manage',
            'proposals.view', 'proposals.manage',
            'deals.view', 'deals.manage',
            'visits.view', 'visits.manage',
            'contacts.view', 'contacts.manage',
            'users.manage',
            'settings.manage',
            'finance.view', 'finance.manage',
            'commissions.approve',
            'audit.view',
            'privacy.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permissions);

        $corretor = Role::firstOrCreate(['name' => 'corretor']);
        $corretor->syncPermissions([
            'properties.view', 'properties.manage',
            'leads.view', 'leads.manage',
            'opportunities.view', 'opportunities.manage',
            'proposals.view', 'proposals.manage',
            'deals.view', 'deals.manage',
            'visits.view', 'visits.manage',
            'contacts.view', 'contacts.manage',
        ]);

        $captador = Role::firstOrCreate(['name' => 'captador']);
        $captador->syncPermissions([
            'properties.view', 'properties.manage',
            'listings.view', 'listings.manage',
            'contacts.view', 'contacts.manage',
        ]);

        $financeiro = Role::firstOrCreate(['name' => 'financeiro']);
        $financeiro->syncPermissions(['leads.view', 'deals.view', 'finance.view', 'finance.manage', 'commissions.approve']);

        $compliance = Role::firstOrCreate(['name' => 'compliance']);
        $compliance->syncPermissions(['contacts.view', 'audit.view', 'privacy.manage']);

        Role::firstOrCreate(['name' => 'cliente']);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@novaimoveis.com.br'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('senha123'),
                'organization_id' => $organization->id,
                'unit_id' => $unit->id,
                'team_id' => $team->id,
                'active' => true,
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('admin');
    }
}
