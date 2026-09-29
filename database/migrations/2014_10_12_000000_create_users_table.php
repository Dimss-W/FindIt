<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('nim', 30)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_telp', 25)->nullable();
            $table->enum('role', ['admin', 'petugas', 'mahasiswa'])->default('mahasiswa');
            $table->foreignId('kampus_id')->nullable()->constrained('kampus')->nullOnDelete();
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
