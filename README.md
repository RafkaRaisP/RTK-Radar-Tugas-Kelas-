# Radar Tugas Kelas

Aplikasi web untuk melacak deadline tugas sekolah secara real-time. Siswa bisa melihat tugas, pengurus kelas (ketua/sekretaris) bisa menambah dan mengelola tugas. Dilengkapi **radar publik** dengan hitung mundur otomatis yang diperbarui setiap detik.

---

## Fitur

| Fitur | Keterangan |
|-------|-----------|
| **Radar Publik** | Halaman yang bisa dilihat siapa saja tanpa login. Menampilkan semua tugas dengan countdown real-time dan filter kelas. |
| **Dashboard** | Halaman setelah login. Lihat tugas kelas sendiri, tandai selesai. |
| **3 Role User** | `ketua_kelas`, `sekretaris` (bisa CRUD tugas), `siswa` (hanya lihat & tandai selesai). |
| **Countdown Langsung** | Waktu tersisa dihitung otomatis dan berubah warna: hijau (>3 hari), kuning (1-3 hari), merah (<1 hari), abu-abu (kedaluwarsa). |
| **Filter Kelas** | Di radar publik, bisa pilih kelas tertentu untuk filter. |

---

## Persyaratan Sistem

- **PHP** 8.2 atau lebih baru
- **Composer** (manajer paket PHP)
- **MySQL** / MariaDB (via Laragon, XAMPP, atau langsung)
- **Laragon** atau **XAMPP** (recommended untuk lokal)
- **Node.js & NPM** (hanya jika ingin build Vite, tidak wajib untuk jalan)

---

## Cara Install (Langkah demi Langkah)

### 1. Clone project

Buka terminal/CMD di folder `C:\laragon\www\`, lalu:

```bash
git clone https://github.com/username/Radar-Tugas-Kelas.git
cd Radar-Tugas-Kelas
```

> Kalau nggak pakai git, tinggal copy folder project ke `C:\laragon\www\`.

### 2. Install dependensi PHP

```bash
composer install
```

### 3. Copy file konfigurasi

```bash
copy .env.example .env
```

### 4. Generate key aplikasi

```bash
php artisan key:generate
```

### 5. Buat database

Buka **phpMyAdmin** (http://localhost/phpmyadmin) atau terminal MySQL, lalu buat database baru:

```sql
CREATE DATABASE radar_tugas_kelas;
```

### 6. Sesuaikan file `.env`

Buka file `.env`, pastikan bagian database seperti ini:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=radar_tugas_kelas
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan `DB_PASSWORD` kalau MySQL kamu pakai password.

### 7. Jalankan migrasi (buat tabel)

```bash
php artisan migrate
```

### 8. Isi data awal (seeder)

```bash
php artisan db:seed
```

Perintah ini akan membuat 1 akun **ketua kelas**:

| Username | Password | Role | Kelas |
|----------|----------|------|-------|
| `ketua_rpl1` | `password123` | Ketua Kelas | XI RPL 1 |

### 9. Jalankan server

```bash
php artisan serve
```

Buka browser, lalu akses: **http://localhost:8000**

---

## Cara Pakai

### Untuk Semua Orang (Tanpa Login)

- Buka **http://localhost:8000/radar**
- Lihat semua deadline tugas dari semua kelas
- Gunakan dropdown **"Semua Kelas"** untuk filter per kelas
- Countdown berjalan otomatis, warna kartu berubah sesuai urgensi

### Untuk Siswa

1. Buka halaman **Register** (Daftar)
2. Isi nama lengkap, username, pilih kelas, password
3. Setelah daftar, otomatis login dan masuk ke **Dashboard**
4. Di dashboard: lihat daftar tugas, klik **"Tandai"** untuk menandai tugas selesai

### Untuk Ketua Kelas / Sekretaris

1. Login dengan akun yang sudah dibuat (misal: `ketua_rpl1` / `password123`)
2. Di dashboard:
   - Klik tombol **`+`** untuk tambah tugas baru
   - Isi judul, mata pelajaran, deadline, deskripsi (opsional)
   - Tugas yang sudah ditambahkan bisa **diedit** atau **dihapus**
3. Semua siswa di kelas yang sama akan melihat tugas tersebut

---

## Akun Default (Seeder)

| Username | Password | Role | Kelas |
|----------|----------|------|-------|
| `ketua_rpl1` | `password123` | Ketua Kelas | XI RPL 1 |

Untuk membuat akun **siswa** atau **kelas lain**, silakan daftar lewat halaman Register.

> Untuk membuat akun ketua kelas / sekretaris tambahan, bisa langsung insert ke database atau jalankan seeder baru.

---

## Struktur Folder (Penjelasan Sederhana)

```
Radar-Tugas-Kelas/
├── app/
│   ├── Http/
│   │   └── Controllers/    # Logic aplikasi (AuthController, TaskController)
│   └── Models/              # Model database (User, Task)
├── bootstrap/               # File bootstrap Laravel (jarang disentuh)
├── config/                  # Pengaturan aplikasi (database, mail, dll)
├── database/
│   ├── migrations/          # Struktur tabel database
│   └── seeders/             # Data awal (user contoh)
├── public/                  # File yang diakses publik (index.php, favicon)
├── resources/
│   └── views/               # Tampilan HTML (Blade templates)
│       ├── auth/            # Halaman login & register
│       ├── public/          # Halaman radar publik
│       └── dashboard.blade.php  # Halaman dashboard
├── routes/
│   └── web.php              # Daftar URL/rute aplikasi
├── storage/                 # Log, cache, file upload
├── vendor/                  # Library pihak ketiga (dari Composer)
├── .env                     # Konfigurasi lingkungan (database, dll)
└── composer.json            # Daftar dependensi PHP
```

---

## Alur Aplikasi

```
Browser → /radar (Radar Publik, tanpa login)
                  ↓
            Lihat semua tugas + countdown
                  ↓
         /login atau /register
                  ↓
            Dashboard (tugas kelas sendiri)
                  ↓
     ┌────────────┼────────────┐
     │            │            │
  Siswa      Ketua Kelas   Sekretaris
  (tandai     (tambah,      (tambah,
   selesai)    edit, hapus)   edit, hapus)
```

---

## Teknologi

| Teknologi | Kegunaan |
|-----------|----------|
| **Laravel 11** | Framework PHP backend |
| **MySQL** | Database |
| **Tailwind CSS** | Styling (via CDN, tanpa build) |
| **Blade** | Template engine Laravel |
| **PHP 8.2+** | Bahasa pemrograman |

---

## Troubleshooting

**"Class not found" setelah clone?**
```bash
composer dump-autoload
```

**"No application key"** 
```bash
php artisan key:generate
```

**"Table not found" atau error database?**
```bash
php artisan migrate
```

**Lupa password?** Bisa reset via phpMyAdmin, atau jalankan ulang seeder:
```bash
php artisan db:seed
```
