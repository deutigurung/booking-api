<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'Administrator']);
        $owner = Role::create(['name' => 'Property Owner']);
        $user = Role::create(['name' => 'Simple User']);

        $permission = Permission::create(
            ['name' => 'properties-manage'],
        );
        $admin->permissions()->sync($permission);
        $owner->permissions()->sync($permission);


    }
}
