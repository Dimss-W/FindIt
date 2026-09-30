<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klaim', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporan_barang')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('deskripsi_klaim');
            $table->text('bukti_kepemilikan')->nullable();
            $table->string('foto_bukti')->nullable();
            $table->enum('status', ['MENUNGGU VERIFIKASI', 'DISETUJUI', 'DITOLAK'])->default('MENUNGGU VERIFIKASI');
            $table->string('kode_tiket', 32)->nullable()->unique();
            $table->string('qr_token', 64)->nullable();
            $table->text('catatan_petugas')->nullable();
            $table->timestamp('tanggal_diverifikasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klaim');
    }
};
