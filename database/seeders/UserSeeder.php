<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Manager TI
        $manager = User::create([
            'name' => 'Manager TI',
            'email' => 'manager@bbppt.go.id',
            'password' => Hash::make('password'),
            'jabatan' => 'Manager Layanan TI',
            'is_active' => true,
        ]);
        $manager->assignRole('ManagerTI');

        // Create Team Lead
        $teamLead = User::create([
            'name' => 'Ketua Tim TI',
            'email' => 'teamlead@bbppt.go.id',
            'password' => Hash::make('password'),
            'jabatan' => 'Ketua Tim TI',
            'is_active' => true,
        ]);
        $teamLead->assignRole('TeamLead');

        // Create Helpdesk
        $helpdesk = User::create([
            'name' => 'Staff Helpdesk',
            'email' => 'helpdesk@bbppt.go.id',
            'password' => Hash::make('password'),
            'jabatan' => 'Staff Helpdesk',
            'is_active' => true,
        ]);
        $helpdesk->assignRole('Helpdesk');

        // Create Technicians
        $tech1 = User::create([
            'name' => 'Staff Teknisi 1',
            'email' => 'teknisi1@bbppt.go.id',
            'password' => Hash::make('password'),
            'jabatan' => 'Staff Teknisi',
            'is_active' => true,
        ]);
        $tech1->assignRole('Technician');

        $tech2 = User::create([
            'name' => 'Staff Teknisi 2',
            'email' => 'teknisi2@bbppt.go.id',
            'password' => Hash::make('password'),
            'jabatan' => 'Staff Teknisi',
            'is_active' => true,
        ]);
        $tech2->assignRole('Technician');

        // Create Requesters (Pemohon)
        $requester1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@bbppt.go.id',
            'password' => Hash::make('password'),
            'jabatan' => 'Staff Administrasi',
            'is_active' => true,
        ]);
        $requester1->assignRole('Requester');

        $requester2 = User::create([
            'name' => 'Siti Rahayu',
            'email' => 'siti@bbppt.go.id',
            'password' => Hash::make('password'),
            'jabatan' => 'Staff Keuangan',
            'is_active' => true,
        ]);
        $requester2->assignRole('Requester');

        $this->command->info('Users created successfully!');
        $this->command->info('');
        $this->command->info('Login credentials (password: password):');
        $this->command->info('- Manager TI: manager@bbppt.go.id');
        $this->command->info('- Team Lead: teamlead@bbppt.go.id');
        $this->command->info('- Helpdesk: helpdesk@bbppt.go.id');
        $this->command->info('- Teknisi 1: teknisi1@bbppt.go.id');
        $this->command->info('- Teknisi 2: teknisi2@bbppt.go.id');
        $this->command->info('- Pemohon (Budi): budi@bbppt.go.id');
        $this->command->info('- Pemohon (Siti): siti@bbppt.go.id');
    }
}
