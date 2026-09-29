<?php

namespace Database\Seeders;

use App\Models\Kampus;
use Illuminate\Database\Seeder;

class KampusSeeder extends Seeder
{
    public function run(): void
    {
        $campuses = [
            // 1. Kampus Utama (Jabodetabek)
            [
                'kode_kampus' => 'KMP-KRM',
                'nama_kampus' => 'UBSI Kampus Kramat 98 (Rektorat)',
                'alamat' => 'Jl. Kramat Raya No.98, RT.2/RW.9, Kwitang, Kec. Senen',
                'kota' => 'Jakarta Pusat',
            ],
            [
                'kode_kampus' => 'KMP-PMD',
                'nama_kampus' => 'UBSI Kampus Pemuda',
                'alamat' => 'Jl. Pemuda No.10, RT.8/RW.4, Rawamangun, Kec. Pulo Gadung',
                'kota' => 'Jakarta Timur',
            ],
            [
                'kode_kampus' => 'KMP-KLM',
                'nama_kampus' => 'UBSI Kampus Kalimalang',
                'alamat' => 'Jl. Inspeksi Saluran Kalimalang No.88',
                'kota' => 'Jakarta Timur',
            ],
            [
                'kode_kampus' => 'KMP-DWS',
                'nama_kampus' => 'UBSI Kampus Dewi Sartika',
                'alamat' => 'Jl. Dewi Sartika No.289, RT.4/RW.4, Cawang, Kec. Kramat Jati',
                'kota' => 'Jakarta Timur',
            ],
            [
                'kode_kampus' => 'KMP-CKR',
                'nama_kampus' => 'UBSI Kampus Cengkareng',
                'alamat' => 'Jl. Raya Daan Mogot KM.11, Cengkareng',
                'kota' => 'Jakarta Barat',
            ],
            [
                'kode_kampus' => 'KMP-SLP',
                'nama_kampus' => 'UBSI Kampus Slipi',
                'alamat' => 'Jl. Kemanggisan Utama No.18, Palmerah',
                'kota' => 'Jakarta Barat',
            ],
            [
                'kode_kampus' => 'KMP-FTM',
                'nama_kampus' => 'UBSI Kampus Fatmawati',
                'alamat' => 'Jl. RS Fatmawati No.24, Cilandak',
                'kota' => 'Jakarta Selatan',
            ],
            [
                'kode_kampus' => 'KMP-CLD',
                'nama_kampus' => 'UBSI Kampus Ciledug',
                'alamat' => 'Jl. Ciledug Raya No.108, Petukangan Utara',
                'kota' => 'Jakarta Selatan / Tangerang',
            ],
            [
                'kode_kampus' => 'KMP-MGD',
                'nama_kampus' => 'UBSI Kampus Margonda (Depok)',
                'alamat' => 'Jl. Margonda Raya No.8, Pondok Cina',
                'kota' => 'Kota Depok',
            ],
            [
                'kode_kampus' => 'KMP-CMT',
                'nama_kampus' => 'UBSI Kampus Cut Mutia (Bekasi)',
                'alamat' => 'Jl. Cut Mutia No.88, Sepanjang Jaya, Rawalumbu',
                'kota' => 'Kota Bekasi',
            ],
            [
                'kode_kampus' => 'KMP-KLB',
                'nama_kampus' => 'UBSI Kampus Kaliabang (Bekasi)',
                'alamat' => 'Jl. Kaliabang No.8, Perwira, Kec. Bekasi Utara',
                'kota' => 'Kota Bekasi',
            ],
            [
                'kode_kampus' => 'KMP-JTW',
                'nama_kampus' => 'UBSI Kampus Jatiwaringin (Bekasi)',
                'alamat' => 'Jl. Raya Jatiwaringin No.18, Pondok Gede',
                'kota' => 'Kota Bekasi',
            ],
            [
                'kode_kampus' => 'KMP-CBT',
                'nama_kampus' => 'UBSI Kampus Cibitung (Bekasi)',
                'alamat' => 'Jl. Raya Teuku Umar No.15, Cibitung',
                'kota' => 'Kabupaten Bekasi',
            ],
            [
                'kode_kampus' => 'KMP-CKG',
                'nama_kampus' => 'UBSI Kampus Cikarang (Bekasi)',
                'alamat' => 'Jl. Cikarang Baru Raya, Cikarang Utara',
                'kota' => 'Kabupaten Bekasi',
            ],
            [
                'kode_kampus' => 'KMP-BGR',
                'nama_kampus' => 'UBSI Kampus Bogor',
                'alamat' => 'Jl. KH. Soleh Iskandar No.88, Tanah Sareal',
                'kota' => 'Kota Bogor',
            ],
            [
                'kode_kampus' => 'KMP-TNG',
                'nama_kampus' => 'UBSI Kampus Tangerang',
                'alamat' => 'Jl. Gatot Subroto KM.8, Jatiuwung',
                'kota' => 'Kota Tangerang',
            ],
            [
                'kode_kampus' => 'KMP-BSD',
                'nama_kampus' => 'UBSI Kampus BSD (Tangerang Selatan)',
                'alamat' => 'Rawa Mekar Jaya, Serpong',
                'kota' => 'Kota Tangerang Selatan',
            ],
            [
                'kode_kampus' => 'KMP-CPT',
                'nama_kampus' => 'UBSI Kampus Ciputat (Tangerang Selatan)',
                'alamat' => 'Jl. Ir. H. Juanda No.39, Ciputat',
                'kota' => 'Kota Tangerang Selatan',
            ],

            // 2. Kampus PSDKU (Luar Kampus Utama)
            [
                'kode_kampus' => 'KMP-SKB',
                'nama_kampus' => 'UBSI Kampus Sukabumi',
                'alamat' => 'Jl. Veteran II No.64, Selabatu, Cikole',
                'kota' => 'Kota Sukabumi',
            ],
            [
                'kode_kampus' => 'KMP-KRW',
                'nama_kampus' => 'UBSI Kampus Karawang',
                'alamat' => 'Jl. Banten No.1, Karangpawitan, Karawang Barat',
                'kota' => 'Kabupaten Karawang',
            ],
            [
                'kode_kampus' => 'KMP-CKP',
                'nama_kampus' => 'UBSI Kampus Cikampek',
                'alamat' => 'Jl. Ir. H. Djuanda No.17, Cikampek',
                'kota' => 'Kabupaten Karawang',
            ],
            [
                'kode_kampus' => 'KMP-TSM',
                'nama_kampus' => 'UBSI Kampus Tasikmalaya',
                'alamat' => 'Jl. Tanuwijaya No.4, Empangsari, Tawang',
                'kota' => 'Kota Tasikmalaya',
            ],
            [
                'kode_kampus' => 'KMP-TGL',
                'nama_kampus' => 'UBSI Kampus Tegal',
                'alamat' => 'Jl. Sipelem No.22, Kraton, Tegal Barat',
                'kota' => 'Kota Tegal',
            ],
            [
                'kode_kampus' => 'KMP-PWK',
                'nama_kampus' => 'UBSI Kampus Purwokerto',
                'alamat' => 'Jl. HR. Boenyamin No.106, Pabuaran',
                'kota' => 'Kabupaten Banyumas',
            ],
            [
                'kode_kampus' => 'KMP-SKT',
                'nama_kampus' => 'UBSI Kampus Surakarta (Solo)',
                'alamat' => 'Jl. Letjen Sutoyo No.43, Cengklik, Nusukan',
                'kota' => 'Kota Surakarta',
            ],
            [
                'kode_kampus' => 'KMP-YOG',
                'nama_kampus' => 'UBSI Kampus Yogyakarta',
                'alamat' => 'Jl. Ring Road Barat No.8, Gamping',
                'kota' => 'Kabupaten Sleman / D.I. Yogyakarta',
            ],
            [
                'kode_kampus' => 'KMP-PTK',
                'nama_kampus' => 'UBSI Kampus Pontianak',
                'alamat' => 'Jl. Abdurrahman Saleh No.18A',
                'kota' => 'Kota Pontianak',
            ],
        ];

        foreach ($campuses as $campus) {
            Kampus::updateOrCreate(
                ['kode_kampus' => $campus['kode_kampus']],
                array_merge($campus, ['status' => 'aktif'])
            );
        }
    }
}
