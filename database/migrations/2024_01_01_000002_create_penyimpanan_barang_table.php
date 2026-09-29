<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyimpanan_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporan_barang')->cascadeOnDelete();
            $table->foreignId('petugas_id')->constrained('users')->cascadeOnDelete();
            $table->string('lokasi_penyimpanan'); // e.g. Pos Security Gedung A - Lemari 2
            $table->dateTime('tanggal_diterima');
            $table->string('kondisi_barang'); // e.g. Baik, Rusak ringan, Layar retak, dll.
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyimpanan_barang');
    }
};
