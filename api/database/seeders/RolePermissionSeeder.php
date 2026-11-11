<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // F001 workflow transition permissions (enforced server-side)
        $perms = [
            'f001.create', 'f001.update', 'f001.submit_review',      // site_officer: draft -> pending_review
            'f001.validate',                                         // executive: pending_review -> pending_approval
            'f001.final_approve',                                    // manager: pending_approval -> submitted
            'f001.return',                                           // executive/manager/admin: -> returned
            'f001.override',                                         // super_admin: any transition
            'site.manage', 'user.manage', 'report.manage',
            'attendance.manage', 'monitoring.manage',
        ];
        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        $roles = [
            'super_admin'  => $perms, // everything
            'manager'      => ['f001.final_approve', 'f001.return', 'site.manage', 'report.manage', 'monitoring.manage'],
            'executive'    => ['f001.validate', 'f001.return', 'report.manage'],
            'site_officer' => ['f001.create', 'f001.update', 'f001.submit_review'],
            'po'           => ['site.manage', 'report.manage'],
            'authenticated'=> [],
            'public'       => [],
        ];
        foreach ($roles as $name => $grant) {
            $role = Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            $role->syncPermissions($grant);
        }
    }
}
