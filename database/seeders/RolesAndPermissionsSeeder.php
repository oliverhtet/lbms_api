<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Books
            'view books',
            'create books',
            'edit books',
            'delete books',
            
            // Authors
            'view authors',
            'create authors',
            'edit authors',
            'delete authors',
            
            // Categories
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Borrowings
            'view borrowings',
            'create borrowings',
            'edit borrowings',
            'delete borrowings',
            
            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'librarian']);
        $role->givePermissionTo([
            'view books', 'create books', 'edit books',
            'view authors', 'create authors', 'edit authors',
            'view categories', 'create categories', 'edit categories',
            'view borrowings', 'create borrowings', 'edit borrowings',
        ]);

        $role = Role::create(['name' => 'member']);
        $role->givePermissionTo([
            'view books',
            'view authors',
            'view categories',
        ]);

        // Create default users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        $librarian = User::create([
            'name' => 'Librarian User',
            'email' => 'librarian@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $librarian->assignRole('librarian');

        $member = User::create([
            'name' => 'Member User',
            'email' => 'member@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $member->assignRole('member');
    }
}