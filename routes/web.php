<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - FINDIT (UBSI Lost & Found)
|--------------------------------------------------------------------------
*/

// ==========================================
// PUBLIC ROUTES
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/cari', [SearchController::class, 'index'])->name('search');

// PWA Assets Handler (Memastikan Manifest & Service Worker selalu disajikan dengan MIME Type yang tepat)
Route::get('/manifest.json', function () {
    return response()->file(public_path('manifest.json'), [
        'Content-Type' => 'application/manifest+json; charset=utf-8',
    ]);
});
Route::get('/sw.js', function () {
    return response()->file(public_path('sw.js'), [
        'Content-Type' => 'application/javascript; charset=utf-8',
        'Service-Worker-Allowed' => '/',
    ]);
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Quick Demo Role Switcher (Memudahkan perpindahan peran saat pengujian)
Route::get('/demo-switch/{role}', function ($role) {
    if ($role === 'mahasiswa') {
        $user = \App\Models\User::where('role', 'mahasiswa')->first();
        $target = route('mahasiswa.dashboard');
    } elseif ($role === 'petugas') {
        $user = \App\Models\User::where('email', 'admin.klb@bsi.ac.id')->first() ?? \App\Models\User::where('role', 'petugas')->first();
        $target = route('petugas.dashboard');
    } elseif ($role === 'admin') {
        $user = \App\Models\User::where('role', 'admin')->first();
        $target = route('admin.dashboard');
    } else {
        return redirect()->route('login');
    }

    if ($user) {
        \Illuminate\Support\Facades\Auth::login($user);
        return redirect($target)->with('success', 'Beralih ke akun ' . strtoupper($user->role) . ' (' . $user->name . ')');
    }

    return redirect()->route('login');
})->name('demo.switch');

// ==========================================
// AUTHENTICATED GLOBAL ROUTES (PROFILE)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profil', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profil', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profil/password', [AuthController::class, 'updatePassword'])->name('profile.password');
});

// ==========================================
// MAHASISWA ROUTES
// ==========================================
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');

    // Pusat Pelaporan & Aktivitas Terpadu (Unified Hubs)
    Route::get('/lapor', [MahasiswaController::class, 'laporHub'])->name('lapor.hub');
    Route::get('/aktivitas', [MahasiswaController::class, 'aktivitasHub'])->name('aktivitas.hub');

    // Buat Laporan (Individual / Direct endpoints)
    Route::get('/lapor/hilang', [MahasiswaController::class, 'laporHilangForm'])->name('lapor.hilang');
    Route::post('/lapor/hilang', [MahasiswaController::class, 'laporHilangSubmit']);
    Route::get('/lapor/temukan', [MahasiswaController::class, 'laporTemukanForm'])->name('lapor.temukan');
    Route::post('/lapor/temukan', [MahasiswaController::class, 'laporTemukanSubmit']);

    // Manajemen Laporan Saya & Detail
    Route::get('/laporan-saya', [MahasiswaController::class, 'laporanSaya'])->name('laporan.saya');
    Route::get('/laporan/{id}', [MahasiswaController::class, 'detailLaporan'])->name('laporan.detail');
    Route::get('/laporan/{id}/edit', [MahasiswaController::class, 'editLaporan'])->name('laporan.edit');
    Route::put('/laporan/{id}', [MahasiswaController::class, 'updateLaporan'])->name('laporan.update');
    Route::delete('/laporan/{id}', [MahasiswaController::class, 'deleteLaporan'])->name('laporan.delete');

    // Pengajuan Klaim
    Route::get('/laporan/{id}/klaim', [MahasiswaController::class, 'klaimForm'])->name('klaim.form');
    Route::post('/laporan/{id}/klaim', [MahasiswaController::class, 'submitKlaim'])->name('klaim.submit');
    Route::get('/klaim-saya', [MahasiswaController::class, 'klaimSaya'])->name('klaim.saya');

    // Notifikasi
    Route::get('/notifikasi', [MahasiswaController::class, 'notifikasi'])->name('notifikasi');
    Route::post('/notifikasi/{id}/read', [MahasiswaController::class, 'markNotifikasiAsRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [MahasiswaController::class, 'markAllNotifikasiAsRead'])->name('notifikasi.read_all');
});

// ==========================================
// ADMIN & LAYANAN KAMPUS (VERIFIKASI & PENGEMBALIAN)
// ==========================================
Route::middleware(['auth', 'role:admin,petugas'])->prefix('layanan-kampus')->name('petugas.')->group(function () {
    Route::get('/dashboard', [PetugasController::class, 'dashboard'])->name('dashboard');

    // Pusat Kerja Terpadu (Unified Hubs)
    Route::get('/kelola-barang', [PetugasController::class, 'inventarisHub'])->name('inventaris.hub');
    Route::get('/serah-terima', [PetugasController::class, 'serahTerimaHub'])->name('serah_terima.hub');

    Route::get('/barang-ditemukan', [PetugasController::class, 'barangDitemukan'])->name('barang.ditemukan');
    Route::get('/menunggu-verifikasi', [PetugasController::class, 'menungguVerifikasi'])->name('menunggu.verifikasi');

    // Verifikasi fisik barang
    Route::get('/verifikasi/{id}', [PetugasController::class, 'verifikasiForm'])->name('verifikasi.form');
    Route::post('/verifikasi/{id}', [PetugasController::class, 'simpanVerifikasi'])->name('verifikasi.simpan');

    // Barang Diamankan & Laporan Hilang di Kampus
    Route::get('/barang-diamankan', [PetugasController::class, 'barangDiamankan'])->name('barang.diamankan');
    Route::post('/barang/{id}/donasikan', [PetugasController::class, 'donasikanBarang'])->name('barang.donasikan');
    Route::get('/laporan-hilang', [PetugasController::class, 'laporanHilang'])->name('laporan.hilang');

    // Smart Matching di Kampus
    Route::get('/smart-matching', [PetugasController::class, 'smartMatching'])->name('smart.matching');

    // Klaim Mahasiswa
    Route::get('/klaim', [PetugasController::class, 'pengajuanKlaim'])->name('klaim.index');
    Route::get('/klaim/{id}', [PetugasController::class, 'detailKlaim'])->name('klaim.detail');
    Route::post('/klaim/{id}/verifikasi', [PetugasController::class, 'verifikasiKlaim'])->name('klaim.verifikasi');

    // Pengembalian Barang & Scanner QR
    Route::get('/scan-qr', [PetugasController::class, 'scanQrIndex'])->name('scan.qr');
    Route::post('/scan-qr/verify', [PetugasController::class, 'verifyQrTicket'])->name('scan.qr.verify');
    Route::post('/scan-qr/proses', [PetugasController::class, 'prosesSerahTerimaQr'])->name('scan.qr.proses');
    Route::get('/bast/{id}/cetak', [PetugasController::class, 'cetakBast'])->name('bast.cetak');

    Route::get('/barang-siap-diambil', [PetugasController::class, 'barangSiapDiambil'])->name('barang.siap_diambil');
    Route::get('/pengembalian/{id}', [PetugasController::class, 'formPengembalian'])->name('pengembalian.form');
    Route::post('/pengembalian/{id}', [PetugasController::class, 'simpanPengembalian'])->name('pengembalian.simpan');
    Route::get('/riwayat-pengembalian', [PetugasController::class, 'riwayatPengembalian'])->name('riwayat.pengembalian');
});

// ==========================================
// ADMIN GLOBAL ROUTES
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Manajemen Mahasiswa
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'simpanUser'])->name('users.simpan');
    Route::post('/users/import', [AdminController::class, 'importExcelUser'])->name('users.import');
    Route::get('/users/template', [AdminController::class, 'downloadTemplateUser'])->name('users.template');
    Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');

    // Manajemen Petugas
    Route::get('/petugas', [AdminController::class, 'petugas'])->name('petugas');
    Route::post('/petugas', [AdminController::class, 'simpanPetugas'])->name('petugas.simpan');
    Route::put('/petugas/{id}', [AdminController::class, 'updatePetugas'])->name('petugas.update');
    Route::post('/petugas/{id}/toggle', [AdminController::class, 'togglePetugasStatus'])->name('petugas.toggle');

    // Manajemen Kampus
    Route::get('/kampus', [AdminController::class, 'kampus'])->name('kampus');
    Route::post('/kampus', [AdminController::class, 'simpanKampus'])->name('kampus.simpan');
    Route::put('/kampus/{id}', [AdminController::class, 'updateKampus'])->name('kampus.update');
    Route::post('/kampus/{id}/toggle', [AdminController::class, 'toggleKampusStatus'])->name('kampus.toggle');

    // Manajemen Kategori
    Route::get('/kategori', [AdminController::class, 'kategori'])->name('kategori');
    Route::post('/kategori', [AdminController::class, 'simpanKategori'])->name('kategori.simpan');
    Route::put('/kategori/{id}', [AdminController::class, 'updateKategori'])->name('kategori.update');

    // Moderasi Semua Laporan
    Route::get('/semua-laporan', [AdminController::class, 'semuaLaporan'])->name('laporan.index');
    Route::delete('/laporan/{id}', [AdminController::class, 'hapusLaporan'])->name('laporan.hapus');

    // Monitoring Klaim & Statistik
    Route::get('/semua-klaim', [AdminController::class, 'semuaKlaim'])->name('klaim.index');
    Route::get('/statistik', [AdminController::class, 'statistik'])->name('statistik');
});
