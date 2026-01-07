# Sistem Ticketing Pengelolaan Layanan TI - BBPPT

Aplikasi web untuk pengelolaan tiket layanan Teknologi Informasi di Balai Besar Pengembangan Penjaminan Mutu Pendidikan Vokasi (BBPPT).

## 📋 Deskripsi

Sistem Ticketing ini memungkinkan:
- Pemohon untuk mengajukan tiket layanan TI
- Staff Helpdesk untuk mengelola dan menugaskan tiket
- Staff Teknisi untuk menangani tiket teknis
- Manager untuk memberikan persetujuan dan memonitor kinerja

## 🛠️ Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Blade Templates + Tailwind CSS v4
- **Database**: MySQL
- **Icons**: Tabler Icons
- **Auth & Roles**: Spatie Laravel Permission
- **Activity Log**: Spatie Laravel Activitylog

## 👥 Role Pengguna

| Role | Deskripsi |
|------|-----------|
| **Pemohon Layanan** | User yang mengajukan tiket |
| **Staf Helpdesk** | Menerima dan mengelola tiket masuk |
| **Staf Teknisi** | Menangani tiket teknis |
| **Ketua Tim (TeamLead)** | Memantau kinerja layanan |
| **Manager Layanan TI** | Memberikan persetujuan, memonitor, download laporan |

## 📦 Fitur Utama

### Pemohon Layanan
- ✅ Membuat tiket baru
- ✅ Upload lampiran
- ✅ Melihat riwayat & status tiket
- ✅ Memberikan komentar/respon
- ✅ Menutup tiket (setelah resolved)

### Staf Helpdesk
- ✅ Melihat semua tiket masuk
- ✅ Mengubah kategori & prioritas tiket
- ✅ Menugaskan tiket ke staff (Helpdesk/Teknisi)
- ✅ Update status tiket
- ✅ Catatan internal (tidak terlihat pemohon)

### Staf Teknisi
- ✅ Melihat tiket yang ditugaskan
- ✅ Update status tiket
- ✅ Menyelesaikan tiket

### Manager Layanan TI
- ✅ Dashboard monitoring
- ✅ Memberikan persetujuan (approve/reject)
- ✅ Download laporan (Excel/PDF)

## 🚀 Instalasi

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL / SQLite

### Langkah Instalasi

```bash
# Clone repository
git clone <repository-url>
cd ticketing-bbppt-2

# Install PHP dependencies
composer install

# Install NPM dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Setup database (edit .env sesuai konfigurasi)
php artisan migrate

# Seed data master & user
php artisan db:seed

# Build assets
npm run build

# Jalankan server
php artisan serve
```

## Default Login

| Role | Email | Password |
|------|-------|----------|
| Manager TI | manager@bbppt.go.id | password |
| Ketua Tim | teamlead@bbppt.go.id | password |
| Helpdesk | helpdesk@bbppt.go.id | password |
| Teknisi | teknisi@bbppt.go.id | password |
| Pemohon | user@bbppt.go.id | password |

## Struktur Folder

```
ticketing-bbppt-2/
├── app/
│   ├── Http/Controllers/     # Controller
│   ├── Models/               # Eloquent Models
│   └── ...
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── resources/
│   ├── views/                # Blade templates
│   └── css/                  # Stylesheets
├── routes/
│   └── web.php               # Web routes
└── ...
```

## Status Tiket

| Status | Deskripsi |
|--------|-----------|
| Open | Tiket baru dibuat |
| In Progress | Sedang dikerjakan |
| Menunggu Persetujuan | Butuh approval Manager |
| Resolved | Sudah diselesaikan |
| Closed | Tiket ditutup |

## Prioritas

| Prioritas | Level |
|-----------|-------|
| Kecil (Low) | 1 |
| Sedang (Normal) | 2 |
| Tinggi (High) | 3 |

## Perintah Artisan

```bash
# Clear cache
php artisan optimize:clear

# Re-seed database
php artisan migrate:fresh --seed

# Run development server
php artisan serve
```

## Catatan Pengembangan

- Sistem menggunakan soft delete untuk data penting
- Audit trail menggunakan Spatie Activity Log
- File upload disimpan di storage/app/public

**Dikembangkan untuk BBPPT © 2026**
