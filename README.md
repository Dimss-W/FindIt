# 🔍 FINDIT UBSI — Sistem Informasi Lost & Found Multi-Kampus

<p align="center">
  <img src="public/images/logo-ubsi.png" alt="Logo UBSI" width="120" style="margin-bottom: 10px;" onerror="this.style.display='none'">
  <br>
  <strong>Aplikasi Pengelolaan Barang Hilang & Temuan Terintegrasi Multi-Kampus</strong><br>
  <em>Universitas Bina Sarana Informatika (UBSI)</em>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat&logo=laravel&logoColor=white" alt="Laravel 10">
  <img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat&logo=php&logoColor=white" alt="PHP 8.1+">
  <img src="https://img.shields.io/badge/TailwindCSS-Modern%20UI-38B2AC?style=flat&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/PWA-Ready-5A0FC8?style=flat&logo=pwa&logoColor=white" alt="PWA">
  <img src="https://img.shields.io/badge/Tests-10%20Passed-success?style=flat" alt="Tests Passed">
</p>

---

## 📖 Tentang Aplikasi

**FINDIT UBSI** adalah sistem informasi berbasis web responsif dan *Progressive Web App (PWA)* yang dirancang untuk membantu mahasiswa dan petugas kampus Universitas Bina Sarana Informatika dalam mencatat, mencari, mencocokkan, dan mengembalikan barang hilang maupun barang temuan secara transparan, aman, dan terstruktur di seluruh cabang kampus UBSI.

---

## ✨ Fitur Utama

### 1. 🎓 Akses Mahasiswa
- **Lapor Barang Hilang & Ditemukan**: Form pelaporan dengan upload multi-foto, rincian lokasi, waktu, dan ciri-ciri khusus.
- **Pencarian Cerdas & Filter Kampus**: Cari barang berdasarkan kategori, nama, atau lokasi kampus UBSI tertentu.
- **Klaim Barang Temuan & QR Tiket**: Ajukan verifikasi kepemilikan dan dapatkan QR Code Tiket Klaim resmi.
- **Notifikasi Terintegrasi**: Pemberitahuan status klaim dan rekomendasi kecocokan barang secara real-time.
- **PWA Mobile Installable**: Dapat diinstal di layar utama smartphone Android layaknya aplikasi native.

### 2. 🛡️ Portal Layanan Kampus (Petugas / Security)
- **Inventaris Hub All-in-One**: Manajemen barang menunggu verifikasi, barang diamankan di loker/pos, dan laporan kehilangan.
- **Pemindai QR Code Tiket**: Scan QR Code mahasiswa untuk validasi identitas dan serah terima seketika.
- **Cetak BAST Resmi Standar DINAS A4**: Berita Acara Serah Terima Barang lengkap dengan Kop Surat resmi Yayasan Bina Sarana Informatika / UBSI.
- **Aturan Masa Simpan & Donasi (> 90 Hari)**: Peringatan otomatis untuk barang tak bertuan yang tersimpan lebih dari 90 hari, lengkap dengan tombol alokasi donasi sosial kampus.
- **Notifikasi WhatsApp Otomatis & One-Click Chat (`wa.me`)**: Kirim informasi tiket klaim dan bukti serah terima langsung ke kontak WA mahasiswa.

### 3. ⚙️ Portal Admin Universitas
- **Master Data**: Pengelolaan data multi-kampus UBSI, kategori barang, dan staf petugas kampus.
- **Import Data Mahasiswa Excel**: Template 5 kolom (Nama, NIM, Tanggal Lahir YYYY-MM-DD, Kampus, No WhatsApp) berdesain bersih (*Clean Navy Template*).
- **Format Password Default**: Password mahasiswa otomatis mengikuti format tanggal lahir `YYYY-MM-DD`.
- **Statistik & Audit Log**: Monitoring efektivitas pengembalian barang dan pelaporan aktivitas.

### 4. ⚡ Algoritma Smart Matching
Sistem menghitung persentase kemiripan laporan kehilangan vs temuan dengan bobot terukur:
- Kampus: **30%**
- Kategori Barang: **20%**
- Kesamaan Nama Barang (Jaccard & Text Similarity): **20%**
- Lokasi Kejadian: **15%**
- Rentang Tanggal: **15%**

---

## 🛠️ Kebutuhan Sistem (Prerequisites)

- **PHP**: $\ge$ 8.1
- **Composer**: $\ge$ 2.x
- **MySQL / MariaDB** (Laragon / XAMPP direkomendasikan)
- **Web Browser Modern** (Google Chrome, Edge, Safari)

---

## 🚀 Panduan Instalasi Langkah demi Langkah

### 1. Clone Repository
```bash
git clone <URL_REPOSITORY_ANDA>
cd findit
```

### 2. Install Dependensi PHP
```bash
composer install
```

### 3. Konfigurasi Environment (`.env`)
Salin file konfigurasi contoh dan buat file `.env`:
```bash
cp .env.example .env
```
Buka file `.env` dan sesuaikan koneksi database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_findit
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Buat Database & Jalankan Migrasi + Seeder
Buat database bernama `db_findit` di MySQL (lewat phpMyAdmin / HeidiSQL), lalu jalankan:
```bash
php artisan migrate --seed
```

### 6. Buat Symbolic Link Storage
Agar foto barang yang diupload dapat diakses oleh browser:
```bash
php artisan storage:link
```

### 7. Jalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui browser di: **`http://127.0.0.1:8000`** atau virtual host Laragon **`http://findit.test`**.

---

## 🔑 Akun Demo Pengujian

Semua akun default telah disiapkan melalui Seeder untuk memudahkan pengujian:

| Role | Username / NIM / Email | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@findit.ubsi.ac.id` | `password` | Administrator Universitas |
| **Petugas** | `petugas.cengkareng@ubsi.ac.id` | `password` | Petugas UBSI Cengkareng |
| **Mahasiswa** | `19230181` | `2004-04-21` | Dimas Wijanarko (Cengkareng) |
| **Mahasiswa** | `12220199` | `2004-05-14` | Dimas Arya Pratama (Kramat 98) |
| **Mahasiswa** | `12220340` | `2004-08-20` | Annisa Putri (Margonda) |
| **Mahasiswa** | `12220551` | `2003-12-05` | Rizky Fauzi (Salemba 22) |

> 💡 *Catatan: Mahasiswa login menggunakan **NIM** dengan password format tanggal lahir **`YYYY-MM-DD`**.*

---

## 🧪 Menjalankan Pengujian (Testing)

Aplikasi dilengkapi test suite PHPUnit untuk memastikan seluruh endpoint, autentikasi, PWA manifest, template Excel, dan logika WhatsApp berfungsi optimal:

```bash
php artisan test
```

Hasil uji:
```
Tests:    10 passed (46 assertions)
Status:   100% PASSING
```

---

## 📂 Struktur Direktori Utama

```
findit/
├── app/
│   ├── Http/Controllers/    # Controller Admin, Petugas, Mahasiswa, Laporan
│   ├── Models/                 # Model LaporanBarang, Klaim, Pengembalian, dll.
│   └── Services/               # SmartMatchingService & WhatsAppService
├── database/
│   ├── migrations/             # Struktur skema tabel database
│   └── seeders/                # Data awal kampus, kategori, dan akun demo
├── docs/                       # Dokumentasi Activity Diagram & File Draw.io
├── public/
│   ├── manifest.json           # Web App Manifest PWA
│   └── sw.js                   # Service Worker Offline Caching
├── resources/views/            # Template Blade (layouts, admin, petugas, mahasiswa)
└── routes/
    └── web.php                 # Rute utama sistem
```

---

## 📄 Lisensi & Kontribusi

Dikembangkan untuk kebutuhan operasional layanan kemahasiswaan **Universitas Bina Sarana Informatika (UBSI)**.
Hak Cipta © 2026 FINDIT UBSI.
