<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Status;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categories
        $categories = [
            ['name' => 'Technical Support', 'slug' => 'technical-support', 'description' => 'Masalah teknis pada perangkat atau software'],
            ['name' => 'Account & Access', 'slug' => 'account-access', 'description' => 'Permintaan akses atau masalah akun'],
            ['name' => 'Website', 'slug' => 'website', 'description' => 'Masalah atau permintaan website'],
            ['name' => 'Jaringan', 'slug' => 'jaringan', 'description' => 'Masalah koneksi jaringan atau internet'],
            ['name' => 'Hardware', 'slug' => 'hardware', 'description' => 'Masalah atau permintaan perangkat keras'],
            ['name' => 'Software', 'slug' => 'software', 'description' => 'Instalasi atau masalah aplikasi'],
            ['name' => 'IOT', 'slug' => 'iot', 'description' => 'Masalah atau permintaan perangkat IoT'],
            ['name' => 'Permintaan Kebutuhan', 'slug' => 'permintaan-kebutuhan', 'description' => 'Permintaan kebutuhan'],
            ['name' => 'Maintenance', 'slug' => 'maintenance', 'description' => 'Maintenance'],
            ['name' => 'Lainnya', 'slug' => 'lainnya', 'description' => 'Kategori lainnya'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Priorities
        $priorities = [
            ['name' => 'Low', 'slug' => 'low', 'color' => '#22c55e', 'level' => 1],
            ['name' => 'Medium', 'slug' => 'medium', 'color' => '#3b82f6', 'level' => 2],
            ['name' => 'High', 'slug' => 'high', 'color' => '#f97316', 'level' => 3],
        ];

        foreach ($priorities as $priority) {
            Priority::create($priority);
        }

        // Statuses
        $statuses = [
            ['name' => 'Baru', 'slug' => 'baru', 'color' => '#3b82f6', 'order' => 1, 'is_default' => true, 'is_closed' => false],
            ['name' => 'In Progress', 'slug' => 'in-progress', 'color' => '#f59e0b', 'order' => 2, 'is_default' => false, 'is_closed' => false],
            ['name' => 'Menunggu Persetujuan', 'slug' => 'menunggu-persetujuan', 'color' => '#a855f7', 'order' => 3, 'is_default' => false, 'is_closed' => false],
            ['name' => 'Selesai', 'slug' => 'selesai', 'color' => '#10b981', 'order' => 4, 'is_default' => false, 'is_closed' => false],
            ['name' => 'Closed', 'slug' => 'closed', 'color' => '#6b7280', 'order' => 5, 'is_default' => false, 'is_closed' => true],
        ];

        foreach ($statuses as $status) {
            Status::create($status);
        }

        $this->command->info('Master data seeded successfully!');
    }
}
