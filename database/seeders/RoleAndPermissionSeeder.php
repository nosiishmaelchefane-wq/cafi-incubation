<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define modules and their permissions
        $modules = [
            'Dashboard' => ['view'],
            'Calls for Applications' => ['view', 'create', 'edit', 'delete', 'approve'],
            'Applications' => ['view', 'create', 'edit', 'delete', 'approve'],
            'Screening & Eligibility' => ['view', 'create', 'edit', 'delete', 'approve'],
            'Evaluation & Scoring' => ['view', 'create', 'edit', 'delete', 'approve'],
            'Cohort Management' => ['view', 'create', 'edit', 'delete', 'approve'],
            'Enterprise Reports' => ['view', 'create', 'edit', 'delete', 'approve'],
            'ESO Reports' => ['view', 'create', 'edit', 'delete', 'approve'],
            'User Management' => ['view', 'create', 'edit', 'delete', 'approve'],
            'Knowledge Hub' => ['view', 'create', 'edit', 'delete'],
            'Analytics & Reporting' => ['view'],
        ];

        // Create permissions
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissionName = $action . ' ' . $module;
                Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
            }
        }

        // Create roles and assign permissions
        $roles = [
            'Super Administrator' => Permission::all(), // All permissions
            'CAFI Administrator' => [
                'view Dashboard',
                'view Calls for Applications', 'create Calls for Applications', 'edit Calls for Applications', 'approve Calls for Applications',
                'view Applications', 'edit Applications', 'approve Applications',
                'view Screening & Eligibility', 'edit Screening & Eligibility', 'approve Screening & Eligibility',
                'view Evaluation & Scoring', 'edit Evaluation & Scoring', 'approve Evaluation & Scoring',
                'view Cohort Management', 'edit Cohort Management',
                'view Enterprise Reports',
                'view ESO Reports',
                'view User Management', 'edit User Management',
                'view Knowledge Hub', 'create Knowledge Hub', 'edit Knowledge Hub',
                'view Analytics & Reporting',
            ],
            'Enterprise Support Org.' => [
                'view Dashboard',
                'view Calls for Applications',
                'view Applications', 'edit Applications',
                'view Screening & Eligibility',
                'view Evaluation & Scoring',
                'view Cohort Management',
                'view Enterprise Reports', 'create Enterprise Reports',
                'view Knowledge Hub', 'create Knowledge Hub',
                'view Analytics & Reporting',
            ],
            'Entrepreneur' => [
                'view Dashboard',
                'view Calls for Applications',
                'view Applications', 'create Applications', 'edit Applications',
                'view Knowledge Hub',
                'view Analytics & Reporting',
            ],
            'Mentor' => [
                'view Dashboard',
                'view Applications',
                'view Screening & Eligibility',
                'view Evaluation & Scoring', 'create Evaluation & Scoring',
                'view Cohort Management',
                'view Knowledge Hub', 'create Knowledge Hub',
                'view Analytics & Reporting',
            ],
            'Investor' => [
                'view Dashboard',
                'view Calls for Applications',
                'view Applications',
                'view Enterprise Reports',
                'view Knowledge Hub',
                'view Analytics & Reporting',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
            
            if ($permissions instanceof \Illuminate\Database\Eloquent\Collection) {
                $role->syncPermissions($permissions);
            } else {
                foreach ($permissions as $permission) {
                    $permission = Permission::where('name', $permission)->first();
                    if ($permission) {
                        $role->givePermissionTo($permission);
                    }
                }
            }
        }

        // Assign Super Administrator to first user (if exists)
        $user = User::first();
        if ($user) {
            $user->assignRole('Super Administrator');
        }
    }
}