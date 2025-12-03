<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Illuminate\Cache\CacheManager::class]->forget('spatie.permission.cache');

        // Create Permissions
        $permissions = [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'kanban.view', 'kanban.create', 'kanban.move', 'kanban.delete',
            'chat.view', 'chat.send',
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::create(['name' => $permission, 'slug' => $permission]);
        }

        // Create Roles and Assign Permissions
        $admin = \App\Models\Role::create(['name' => 'Admin', 'slug' => 'admin', 'description' => 'Administrator']);
        $gestor = \App\Models\Role::create(['name' => 'Gestor', 'slug' => 'gestor', 'description' => 'Manager']);
        $vendedor = \App\Models\Role::create(['name' => 'Vendedor', 'slug' => 'vendedor', 'description' => 'Salesperson']);
        $suporte = \App\Models\Role::create(['name' => 'Suporte', 'slug' => 'suporte', 'description' => 'Support Agent']);

        // Admin gets all permissions
        $allPermissions = \App\Models\Permission::all();
        $admin->permissions()->attach($allPermissions);

        // Gestor
        $gestorPermissions = \App\Models\Permission::where('slug', 'like', 'users.%')
            ->orWhere('slug', 'like', 'kanban.%')
            ->orWhere('slug', 'like', 'chat.%')
            ->get();
        $gestor->permissions()->attach($gestorPermissions);

        // Vendedor
        $vendedorPermissions = \App\Models\Permission::where('slug', 'like', 'kanban.%')
            ->orWhere('slug', 'like', 'chat.%')
            ->get();
        $vendedor->permissions()->attach($vendedorPermissions);

        // Suporte
        $suportePermissions = \App\Models\Permission::where('slug', 'like', 'chat.%')->get();
        $suporte->permissions()->attach($suportePermissions);

        // Create a default Admin user
        $user = \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->roles()->attach($admin);
    }
}
