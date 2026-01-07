<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
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
            // Ticket permissions
            'tickets.view.own',
            'tickets.view.assigned',
            'tickets.view.all',
            'tickets.create',
            'tickets.update.own',
            'tickets.update.assigned',
            'tickets.update.all',
            'tickets.assign',
            'tickets.close.own',
            'tickets.close.all',
            'tickets.delete',
            
            // Approval permissions
            'approvals.view',
            'approvals.approve',
            'approvals.reject',
            
            // Comment permissions
            'comments.create',
            'comments.create.internal',
            'comments.view.internal',
            
            // Master data permissions
            'master.categories.manage',
            'master.priorities.manage',
            'master.statuses.manage',
            'master.users.manage',
            
            // Dashboard & Reports
            'dashboard.view',
            'reports.view',
            'audit.view',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        // 1. Requester (Pemohon)
        $requester = Role::create(['name' => 'Requester']);
        $requester->givePermissionTo([
            'tickets.view.own',
            'tickets.create',
            'tickets.update.own',
            'tickets.close.own',
            'comments.create',
            'dashboard.view',
        ]);

        // 2. Helpdesk
        $helpdesk = Role::create(['name' => 'Helpdesk']);
        $helpdesk->givePermissionTo([
            'tickets.view.all',
            'tickets.create',
            'tickets.update.all',
            'tickets.assign',
            'tickets.close.all',
            'comments.create',
            'comments.create.internal',
            'comments.view.internal',
            'master.categories.manage',
            'master.priorities.manage',
            'master.statuses.manage',
            'master.users.manage',
            'dashboard.view',
            'reports.view',
        ]);

        // 3. Technician (Teknisi)
        $technician = Role::create(['name' => 'Technician']);
        $technician->givePermissionTo([
            'tickets.view.assigned',
            'tickets.update.assigned',
            'comments.create',
            'comments.create.internal',
            'comments.view.internal',
            'dashboard.view',
        ]);

        // 4. TeamLead (Ketua Tim)
        $teamLead = Role::create(['name' => 'TeamLead']);
        $teamLead->givePermissionTo([
            'tickets.view.all',
            'tickets.update.all',
            'tickets.assign',
            'comments.create',
            'comments.create.internal',
            'comments.view.internal',
            'dashboard.view',
            'reports.view',
            'audit.view',
        ]);

        // 5. ManagerTI (Manager Layanan TI)
        $manager = Role::create(['name' => 'ManagerTI']);
        $manager->givePermissionTo([
            'tickets.view.all',
            'tickets.update.all',
            'tickets.assign',
            'tickets.close.all',
            'approvals.view',
            'approvals.approve',
            'approvals.reject',
            'comments.create',
            'comments.create.internal',
            'comments.view.internal',
            'dashboard.view',
            'reports.view',
            'audit.view',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}
