<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            KampusSeeder::class,
            KategoriSeeder::class,
            UserSeeder::class,
            // LaporanSeeder::class, // Dinonaktifkan agar data laporan tetap bersih dari nol
        ]);
    }
}
