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
        // Manager TI
        $manager = User::create([
            'name' => 'Manager TI',
            'email' => 'manager@bbppt.go.id',
            'password' => Hash::make('password'),
            'jabatan' => 'Manager Layanan TI',
        ]);
        $manager->assignRole('ManagerTI');

        // Team Lead
        $teamLead = User::create([
            'name' => 'Ketua Tim TI',
            'email' => 'teamlead@bbppt.go.id',
            'password' => Hash::make('password'),
            'jabatan' => 'Ketua Tim',
        ]);
        $teamLead->assignRole('TeamLead');

        // Helpdesk Staff
        $helpdesk = User::create([
            'name' => 'Staff Helpdesk',
            'email' => 'helpdesk@bbppt.go.id',
            'password' => Hash::make('password'),
            'jabatan' => 'Staff Helpdesk',
        ]);
        $helpdesk->assignRole('Helpdesk');

        // Technicians
        $teknisi1 = User::create([
            'name' => 'Teknisi 1',
            'email' => 'teknisi1@bbppt.go.id',
            'password' => Hash::make('password'),
            'jabatan' => 'Staff Teknisi',
        ]);
        $teknisi1->assignRole('Technician');

        $teknisi2 = User::create([
            'name' => 'Teknisi 2',
            'email' => 'teknisi2@bbppt.go.id',
            'password' => Hash::make('password'),
            'jabatan' => 'Staff Teknisi',
        ]);
        $teknisi2->assignRole('Technician');

        // Requesters (Pemohon)
        $pemohon1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@bbppt.go.id',
            'password' => Hash::make('password'),
            'jabatan' => 'Staff Umum',
        ]);
        $pemohon1->assignRole('Pemohon');

        $pemohon2 = User::create([
            'name' => 'Siti Rahayu',
            'email' => 'siti@bbppt.go.id',
            'password' => Hash::make('password'),
            'jabatan' => 'Staff Keuangan',
        ]);
        $pemohon2->assignRole('Pemohon');

        $this->command->info('Users created successfully!');
        $this->command->newLine();
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
