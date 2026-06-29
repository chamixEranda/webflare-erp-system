<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view roles',
            'add roles',
            'edit roles',
            'delete roles',
            'view users',
            'add users',
            'edit users',
            'delete users',
            'view sales',
            'add sales',
            'edit sales',
            'delete sales',
            'Approve sales',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign created permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions(Permission::all());

        $salesManagerRole = Role::firstOrCreate(['name' => 'Sales Manager']);
        $salesManagerRole->syncPermissions([
            'add sales',
            'edit sales',
        ]);

        // Assign Super Admin role to the default user if they exist
        $user = User::where('email', 'test@example.com')->first();
        if ($user) {
            $user->assignRole($superAdminRole);
        }
    }
}
