<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporan_barang')->cascadeOnDelete();
            $table->foreignId('klaim_id')->nullable()->constrained('klaim')->nullOnDelete();
            $table->foreignId('petugas_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('tanggal_pengembalian');
            $table->text('catatan')->nullable();
            $table->string('foto_penyerahan')->nullable();
            $table->string('status')->default('DIKEMBALIKAN');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};
