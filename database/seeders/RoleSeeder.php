<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'properties.view', 'properties.manage',
            'leads.view', 'leads.manage',
            'visits.view', 'visits.manage',
            'users.manage',
            'settings.manage',
            'finance.view', 'finance.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permissions);

        $corretor = Role::firstOrCreate(['name' => 'corretor']);
        $corretor->syncPermissions(['properties.view', 'properties.manage', 'leads.view', 'leads.manage', 'visits.view', 'visits.manage']);

        $financeiro = Role::firstOrCreate(['name' => 'financeiro']);
        $financeiro->syncPermissions(['leads.view', 'finance.view', 'finance.manage']);

        Role::firstOrCreate(['name' => 'cliente']);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@novaimoveis.com.br'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('senha123'),
                'active' => true,
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('admin');
    }
}
