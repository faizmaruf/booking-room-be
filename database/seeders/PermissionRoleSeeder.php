<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionsForAdmin = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'view bookings',
            'create bookings',
            'edit bookings',
            'delete bookings',
            'view rooms',
            'create rooms',
            'edit rooms',
            'delete rooms',
            'view reports',
            'edit status bookings',
        ];

        $permissionsForUser = [
            'view bookings',
            'create bookings',
            'edit bookings',
            'delete bookings',
            'view rooms',
        ];

        $permissionForPic = [
            'view rooms',
            'create rooms',
            'edit rooms',
            'delete rooms',
            'view bookings',
            'create bookings',
            'edit bookings',
        ];

        $roles = [
            'admin',
            'user',
            'pic ruangan',
        ];

        foreach ($roles as $role) {
            $roleId = DB::table('roles')->where('name', $role)->value('id');


            if ($role === 'admin') {
                $permissions = $permissionsForAdmin;
            } elseif ($role === 'user') {
                $permissions = $permissionsForUser;
            } elseif ($role === 'pic ruangan') {
                $permissions = $permissionForPic;
            } else {
                continue;
            }

            foreach ($permissions as $permissionName) {
                $permissionId = DB::table('permissions')->where('name', $permissionName)->value('id');

                if ($permissionId) {
                    DB::table('permission_role')->insert([
                        'permission_id' => $permissionId,
                        'role_id' => $roleId,
                        'created_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
