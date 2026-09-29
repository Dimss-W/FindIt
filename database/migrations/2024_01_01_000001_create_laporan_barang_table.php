<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_laporan', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kampus_id')->constrained('kampus')->cascadeOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori')->cascadeOnDelete();
            $table->enum('jenis_laporan', ['HILANG', 'DITEMUKAN']);
            $table->string('nama_barang');
            $table->string('lokasi_kejadian');
            $table->date('tanggal_kejadian');
            $table->time('waktu_kejadian')->nullable();
            $table->text('deskripsi');
            $table->text('ciri_khusus')->nullable();
            $table->text('foto_barang')->nullable();
            $table->string('status', 40); // SEDANG DICARI, ADA KEMUNGKINAN COCOK, MENUNGGU VERIFIKASI, BARANG DIAMANKAN, SIAP DIAMBIL, DIKEMBALIKAN, DITOLAK
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_barang');
    }
};
