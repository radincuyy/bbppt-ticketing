# 🎫 Sistem Ticketing Pengelolaan Layanan TI

<p align="center">
  <strong>Balai Besar Pengujian Perangkat Telekomunikasi (BBPPT)</strong><br>
  Sistem Pengelolaan Permintaan Layanan Teknologi Informasi
</p>

---

## Deskripsi

Sistem Ticketing Pengelolaan Layanan TI adalah aplikasi web yang dirancang untuk mengelola permintaan layanan TI secara efisien dan terstruktur. Aplikasi ini mendukung alur kerja dari pengajuan tiket hingga penyelesaian, dengan fitur persetujuan, penugasan teknisi, dan pelaporan kinerja.

## Fitur Utama

### 👤 Pemohon Layanan
- Membuat tiket permintaan layanan TI
- Upload lampiran dokumen/screenshot
- Melihat riwayat dan status tiket
- Memberikan komentar/respon
- Menutup tiket setelah selesai

### 👨‍💼 Staff Helpdesk
- Melihat semua tiket masuk
- Menugaskan tiket ke staff (Helpdesk/Teknisi)
- Mengubah kategori dan prioritas tiket
- Update status tiket
- Mengelola daftar tugas pribadi

### 🔧 Staff Teknisi
- Melihat tiket yang ditugaskan (Daftar Tugas)
- Update status tiket
- Menyelesaikan tiket

### 👔 Ketua Tim (Team Lead)
- Dashboard monitoring kinerja layanan
- Grafik statistik tiket per status dan kategori
- Tabel kinerja staff Helpdesk & Teknisi

### 🏢 Manager Layanan TI
- Dashboard monitoring lengkap
- Memberikan persetujuan (approve/reject)
- Download laporan (Excel/PDF)
- Monitoring kinerja layanan TI

---

## 🛠️ Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| **Backend** | Laravel 12 (PHP 8.4) |
| **Frontend** | Blade Templates + Tailwind CSS v4 |
| **Database** | MySQL 8.0 |
| **Icons** | Tabler Icons |
| **Charts** | Chart.js |
| **Auth & Roles** | Spatie Laravel Permission |
| **JavaScript** | Alpine.js |

---

## 📦 Instalasi

### Prasyarat
- PHP 8.2+
- Composer 2.x
- Node.js 18+ & NPM
- MySQL 8.0+

### Langkah Instalasi

```bash
# 1. Clone repository
git clone <repository-url>
cd bbppt-ticketing

# 2. Install PHP dependencies
composer install

# 3. Install NPM dependencies
npm install

# 4. Salin file environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Konfigurasi database di file .env
# DB_DATABASE=ticketing_bbppt
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Jalankan migrasi dan seeder
php artisan migrate --seed

# 8. Buat symbolic link untuk storage
php artisan storage:link

# 9. Build assets
npm run build

# 10. Jalankan server
php artisan serve
```

### Mode Development

```bash
# Jalankan semua service (server, vite, queue)
composer dev
```

---

## 🔐 Akun Default

| Role | Email | Password |
|------|-------|----------|
| Manager TI | manager@bbppt.go.id | password |
| Ketua Tim | teamlead@bbppt.go.id | password |
| Helpdesk | helpdesk@bbppt.go.id | password |
| Teknisi 1 | teknisi1@bbppt.go.id | password |
| Teknisi 2 | teknisi2@bbppt.go.id | password |
| Pemohon (Budi) | budi@bbppt.go.id | password |
| Pemohon (Siti) | siti@bbppt.go.id | password |

---

## 📁 Struktur Proyek

```
bbppt-ticketing/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Controller aplikasi
│   │   │   ├── Admin/            # Controller admin
│   │   │   ├── Auth/             # Controller autentikasi
│   │   │   ├── TiketController   # Controller utama tiket
│   │   │   └── ...
│   │   └── Requests/             # Form Request (validasi)
│   ├── Models/                   # Eloquent Models
│   └── Services/                 # Service Layer (logika bisnis)
├── database/
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
├── resources/
│   ├── css/                      # Stylesheet
│   ├── js/                       # JavaScript
│   └── views/                    # Blade templates
├── routes/
│   └── web.php                   # Web routes
└── ...
```

---

## Arsitektur Kode

Codebase mengikuti prinsip **Clean Architecture** dengan pemisahan tanggung jawab:

### 1. Form Requests
Validasi input dipisahkan ke class tersendiri untuk menjaga controller tetap bersih.

```
app/Http/Requests/
├── StoreTiketRequest.php
├── UpdateTiketRequest.php
├── StoreUserRequest.php
└── ...
```

### 2. Service Layer
Logika bisnis kompleks dipindahkan ke Service untuk reusability dan testability.

```php
// Contoh penggunaan TiketService
$tiketService = new TiketService();
$tiketService->createTiket($data, $user);
$tiketService->assignTeknisi($tiket, $teknisi);
$tiketService->closeTiket($tiket, $user, $catatan);
```

### 3. Alur Request
```
Request → Controller → FormRequest (validasi) → Service (logika) → Response
```

---

## 📊 Status Tiket

| Status | Deskripsi | Warna |
|--------|-----------|-------|
| Open | Tiket baru dibuat | 🔵 Biru |
| Dalam Proses | Sedang dikerjakan | 🟡 Kuning |
| Menunggu Persetujuan | Butuh approval Manager | 🟣 Ungu |
| Selesai | Sudah diselesaikan teknisi | 🟢 Hijau |
| Closed | Tiket ditutup oleh pemohon | ⚫ Abu |



## Lisensi

Dikembangkan untuk **Balai Besar Pengujian Perangkat Telekomunikasi (BBPPT)**.