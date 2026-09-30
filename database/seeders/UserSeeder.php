<?php

namespace Database\Seeders;

use App\Models\Kampus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ADMIN UTAMA GLOBAL (Memegang seluruh kontrol sistem dan supervisi 27 kampus)
        User::updateOrCreate(
            ['email' => 'admin@bsi.ac.id'],
            [
                'name' => 'Administrator FINDIT UBSI',
                'nim' => 'ADM-GLOBAL',
                'tanggal_lahir' => '1990-01-01',
                'no_telp' => '081299990001',
                'role' => 'admin',
                'kampus_id' => null,
                'avatar' => null,
                'is_active' => true,
                'password' => Hash::make('password123'),
            ]
        );

        // Backup alias admin@findit.bsi.ac.id
        User::updateOrCreate(
            ['email' => 'admin@findit.bsi.ac.id'],
            [
                'name' => 'Administrator FINDIT UBSI',
                'nim' => 'ADM-GLOBAL-2',
                'tanggal_lahir' => '1990-01-01',
                'no_telp' => '081299990002',
                'role' => 'admin',
                'kampus_id' => null,
                'avatar' => null,
                'is_active' => true,
                'password' => Hash::make('password123'),
            ]
        );

        // Alias sesuai README.md (admin@findit.ubsi.ac.id / password)
        User::updateOrCreate(
            ['email' => 'admin@findit.ubsi.ac.id'],
            [
                'name' => 'Administrator FINDIT UBSI',
                'nim' => 'ADM-GLOBAL-3',
                'tanggal_lahir' => '1990-01-01',
                'no_telp' => '081299990003',
                'role' => 'admin',
                'kampus_id' => null,
                'avatar' => null,
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );

        // 2. GENERATE AKUN PETUGAS / ADMIN LAYANAN UNTUK SELURUH 27 KAMPUS
        $campuses = Kampus::all();
        foreach ($campuses as $kmp) {
            $rawCode = strtolower(str_replace(['KMP-', ' '], ['', ''], $kmp->kode_kampus));

            $email = "admin.{$rawCode}@bsi.ac.id";

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => 'Admin Layanan ' . preg_replace('/^UBSI Kampus /i', '', $kmp->nama_kampus),
                    'nim' => 'ADM-' . str_replace('KMP-', '', $kmp->kode_kampus),
                    'tanggal_lahir' => '1992-05-15',
                    'no_telp' => '0812' . str_pad((string)$kmp->id, 8, '8', STR_PAD_LEFT),
                    'role' => 'petugas',
                    'kampus_id' => $kmp->id,
                    'avatar' => null,
                    'is_active' => true,
                    'password' => Hash::make('password123'),
                ]
            );
        }

        // 3. DUMMY MAHASISWA (Format email resmi UBSI: nim@bsi.ac.id)
        $firstKampus = Kampus::where('kode_kampus', 'KMP-KLB')->first() ?? $campuses->first();
        $secondKampus = Kampus::where('kode_kampus', 'KMP-KRM')->first() ?? $campuses->last();
        $thirdKampus = Kampus::where('kode_kampus', 'KMP-MGD')->first() ?? $campuses->skip(1)->first();

        // Mahasiswa 1: Dimas Arya Pratama (NIM: 12220199 | Tgl Lahir: 14 Mei 2004)
        User::updateOrCreate(
            ['nim' => '12220199'],
            [
                'name' => 'Dimas Arya Pratama',
                'email' => '12220199@bsi.ac.id',
                'tanggal_lahir' => '2004-05-14',
                'no_telp' => '085712345678',
                'role' => 'mahasiswa',
                'kampus_id' => $firstKampus?->id,
                'avatar' => null,
                'is_active' => true,
                'password' => Hash::make('2004-05-14'), // Password default login: YYYY-MM-DD
            ]
        );

        // Mahasiswa 2: Annisa Putri Rahmawati (NIM: 12220340 | Tgl Lahir: 20 Agustus 2004)
        User::updateOrCreate(
            ['nim' => '12220340'],
            [
                'name' => 'Annisa Putri Rahmawati',
                'email' => '12220340@bsi.ac.id',
                'tanggal_lahir' => '2004-08-20',
                'no_telp' => '085888776655',
                'role' => 'mahasiswa',
                'kampus_id' => $secondKampus?->id,
                'avatar' => null,
                'is_active' => true,
                'password' => Hash::make('2004-08-20'), // Password default login: YYYY-MM-DD
            ]
        );

        // Mahasiswa 3: Rizky Fauzi (NIM: 12220551 | Tgl Lahir: 05 Desember 2003)
        User::updateOrCreate(
            ['nim' => '12220551'],
            [
                'name' => 'Rizky Fauzi',
                'email' => '12220551@bsi.ac.id',
                'tanggal_lahir' => '2003-12-05',
                'no_telp' => '085799112233',
                'role' => 'mahasiswa',
                'kampus_id' => $thirdKampus?->id,
                'avatar' => null,
                'is_active' => true,
                'password' => Hash::make('2003-12-05'), // Password default login: YYYY-MM-DD
            ]
        );

        // Mahasiswa 4: Dimas Wijanarko (NIM: 19230181 | Tgl Lahir: 21 April 2004)
        User::updateOrCreate(
            ['nim' => '19230181'],
            [
                'name' => 'Dimas Wijanarko',
                'email' => '19230181@bsi.ac.id',
                'tanggal_lahir' => '2004-04-21',
                'no_telp' => '085711223344',
                'role' => 'mahasiswa',
                'kampus_id' => $secondKampus?->id,
                'avatar' => null,
                'is_active' => true,
                'password' => Hash::make('2004-04-21'), // Password default login: YYYY-MM-DD
            ]
        );

        // Mahasiswa 5: Dika Fathur (NIM: 12210181 | Tgl Lahir: 20 September 2004) - sesuai README.md
        $ckgKampus = Kampus::where('nama_kampus', 'like', '%Cengkareng%')->first() ?? $firstKampus;
        User::updateOrCreate(
            ['nim' => '12210181'],
            [
                'name' => 'Dika Fathur',
                'email' => '12210181@bsi.ac.id',
                'tanggal_lahir' => '2004-09-20',
                'no_telp' => '081234567890',
                'role' => 'mahasiswa',
                'kampus_id' => $ckgKampus?->id,
                'avatar' => null,
                'is_active' => true,
                'password' => Hash::make('2004-09-20'), // Password default login: YYYY-MM-DD
            ]
        );

        // Alias Petugas Cengkareng sesuai README.md (petugas.cengkareng@ubsi.ac.id / password)
        User::updateOrCreate(
            ['email' => 'petugas.cengkareng@ubsi.ac.id'],
            [
                'name' => 'Petugas Layanan UBSI Cengkareng',
                'nim' => 'ADM-CKG-README',
                'tanggal_lahir' => '1992-05-15',
                'no_telp' => '081299887766',
                'role' => 'petugas',
                'kampus_id' => $ckgKampus?->id,
                'avatar' => null,
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );
    }
}
