# Radar Tugas Kelas — Master Design System

> Global Source of Truth. Halaman baru wajib mengikuti file ini.
> Override per-halaman: `design-system/radar-tugas-kelas/pages/<nama-halaman>.md`

**Sumber:** tema yang sudah berjalan di codebase (divalidasi dari `resources/css/app.css`,
`tailwind.config.js`, dan seluruh Blade view), ditambah hasil pencarian domain terverifikasi
ui-ux-pro-max.

---

## 1. Identitas & Konsep

| | |
|---|---|
| **Produk** | Aplikasi pelacak deadline tugas sekolah (dashboard + radar publik) |
| **Audiens** | Siswa Indonesia (SMA/SMK), pengurus kelas, admin sekolah |
| **Konsep inti** | **Buku agenda sekolah** — kertas bergaris, sampul merah marun, selotip, stempel |
| **Suara** | Editorial, ringkas, instruktif. Label bahasa Indonesia, mono uppercase |

**Gaya terverifikasi (`--domain style`):**

- `e-ink-paper` — paper background, ink text, tanpa gradient, reading-focused.
- `editorial-grid-magazine` — asymmetric grid, mono uppercase labels, section divider, tracking lebar.
- `anti-polish-raw-aesthetic` — tape overlay, rotasi kecil, tekstur kertas, imperfection disengaja.

**Jangan pernah:** glassmorphism, gradient halus, blur shadow tebal, rounded ≥16px,
emoji sebagai ikon, gaya "SaaS modern" generik.

---

## 2. Tipografi

Pola terverifikasi (`--domain typography`): **Serif + Sans + Mono triple stack**.
Konfigurasi yang ada sudah memenuhi pola itu — **pertahankan, jangan ganti.**

| Peran | Font | Bobot | Pemakaian |
|---|---|---|---|
| Display | **Spectral** (`.font-display`) | 700 / 800 | Judul H1–H3, angka besar |
| Body / UI | **Figtree** | 400–700 | Paragraf, tombol, input |
| Label / Data | **JetBrains Mono** (`.font-mono`) | 400 / 500 / 700 | Label, tanggal, countdown, badge |

**Aturan:**
- Label selalu `uppercase` + `tracking-[.16em]` s/d `[.24em]`.
- Angka yang berubah (countdown, tanggal) wajib `tabular-nums`.
- Jangan pakai mono untuk paragraf. Jangan pakai serif untuk label kecil.

```js
// tailwind.config.js — jangan diubah
fontFamily: {
    sans: ['Figtree', ...defaultTheme.fontFamily.sans],
}
// display & mono didefinisikan sebagai class di resources/css/app.css
```

---

## 3. Warna

### 3.1 Identitas (konstan, tidak boleh berubah)

| Token | Nilai | Kontras | Catatan |
|---|---|---|---|
| `--brand-deep` | `#A30F2B` | 6.89 vs `#f5efe3` | Sampul, sidebar, header |
| `--brand` | `#BE1836` | 5.44 vs `#f5efe3` | Aksen, tombol utama, fokus |
| `--brand-ink` | `#760B20` | — | Bayangan tombol (`box-shadow`) |

### 3.2 Surface

| Token | Light | Dark |
|---|---|---|
| `--surface-page` | `#F5EFE3` (notebook bg) | `#111113` |
| `--surface-paper` | `#FFFDF7` (`.paper-sheet`) | `#1F1F22` |
| `--surface-cover` | `#A30F2B` | `#18181B` |

### 3.3 Teks (SEMUA ≥ 4.5:1 — sudah diverifikasi)

Gunakan **class semantik**, bukan step warna Tailwind:

| Class | Light | Dark | Pengganti |
|---|---|---|---|
| `.txt-muted` | `#6B655D` → **5.03 / 5.66** | `#A1A1AA` → **7.36 / 6.41** | `text-stone-400` (2.20) |
| `.txt-label` | `#57534E` → **6.66 / 7.50** | `#D4D4D8` → aman | `text-stone-500` (4.19) |
| `--text-primary` | `#292524` | `#FAFAFA` | — |
| `--label-on-cover` | `#FFFFFF` → **7.89** | `#FECACA` → **12.25** | `text-red-100/60` (2.78) |

Didefinisikan lewat custom property di `:root` / `.dark` sehingga spesifisitas
tetap `(0,1,0)` dan **tidak menimpa** utility `hover:`/`focus:`/`dark:` `(0,2,0)`.

> ⚠️ **Dilarang:** `text-stone-400`, `text-stone-500`, `text-stone-300` (teks),
> `text-red-100/60`, `text-red-100/70`, `dark:text-zinc-500`, `dark:text-zinc-600`.
> Semuanya sudah dihapus dari codebase — jangan ditulis ulang.
>
> `tailwind.config.js` juga me-remap `stone-400` → `#6B655D` dan `stone-500` → `#57534E`
> sebagai pengaman bila ada pemakaian baru.

### 3.4 Urgency / Countdown (informasi terpenting — wajib ≥ 4.5:1)

Dipakai oleh `.task-card-item[data-urgency]` → `--urgency`, pada teks `.cd-count`,
`.tl-count`, `.status-dot`, dan border-top kartu.

Nilai di bawah lolos di **kedua** surface terang: kertas `#FFFDF7` **dan**
latar notebook `#F5EFE3` (kasus lebih ketat).

| Band | Light (paper / notebook) | Dark (kartu `#1F1F22`) |
|---|---|---|
| `green` (>3 hari) | `#047857` → **5.39 / 4.79** | `#10B981` → **6.48** |
| `yellow` (1–3 hari) | `#92400E` → **6.97 / 6.19** | `#D97706` → **5.16** |
| `red` (<1 hari) | `#BE123C` → **6.18 / 5.49** | `#F87171` → **5.94** |
| `expired` | `#6B655D` → **5.66 / 5.03** | `#A1A1AA` → **6.41** |

Soft variant: opacity 10–14% dari warna di atas.

> Catatan: `#B45309` (4.39), `#DC2626` (4.22) dan `#78716C` (4.19) lolos di kertas
> tapi **gagal** di latar notebook — karena itu dipakai nilai yang lebih gelap.

### 3.5 Status lain

| Status | Light | Dark |
|---|---|---|
| Success / Terverifikasi | `#047857` | `#34D399` |
| Pending / Menunggu | `#B45309` | `#FBBF24` |
| Danger / Hapus | `#DC2626` | `#F87171` |

---

## 4. Komponen

### 4.1 Surface & Bayangan (skeuomorphic, bukan blur)

```css
.paper-sheet {
    background: rgba(255, 253, 247, .96);
    border: 1px solid rgba(67, 57, 48, .16);
    box-shadow: 5px 6px 0 rgba(71, 55, 39, .07), 0 18px 45px -34px rgba(48,37,26,.6);
}
```
- Offset hard shadow (`5px 6px 0`) — **bukan** `0 4px 6px blur`.
- Radius: `rounded-sm` saja (4px). Jangan `rounded-lg`/`rounded-xl`.
- Selotip: `.paper-card::before` — lebar 92px, rotasi `-1.5deg`, warna `rgba(221,205,166,.72)`.

### 4.2 Tombol

| Varian | Kelas | Efek |
|---|---|---|
| Primer | `bg-[#be1836] text-white .primary-button` | `box-shadow: 0 4px 0 #760b20`; hover `translateY(-1px)`; active `translateY(3px)` + shadow 1px |
| Sekunder | `border border-stone-300` + hover border/teks merah | `.pressable` transition 180ms |
| Icon | `h-11 w-11` (44×44) | **wajib** `aria-label` |

Aturan:
- **Min height 44px** (`min-h-11`). `min-h-10` (40px) hanya bila diperluas hit-area-nya.
- Active state **tidak boleh** mengubah layout bounds — hanya `transform`/`shadow`.
- Semua elemen interaktif: `cursor-pointer` + `.pressable`.

### 4.3 Form

```css
.field { border: 1px solid rgba(67,57,48,.2); border-bottom: 2px solid rgba(67,57,48,.38); }
.field:focus { border-color: #BE1836; box-shadow: 0 0 0 3px rgba(190,24,54,.12); outline: none; }
```
- Label: `.font-mono` uppercase `text-[9px]`/`text-[10px]` + `tracking-[.18em]`.
- Setiap input punya `<label>` (atau `sr-only` jika visual-only).
- Error: inline di bawah input, `text-red-600` / `text-rose-400`, dengan ikon/penanda **bukan warna saja**.
- **Auth:** `username` → `autocomplete="username"`, password → `autocomplete="current-password"`.
  Dilarang `autocomplete="off"` pada form login (WCAG 2.2 AA).

### 4.4 Kartu Tugas

- Border-top 4px `var(--urgency)` = semaphore utama.
- Status dot: `aria-hidden="true"`, selalu didampingi teks countdown (jangan warna saja).
- Hover: `translateY(-3px)` + rotasi `±.15deg` (genap/ganjil berlawanan).

### 4.5 Modal

- Overlay: `rgba(28,20,16,.64)` + `backdrop-filter: blur(5px)`.
- **Wajib:** `role="dialog"`, `aria-modal`, `aria-labelledby`, focus trap Tab/Shift+Tab,
  kembalikan fokus ke trigger saat tutup, `Escape` menutup, `inert` pada konten belakang.
- **Semua** modal harus punya focus trap — jangan hanya `addModal`.

---

## 5. Ikon & Aset

- **Tidak ada emoji** sebagai ikon struktural. (Status: sudah bersih — dipertahankan.)
- Semua ikon inline SVG, `stroke="currentColor"`.
- Stroke konsisten per layer: **2** untuk kontrol/header, **1.5** untuk ikon dekoratif/informational.
- Ukuran token: `h-4 w-4` (label inline), `h-5 w-5` (tombol), `h-7 w-7` (empty state).
- Logo: `images/logo.png`, di sampul selalu `brightness-0 invert` agar seragam.
- Ikon bermakna tanpa teks pendamping → wajib `aria-label`.
- Ikon dekoratif di samping teks → `aria-hidden="true"`.

---

## 6. Layout & Spacing

- Rhythm: **skala 4/8** — `gap-2`(8) / `gap-3`(12) / `gap-5`(20) / `gap-7`(28) / `gap-9`(36).
- Container: `max-w-7xl` (publik), `max-w-6xl` (dashboard), `max-w-[430px]` (form auth).
- Gutter: `px-5` mobile → `sm:px-8` desktop.
- Sidebar: `w-[260px]` fixed, konten `lg:pl-[260px]`.
- Section divider: `border-b-2 border-stone-800/80 dark:border-zinc-100/80`.
- Semua fixed header/tab bar wajib punya padding konten bawah agar tidak menutupi scroll.

**Breakpoints:** 375px (wajib), 768px, 1024px, 1440px.

---

## 7. Motion

| Preset | Nilai | Penggunaan |
|---|---|---|
| `--ease-out-expo` | `cubic-bezier(.16,1,.3,1)` | Semua entrance & hover |
| `--ease-theme` | `cubic-bezier(.65,0,.35,1)` | View transition tema (700ms) |
| Durasi micro | `180ms` | `.pressable`, field focus |
| Durasi kartu | `220ms` | `.task-card` hover |
| Durasi entrance | `450–550ms` | `pageIn`, stagger 55ms, delay awal 120ms |

- View transition: clip-path circle dari titik klik (`--rtk-x/y/maxr`).
- **Wajib** menghormati `prefers-reduced-motion` (sudah ada di `app.css` — jangan dihapus).
- Durasi reduced-motion tetap mempertahankan `animation-fill-mode: forwards` agar kartu tidak
  terjebak `opacity: 0`.

---

## 8. Aksesibilitas (Pre-Delivery Checklist)

- [ ] Kontras teks **≥ 4.5:1** di light **dan** dark — ukur, jangan asumsi.
- [ ] Kontras non-teks (border status, fokus) ≥ 3:1.
- [ ] Urgency/status **tidak** disampaikan warna saja — selalu ada teks.
- [ ] Semua tombol icon-only punya `aria-label`.
- [ ] Icon dekoratif `aria-hidden="true"`.
- [ ] Touch target ≥ 44×44px.
- [ ] Focus visible (`:focus-visible` outline) di kedua tema.
- [ ] Form: label + hint + error inline; multi-error fokus ke error summary.
- [ ] Password manager & paste berfungsi di semua form auth.
- [ ] Modal: focus trap, Escape, kembalikan fokus, `aria-modal`.
- [ ] Permission prompt (Notification, Geolocation) hanya setelah gestur user.
- [ ] `prefers-reduced-motion` dihormati.
- [ ] Tidak ada emoji sebagai ikon.
- [ ] Ikon satu keluarga, stroke konsisten.

---

## 9. Anti-Pattern (Dilarang)

1. Emoji 🎨🚀⚙️ sebagai ikon navigasi/kontrol.
2. Blur shadow (`0 4px 12px rgba(...)`) menggantikan offset hard shadow.
3. Rounded `lg`+ pada kartu, tombol, input.
4. Warna teks redup (`stone-400`, `red-100/60`) untuk teks apa pun.
5. Countdown/status hanya lewat warna tanpa teks.
6. `autocomplete="off"` pada form login.
7. Permission prompt otomatis saat halaman dimuat.
8. Modal tanpa focus trap.
9. Satu durasi/easing dipakai untuk semua transisi.
10. Hardcode hex baru di view — tambahkan token dulu.

---

## 10. Stack Notes

- **Laravel 11 + Blade + Tailwind CSS 3 + Vite.**
- `tailwind.config.js` wajib memuat **`darkMode: 'class'`** — toggle tema di
  `layouts/app.blade.php` mengoperasikan class `.dark` pada `<html>`. Tanpa itu
  Tailwind jatuh ke default `'media'` dan seluruh utility `dark:` mengikuti
  preferensi OS, bukan tombol toggle.
- `content` menyertakan `storage/framework/views/*.php`. Jalankan
  `php artisan view:clear` sebelum `npm run build` bila menghapus kelas dari view,
  kalau tidak kelas lama dari cache Blade ikut ter-compile.
- Pisahkan logika variant ke component class (`match()`), jangan rantai `@if` panjang di Blade.
- Gunakan `@props` bila membuat Blade component.
- Build: `npm run build` / dev: `npm run dev` (Vite, bukan CDN).

## 13. Diketahui Rusak (di luar cakupan desain)

| Halaman | Gejala | Penyebab |
|---|---|---|
| `GET /forgot-password` | 500 | `routes/web.php` memanggil `AuthController::showForgotForm()` yang tidak ada |
| `GET /reset-password/{token}` | 500 | `AuthController::showResetForm()` juga tidak ada |

`AuthController` hanya berisi: `showLoginForm`, `showRegisterForm`, `register`,
`login`, `logout`. Route lupa/reset password perlu method tersebut — bukan bug UI.
