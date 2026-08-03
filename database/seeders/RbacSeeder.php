<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Permission
        $p1 = Permission::create(['name' => 'manage-users', 'display_name' => 'Manage Users']);
        $p2 = Permission::create(['name' => 'manage-products', 'display_name' => 'Manage Products']);

        // 2. Buat Role
        $adminRole = Role::create(['name' => 'super-admin', 'display_name' => 'Super Admin']);
        $staffRole = Role::create(['name' => 'staff', 'display_name' => 'Staff Biasa']);

        // 3. Pasangkan Permission ke Role
        $adminRole->permissions()->attach([$p1->id, $p2->id]); // Admin dapat semua
        $staffRole->permissions()->attach([$p2->id]);         // Staff cuma bisa manage produk

        // 4. Buat User & Pasang Rolenya
        $adminUser = User::create([
            'name' => 'Bima Admin',
            'email' => 'admin@rbac.test',
            'password' => Hash::make('password123'),
        ]);
        $adminUser->roles()->attach($adminRole->id);

        $staffUser = User::create([
            'name' => 'Rendra Staff',
            'email' => 'staff@rbac.test',
            'password' => Hash::make('password123'),
        ]);
        $staffUser->roles()->attach($staffRole->id);
    }
}