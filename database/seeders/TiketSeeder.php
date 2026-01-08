<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tiket;
use App\Models\Komentar;
use App\Models\Kategori;
use App\Models\Prioritas;
use App\Models\Status;
use App\Models\User;

class TiketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = Kategori::all();
        $prioritass = Prioritas::all();
        $statuses = Status::all();
        
        // Get users by role
        $pemohons = User::role('Pemohon')->get();
        $teknisis = User::role('Technician')->get();
        $helpdesks = User::role('Helpdesk')->get();

        // Dummy tickets data
        $tickets = [
            [
                'judul' => 'Tidak bisa login ke aplikasi SIMPEG',
                'deskripsi' => 'Saya sudah mencoba login dengan username dan password yang benar, tetapi selalu muncul pesan "Invalid credentials". Sudah dicoba reset password tapi tetap tidak bisa.',
                'kategori' => 'Account & Access',
                'prioritas' => 'High',
                'status' => 'Baru',
            ],
            [
                'judul' => 'Komputer sering restart sendiri',
                'deskripsi' => 'Komputer di ruang kerja saya sering restart secara tiba-tiba, terutama saat membuka aplikasi berat seperti Excel dengan file besar.',
                'kategori' => 'Hardware',
                'prioritas' => 'High',
                'status' => 'Dalam Proses',
                'assign_teknisi' => true,
            ],
            [
                'judul' => 'Request instalasi Microsoft Office',
                'deskripsi' => 'Mohon untuk diinstalkan Microsoft Office 2021 di komputer baru saya. Komputer sudah ready di ruangan.',
                'kategori' => 'Software',
                'prioritas' => 'Medium',
                'status' => 'Baru',
            ],
            [
                'judul' => 'Koneksi internet lambat',
                'deskripsi' => 'Koneksi internet di lantai 2 sangat lambat, terutama pada jam 10-12 siang. Speed test menunjukkan hanya 2 Mbps padahal biasanya 50 Mbps.',
                'kategori' => 'Jaringan',
                'prioritas' => 'Medium',
                'status' => 'Dalam Proses',
                'assign_teknisi' => true,
            ],
            [
                'judul' => 'Printer tidak bisa print',
                'deskripsi' => 'Printer Canon di ruang admin tidak bisa mencetak. Sudah dicoba restart printer dan komputer tapi tetap tidak bisa. Lampu printer menyala normal.',
                'kategori' => 'Hardware',
                'prioritas' => 'Medium',
                'status' => 'Selesai',
                'assign_teknisi' => true,
            ],
            [
                'judul' => 'Website internal error 500',
                'deskripsi' => 'Website internal BBPPT menampilkan error 500 saat mengakses halaman laporan. Error ini muncul sejak kemarin sore.',
                'kategori' => 'Website',
                'prioritas' => 'High',
                'status' => 'Menunggu Persetujuan',
                'assign_teknisi' => true,
            ],
            [
                'judul' => 'Request akun email baru',
                'deskripsi' => 'Mohon dibuatkan akun email untuk pegawai baru atas nama Andi Wijaya dengan format email: andi.wijaya@bbppt.go.id',
                'kategori' => 'Account & Access',
                'prioritas' => 'Low',
                'status' => 'Closed',
                'assign_teknisi' => true,
            ],
            [
                'judul' => 'Maintenance rutin server',
                'deskripsi' => 'Jadwal maintenance rutin server bulan Januari. Mohon dikoordinasikan untuk waktu yang tepat agar tidak mengganggu operasional.',
                'kategori' => 'Maintenance',
                'prioritas' => 'Low',
                'status' => 'Baru',
            ],
            [
                'judul' => 'Sensor IoT tidak mengirim data',
                'deskripsi' => 'Sensor suhu dan kelembaban di ruang server tidak mengirim data ke dashboard monitoring sejak 2 hari yang lalu.',
                'kategori' => 'IOT',
                'prioritas' => 'High',
                'status' => 'Dalam Proses',
                'assign_teknisi' => true,
            ],
            [
                'judul' => 'Permintaan laptop untuk WFH',
                'deskripsi' => 'Mohon disediakan laptop untuk keperluan Work From Home. Spesifikasi minimal: RAM 8GB, SSD 256GB, Windows 11.',
                'kategori' => 'Permintaan Kebutuhan',
                'prioritas' => 'Medium',
                'status' => 'Menunggu Persetujuan',
            ],
        ];

        foreach ($tickets as $index => $ticketData) {
            // Get related IDs
            $kategori = $kategoris->where('nama_kategori', $ticketData['kategori'])->first();
            $prioritas = $prioritass->where('nama_prioritas', $ticketData['prioritas'])->first();
            $status = $statuses->where('nama_status', $ticketData['status'])->first();
            $pemohon = $pemohons->random();
            
            $tiket = Tiket::create([
                'judul' => $ticketData['judul'],
                'deskripsi' => $ticketData['deskripsi'],
                'id_kategori' => $kategori->id_kategori,
                'id_prioritas' => $prioritas->id_prioritas,
                'id_status' => $status->id_status,
                'id_pengguna' => $pemohon->id,
                'id_teknisi' => isset($ticketData['assign_teknisi']) ? $teknisis->random()->id : null,
            ]);

            // Add some comments for tickets that are in progress or completed
            if (in_array($ticketData['status'], ['Dalam Proses', 'Selesai', 'Closed'])) {
                Komentar::create([
                    'id_tiket' => $tiket->id_tiket,
                    'id_pengguna' => $helpdesks->first()->id,
                    'isi_komentar' => 'Tiket sudah diterima dan sedang diproses.',
                ]);

                if (in_array($ticketData['status'], ['Selesai', 'Closed'])) {
                    Komentar::create([
                        'id_tiket' => $tiket->id_tiket,
                        'id_pengguna' => $teknisis->random()->id,
                        'isi_komentar' => 'Masalah sudah diselesaikan. Mohon konfirmasi jika sudah berjalan normal.',
                    ]);
                }
            }
        }

        $this->command->info('Ticket dummy data seeded successfully! (' . count($tickets) . ' tickets)');
    }
}
