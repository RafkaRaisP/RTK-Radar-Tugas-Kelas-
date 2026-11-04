# Radar Tugas Kelas

Aplikasi web untuk melacak deadline tugas sekolah secara real-time — dari **SMK Telekomunikasi Telesandi Bekasi**, berisi data nyata **692 siswa** di **25 kelas** se-3 angkatan (X, XI, XII). Siswa mendaftar pakai **NIS**, pengurus kelas & PJ mengelola tugas, dan siapa pun bisa melihat **radar publik** dengan hitung mundur otomatis tanpa login.

---

## Fitur

| Fitur | Keterangan |
|-------|-----------|
| **Radar Publik** | `/radar` — bisa dibuka tanpa login. Semua tugas dengan countdown real-time (berubah tiap detik) + filter kelas. |
| **Daftar Pakai NIS** | Isi NIS 9 digit → nama, kelas, dan tahun ajaran otomatis terisi dari data siswa (dicek real-time, NIS ganda/salah ditolak). |
| **5 Role User** | `admin`, `ketua_kelas`, `sekretaris`, `pj_mapel`, `siswa`. |
| **Kelola Tugas** | Ketua/Sekretaris/PJ/Admin bisa tambah–edit–hapus tugas di agenda masing-masing; siswa lihat, cari, filter, dan tandai selesai. |
| **Foto Bukti + Verifikasi** | Siswa upload foto bukti pengerjaan → menunggu verifikasi admin → status Selesai/Proses. |
| **PJ Mapel** | Pengurus per mapel; konfirmasi bukti tugas (flow verifikasi khusus PJ). |
| **3 Tahun Ajaran / 25 Kelas** | Kelas X (TA 2026/2027), XI (2025/2026), XII (2024/2025). Dropdown kelas **selalu tersaring per angkatan** — pilih tahun ajaran dulu, baru kelasnya muncul. Admin tetap bisa lihat semua 25 kelas. |
| **Countdown Langsung** | Hijau (>3 hari), kuning (1–3 hari), merah (<1 hari), abu-abu (lewat deadline). |
| **Export PDF** | Tombol "Export PDF" di agenda → laporan bertema web (kop sekolah, badge status, blok tanda tangan), dihitung server-side dalam WIB. |
| **Dark Mode** | Toggle tema gelap/terang, preferensi tersimpan di browser. |
| **Lupa Password** | Reset lewat email (konfigurasi mail di `.env`). |

---

## Persyaratan Sistem

- **PHP** 8.2+ (ekstensi `pdo_mysql`, `gd`/`dom` untuk dompdf)
- **Composer**
- **MySQL / MariaDB** (Laragon/XAMPP)
- **Node.js & NPM** (build Tailwind CSS via Vite — wajib agar tampilan sesuai)

---

## Cara Install

### 1. Ambil project

```bash
cd C:\laragon\www
git clone https://github.com/username/Radar-Tugas-Kelas.git
cd Radar-Tugas-Kelas
```

> Tanpa git: copy folder project ke `C:\laragon\www\`.

### 2. Dependensi PHP + konfigurasi

```bash
composer install
copy .env.example .env
php artisan key:generate
```

### 3. Buat database

Buka **phpMyAdmin** (http://localhost/phpmyadmin), lalu:

```sql
CREATE DATABASE radar_tugas_kelas;
```

Pastikan di `.env` bagian database sesuai:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=radar_tugas_kelas
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi + isi data awal

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

`db:seed` aman dijalankan ulang (idempotent — memakai `updateOrCreate`). Yang dibuat:

- **3 tahun ajaran**: `2026/2027` (aktif, kelas X), `2025/2026` (XI), `2024/2025` (XII)
- **692 data siswa** beserta NIS asli dari Excel pembagian kelas
- **Akun demo** (tabel di bawah)

> Sudah punya database lama? `php artisan migrate` + `php artisan db:seed` tetap jalan — seeder otomatis menyamakan `academic_year_id` semua data lama dengan angkatan kelasnya.

### 5. Build aset frontend

```bash
npm install
npm run build
```

### 6. Jalankan

```bash
php artisan serve
```

Buka **http://localhost:8000** — radar publik: **http://localhost:8000/radar**

---

## Akun Demo

Semua password di bawah bersifat tetap (sama di setiap instalasi).

### Admin

| Username | Password | Role | Kelas |
|----------|----------|------|-------|
| `admin` | `admin123` | Admin | XI RPL 1 |

### Ketua Kelas (angkatan XI)

| Username | Password | Kelas |
|----------|----------|-------|
| `ketua_rpl1` | `77ce0684` | XI RPL 1 |
| `ketua_rpl2` | `97f02bc4` | XI RPL 2 |
| `ketua_rpl3` | `b006c51b` | XI RPL 3 |
| `ketua_rpl4` | `7596feb8` | XI RPL 4 |
| `ketua_tkj1` | `1109c771` | XI TKJ 1 |
| `ketua_tkj2` | `a53960f1` | XI TKJ 2 |
| `ketua_dkv1` | `c90f4379` | XI DKV 1 |
| `ketua_dkv2` | `1d763b0f` | XI DKV 2 |
| `ketua_transmisi1` | `45ce8663` | XI Transmisi 1 |

### Sekretaris (angkatan XI)

| Username | Password | Kelas |
|----------|----------|-------|
| `sekretaris_rpl1` | `ad7d2509` | XI RPL 1 |
| `sekretaris_rpl2` | `90be026e` | XI RPL 2 |
| `sekretaris_rpl3` | `a8e5a428` | XI RPL 3 |
| `sekretaris_rpl4` | `83f787a1` | XI RPL 4 |
| `sekretaris_tkj1` | `3f059d02` | XI TKJ 1 |
| `sekretaris_tkj2` | `5bec118f` | XI TKJ 2 |
| `sekretaris_dkv1` | `266c7e31` | XI DKV 1 |
| `sekretaris_dkv2` | `98811d29` | XI DKV 2 |
| `sekretaris_transmisi1` | `ca755d21` | XI Transmisi 1 |

### PJ Mapel

| Username | Password | Mapel | Kelas |
|----------|----------|-------|-------|
| `pj_matematika` | `pj123456` | Matematika | XI RPL 1 |
| `pj_bahasa` | `pj123456` | Bahasa Indonesia | XI RPL 1 |

### Siswa (1 per angkatan)

| Username | Password | Kelas | NIS |
|----------|----------|-------|-----|
| `siswa_x` | `demo1234` | X RPL 1 | NIS siswa pertama X RPL 1 |
| `siswa_xi` | `demo1234` | XI RPL 1 | NIS siswa pertama XI RPL 1 |
| `siswa_xii` | `demo1234` | XII RPL 1 | NIS siswa pertama XII RPL 1 |

Ingin cek/mengganti password satu akun dari terminal:

```bash
php artisan tinker
>>> App\Models\User::where('username', 'ketua_rpl1')->update(['password' => Illuminate\Support\Facades\Hash::make('password123')]);
```

---

## Cara Pakai

### Semua Orang (tanpa login)

1. Buka `/radar`
2. Lihat semua deadline tugas + countdown yang berjalan otomatis
3. Filter pakai dropdown kelas (dikelompokkan per angkatan X / XI / XII)

### Siswa (baru)

1. Buka **Register**
2. Isi **NIS** dulu — nama, kelas, dan tahun ajaran akan terkunci otomatis kalau NIS terdaftar
3. Lanjut isi username, email, password → otomatis login ke **Dashboard**
4. Di dashboard: lihat tugas kelas sendiri, upload **foto bukti**, tandai selesai (menunggu verifikasi admin)

### Ketua / Sekretaris / PJ

1. Login dengan akun masing-masing
2. Di **Agenda**: tambah (`+`), edit, hapus tugas sesuai kelas & mapelnya
3. PJ: konfirmasi bukti tugas yang masuk di halaman PJ
4. Tombol **Export PDF** untuk mengunduh daftar tugas kelas

### Admin

1. Login `admin` / `admin123`
2. Panel admin: **Users**, **Tugas**, **Verifikasi foto bukti**, **PJ Mapel**, **Mapel**, **Tahun Ajaran**
3. Semua dropdown kelas admin mencakup **25 kelas**; filter bisa dipersempit per tahun ajaran
4. Saat tambah tugas: pilih **tahun ajaran** dulu → daftar kelas tersaring → tahun tugas otomatis mengikuti kelas yang dipilih

---

## Struktur Folder

```
Radar-Tugas-Kelas/
├── app/
│   ├── Http/Controllers/      # AuthController, TaskController, Admin*Controller, dll.
│   └── Models/                # User, Task, Student, AcademicYear, ProofPhoto, PjAssignment, Subject
├── database/
│   ├── migrations/            # Struktur tabel (+ students, users.nis)
│   └── seeders/               # AcademicYearSeeder, StudentSeeder, DatabaseSeeder
├── resources/
│   └── views/
│       ├── auth/              # login, register (cek NIS), forgot/reset password
│       ├── admin/             # panel admin (users, tasks, verifications, pj, subjects, academic-years)
│       ├── dashboard/         # agenda siswa/pengurus
│       ├── pj/                # dashboard PJ mapel
│       ├── public/            # radar publik
│       ├── exports/           # template PDF (tasks-pdf.blade.php)
│       └── layouts/           # app, dashboard
├── routes/web.php             # Daftar URL/rute
├── tests/Feature/             # Test otomatis (php artisan test)
└── composer.json / package.json
```

---

## Alur Data

```
Excel Pembagian Kelas ──► students (692 siswa, 25 kelas, 3 angkatan)
                                 │
        Register (NIS 9 digit) ──┤ cek real-time /students/{nis}/check
                                 ▼
                            users (siswa)
                                 │
    ┌───────────────┬───────────┼──────────────┬──────────────┐
    │               │           │              │              │
  admin         ketua_kelas  sekretaris     pj_mapel       siswa
 (semua kelas)  (kelasnya)   (kelasnya)   (mapelnya)   (lihat+upload bukti)
                                 │
                    tasks (academic_year_id selalu
                    = tahun ajaran angkatan kelasnya)
                                 │
                    foto bukti ──► verifikasi admin ──► Selesai/Proses
                                 │
                    Export PDF (WIB, bertema web)
```

---

## Teknologi

| Teknologi | Kegunaan |
|-----------|----------|
| **Laravel 11** | Framework PHP backend |
| **MySQL** | Database |
| **Tailwind CSS 3 + Vite** | Styling (di-build lokal, bukan CDN) |
| **Blade + vanilla JS** | Template & interaktivitas (tanpa framework frontend) |
| **barryvdh/laravel-dompdf** | Export PDF laporan tugas |

---

## Troubleshooting

**"Class not found" / class alias salah setelah clone?**
```bash
composer dump-autoload
```

**"No application key"**
```bash
php artisan key:generate
```

**"Table not found"**
```bash
php artisan migrate
php artisan db:seed
```

**Tampilan berantakan / class Tailwind tidak ada** (aset belum di-build):
```bash
npm install
npm run build
```

**Foto bukti tidak muncul**
```bash
php artisan storage:link
```

**Reset seluruh data ke awal** (HATI-HATI: menghapus semua data):
```bash
php artisan migrate:fresh --seed
```

**Ganti password demo** → lihat snippet tinker di bagian *Akun Demo*.

---

## Untuk Tugas Akhir

- Data siswa & pembagian kelas berasal dari file resmi sekolah: `PEMBAGIAN KELAS 2627 per 3 Agustus 2026.xlsx` (SMK Telekomunikasi Telesandi Bekasi, TP 2026/2027) — diimpor ke tabel `students` (NIS, nama, kelas, tahun ajaran).
- Aturan emas aplikasi: **kelas = angkatan** — `class_name` selalu sejajar dengan `academic_year_id` (X↔2026/2027, XI↔2025/2026, XII↔2024/2025), divalidasi di sisi server pada setiap form admin.
- Test otomatis: `php artisan test`
