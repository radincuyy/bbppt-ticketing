<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Prioritas;
use App\Models\Status;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data Kategori
        $kategoris = [
            ['nama_kategori' => 'Technical Support'],
            ['nama_kategori' => 'Account & Access'],
            ['nama_kategori' => 'Website'],
            ['nama_kategori' => 'Jaringan'],
            ['nama_kategori' => 'Hardware'],
            ['nama_kategori' => 'Software'],
            ['nama_kategori' => 'IOT'],
            ['nama_kategori' => 'Permintaan Kebutuhan'],
            ['nama_kategori' => 'Maintenance'],
            ['nama_kategori' => 'Lainnya'],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create($kategori);
        }

        // Data Prioritas dengan kode warna
        $prioritass = [
            ['nama_prioritas' => 'Low', 'color' => '#10B981'],
            ['nama_prioritas' => 'Medium', 'color' => '#F59E0B'],
            ['nama_prioritas' => 'High', 'color' => '#EF4444'],
        ];

        foreach ($prioritass as $prioritas) {
            Prioritas::create($prioritas);
        }

        // Data Status dengan kode warna
        $statuses = [
            ['nama_status' => 'Baru', 'color' => '#3B82F6'],
            ['nama_status' => 'Dalam Proses', 'color' => '#F59E0B'],
            ['nama_status' => 'Menunggu Persetujuan', 'color' => '#8B5CF6'],
            ['nama_status' => 'Selesai', 'color' => '#10B981'],
            ['nama_status' => 'Closed', 'color' => '#6B7280'],
        ];

        foreach ($statuses as $status) {
            Status::create($status);
        }

        $this->command->info('Data master berhasil di-seed!');
    }
}
