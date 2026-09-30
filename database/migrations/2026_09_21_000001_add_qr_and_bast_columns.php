<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('klaim', 'kode_tiket')) {
            Schema::table('klaim', function (Blueprint $table) {
                $table->string('kode_tiket', 32)->nullable()->unique()->after('status');
            });
        }

        if (!Schema::hasColumn('klaim', 'qr_token')) {
            Schema::table('klaim', function (Blueprint $table) {
                $table->string('qr_token', 64)->nullable()->after('kode_tiket');
            });
        }

        if (!Schema::hasColumn('pengembalian', 'nomor_bast')) {
            Schema::table('pengembalian', function (Blueprint $table) {
                $table->string('nomor_bast', 64)->nullable()->unique()->after('id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('klaim', function (Blueprint $table) {
            $table->dropColumn(['kode_tiket', 'qr_token']);
        });

        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropColumn('nomor_bast');
        });
    }
};
