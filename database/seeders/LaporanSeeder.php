<?php

namespace Database\Seeders;

use App\Models\Kampus;
use App\Models\Kategori;
use App\Models\Klaim;
use App\Models\LaporanBarang;
use App\Models\Notifikasi;
use App\Models\Pengembalian;
use App\Models\PenyimpananBarang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LaporanSeeder extends Seeder
{
    public function run(): void
    {
        $kaliabang = Kampus::where('kode_kampus', 'KMP-KLB')->first();
        $kramat = Kampus::where('kode_kampus', 'KMP-KRM')->first();
        $dewiSartika = Kampus::where('kode_kampus', 'KMP-DWS')->first();

        $elektronik = Kategori::where('nama_kategori', 'Elektronik')->first();
        $barangPribadi = Kategori::where('nama_kategori', 'Barang Pribadi')->first();
        $dokumen = Kategori::where('nama_kategori', 'Dokumen & Identitas')->first();
        $pakaian = Kategori::where('nama_kategori', 'Pakaian')->first();

        $admin = User::where('email', 'admin@bsi.ac.id')->first();
        $petugasKlb = User::where('email', 'admin.klb@bsi.ac.id')->first() ?? User::where('role', 'petugas')->first();
        $mhsDimas = User::where('nim', '12220199')->orWhere('email', '12220199@bsi.ac.id')->first();
        $mhsAnnisa = User::where('nim', '12220340')->orWhere('email', '12220340@bsi.ac.id')->first();
        $mhsReza = User::where('nim', '12220551')->orWhere('email', '12220551@bsi.ac.id')->first();

        // 1. LAPORAN HILANG: Dompet Kulit Hitam (Dimas di Kampus Kaliabang)
        $lapHilangDompet = LaporanBarang::updateOrCreate(
            ['kode_laporan' => 'FD-2026-00001'],
            [
                'user_id' => $mhsDimas->id,
                'kampus_id' => $kaliabang->id,
                'kategori_id' => $barangPribadi->id,
                'jenis_laporan' => 'HILANG',
                'nama_barang' => 'Dompet Kulit Hitam',
                'lokasi_kejadian' => 'Kantin Kampus Kaliabang',
                'tanggal_kejadian' => Carbon::now()->subDays(2)->toDateString(),
                'waktu_kejadian' => '12:30:00',
                'deskripsi' => 'Dompet lipat warna hitam merk Baellerry tertinggal di meja kantin saat makan siang.',
                'ciri_khusus' => 'Ada goresan kecil di sudut kanan bawah dan stiker hologram BSI di kartu mahasiswa di dalamnya.',
                'foto_barang' => null,
                'status' => 'ADA KEMUNGKINAN COCOK',
            ]
        );

        // 2. LAPORAN DITEMUKAN: Dompet Hitam Merk Baellerry (Annisa menemukan di Kantin Kaliabang)
        $lapTemuDompet = LaporanBarang::updateOrCreate(
            ['kode_laporan' => 'FD-2026-00002'],
            [
                'user_id' => $mhsAnnisa->id,
                'kampus_id' => $kaliabang->id,
                'kategori_id' => $barangPribadi->id,
                'jenis_laporan' => 'DITEMUKAN',
                'nama_barang' => 'Dompet Kulit Hitam Baellerry',
                'lokasi_kejadian' => 'Kantin Kampus Kaliabang',
                'tanggal_kejadian' => Carbon::now()->subDays(2)->toDateString(),
                'waktu_kejadian' => '13:00:00',
                'deskripsi' => 'Ditemukan dompet hitam tergeletak di kursi kantin nomor 04.',
                'ciri_khusus' => 'Dompet kulit hitam ada kartu mahasiswa UBSI dan beberapa kartu minimarket.',
                'foto_barang' => null,
                'status' => 'BARANG DIAMANKAN',
            ]
        );

        // Catat Penyimpanan Fisik oleh Petugas Kaliabang
        PenyimpananBarang::updateOrCreate(
            ['laporan_id' => $lapTemuDompet->id],
            [
                'petugas_id' => $petugasKlb->id,
                'lokasi_penyimpanan' => 'Pos Security Utama Kaliabang - Laci Khusus B02',
                'tanggal_diterima' => Carbon::now()->subDays(2)->addHours(1),
                'kondisi_barang' => 'Sangat Baik, isi utuh dan tersimpan rapi',
                'catatan' => 'Diserahkan oleh mahasiswa saudari Annisa Putri, disimpan untuk menunggu klaim pemilik.',
            ]
        );

        // 3. LAPORAN HILANG: Laptop Asus Vivobook Silver di Kramat 98
        $lapHilangLaptop = LaporanBarang::updateOrCreate(
            ['kode_laporan' => 'FD-2026-00003'],
            [
                'user_id' => $mhsReza->id,
                'kampus_id' => $kramat->id,
                'kategori_id' => $elektronik->id,
                'jenis_laporan' => 'HILANG',
                'nama_barang' => 'Laptop Asus Vivobook 14 Silver',
                'lokasi_kejadian' => 'Laboratorium Komputer 3 Lantai 4',
                'tanggal_kejadian' => Carbon::now()->subDay()->toDateString(),
                'waktu_kejadian' => '16:15:00',
                'deskripsi' => 'Laptop tertinggal di meja praktikum Lab 3 setelah jam perkuliahan Pemrograman Web.',
                'ciri_khusus' => 'Terdapat stiker GitHub Octocat dan stiker UBSI di punggung layar, casing warna silver.',
                'foto_barang' => null,
                'status' => 'SEDANG DICARI',
            ]
        );

        // 4. LAPORAN DITEMUKAN: Jaket Almamater UBSI di Kaliabang (Menunggu Verifikasi)
        $lapTemuJaket = LaporanBarang::updateOrCreate(
            ['kode_laporan' => 'FD-2026-00004'],
            [
                'user_id' => $mhsDimas->id,
                'kampus_id' => $kaliabang->id,
                'kategori_id' => $pakaian->id,
                'jenis_laporan' => 'DITEMUKAN',
                'nama_barang' => 'Jaket Almamater UBSI Ukuran L',
                'lokasi_kejadian' => 'Musholla Kampus Kaliabang',
                'tanggal_kejadian' => Carbon::now()->toDateString(),
                'waktu_kejadian' => '08:45:00',
                'deskripsi' => 'Ditemukan tergantung di gantungan sajadah musholla lantai 1.',
                'ciri_khusus' => 'Ada pin lencana ormawa BEM di kerah sebelah kiri.',
                'foto_barang' => null,
                'status' => 'MENUNGGU VERIFIKASI',
            ]
        );

        // 5. LAPORAN DITEMUKAN + KLAIM + DIKEMBALIKAN: Kunci Motor Honda Vario
        $lapKunci = LaporanBarang::updateOrCreate(
            ['kode_laporan' => 'FD-2026-00005'],
            [
                'user_id' => $mhsAnnisa->id,
                'kampus_id' => $kaliabang->id,
                'kategori_id' => $barangPribadi->id,
                'jenis_laporan' => 'DITEMUKAN',
                'nama_barang' => 'Kunci Kontak Motor Honda Vario 160',
                'lokasi_kejadian' => 'Area Parkir Motor B2 Kampus Kaliabang',
                'tanggal_kejadian' => Carbon::now()->subDays(5)->toDateString(),
                'waktu_kejadian' => '09:00:00',
                'deskripsi' => 'Ditemukan tergantung masih menempel di stop kontak motor di area parkir timur.',
                'ciri_khusus' => 'Gantungan kunci akrilik anime One Piece Luffy dan remote keyless.',
                'foto_barang' => null,
                'status' => 'DIKEMBALIKAN',
            ]
        );

        $simpanKunci = PenyimpananBarang::updateOrCreate(
            ['laporan_id' => $lapKunci->id],
            [
                'petugas_id' => $petugasKlb->id,
                'lokasi_penyimpanan' => 'Pos Security Pintu Keluar Parkir',
                'tanggal_diterima' => Carbon::now()->subDays(5)->addHour(),
                'kondisi_barang' => 'Berfungsi normal dan mulus',
                'catatan' => 'Diamankan oleh petugas patroli parkir.',
            ]
        );

        $klaimKunci = Klaim::updateOrCreate(
            ['laporan_id' => $lapKunci->id, 'user_id' => $mhsReza->id],
            [
                'petugas_id' => $petugasKlb->id,
                'deskripsi_klaim' => 'Saya lupa mencabut kunci motor Honda Vario putih saat buru-buru ujian jam 9.',
                'bukti_kepemilikan' => 'Menunjukkan STNK motor atas nama sendiri dan remote cadangan.',
                'foto_bukti' => null,
                'kode_tiket' => 'TK-UBSI-KLB-1001',
                'qr_token' => \Illuminate\Support\Str::random(32),
                'status' => 'DISETUJUI',
                'catatan_petugas' => 'Klaim terverifikasi sah. Pemilik membawa STNK asli yang nomor polisinya cocok.',
                'tanggal_diverifikasi' => Carbon::now()->subHours(1),
            ]
        );

        Pengembalian::updateOrCreate(
            ['laporan_id' => $lapKunci->id],
            [
                'klaim_id' => $klaimKunci->id,
                'petugas_id' => $petugasKlb->id,
                'user_id' => $mhsReza->id,
                'nomor_bast' => 'BAST/UBSI-KLB/2026/0001',
                'tanggal_pengembalian' => Carbon::now(),
                'catatan' => 'Barang diserahkan langsung di Pos Keamanan Kaliabang dalam keadaan lengkap dan dicek fungsi keyless-nya.',
                'foto_penyerahan' => null,
                'status' => 'DIKEMBALIKAN',
            ]
        );

        // Notifikasi untuk Dimas terkait kecocokan dompet
        Notifikasi::updateOrCreate(
            ['judul' => '⚡ Kemungkinan Barang Ditemukan (92% Cocok)'],
            [
                'user_id' => $mhsDimas->id,
                'pesan' => "Barang yang sesuai dengan laporan kehilangan Anda \"Dompet Kulit Hitam\" telah ditemukan dan diamankan di UBSI Kampus Kaliabang. Silakan cek detail dan ajukan klaim kepemilikan.",
                'link' => '/mahasiswa/laporan/' . $lapTemuDompet->id,
                'is_read' => false,
            ]
        );

        // Notifikasi selamat datang
        Notifikasi::updateOrCreate(
            ['judul' => 'Selamat Datang di FINDIT UBSI 👋'],
            [
                'user_id' => $mhsDimas->id,
                'pesan' => "FINDIT siap membantu Anda melacak barang hilang maupun mengamankan barang temuan di seluruh kampus UBSI.",
                'link' => '/mahasiswa/dashboard',
                'is_read' => true,
            ]
        );
    }
}
