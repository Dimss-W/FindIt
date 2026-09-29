<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'Elektronik',
                'icon' => 'fa-laptop-code',
                'deskripsi' => 'Smartphone, Laptop, Tablet, Charger, Earphone, Smartwatch, Flashdisk, dsb.',
            ],
            [
                'nama_kategori' => 'Tas & Barang Bawaan',
                'icon' => 'fa-bag-shopping',
                'deskripsi' => 'Tas, Ransel, Tote Bag, Koper, Pouch, Tempat Pensil.',
            ],
            [
                'nama_kategori' => 'Dokumen & Identitas',
                'icon' => 'fa-id-card',
                'deskripsi' => 'KTM (Kartu Tanda Mahasiswa), KTP, SIM, STNK, Kartu ATM, Paspor.',
            ],
            [
                'nama_kategori' => 'Barang Pribadi',
                'icon' => 'fa-wallet',
                'deskripsi' => 'Dompet, Gantungan Kunci, Kunci Kendaraan, Jam Tangan, Kacamata, Helm.',
            ],
            [
                'nama_kategori' => 'Pakaian',
                'icon' => 'fa-shirt',
                'deskripsi' => 'Jaket, Almamater UBSI, Hoodie, Topi, Sepatu, Syal, Payung.',
            ],
            [
                'nama_kategori' => 'Perlengkapan Akademik',
                'icon' => 'fa-book-open',
                'deskripsi' => 'Buku Catatan, Modul Kuliah, Diktat, Alat Tulis, Kalkulator Ilmiah.',
            ],
            [
                'nama_kategori' => 'Lainnya',
                'icon' => 'fa-box-open',
                'deskripsi' => 'Barang dan perlengkapan lain yang belum termasuk dalam kategori di atas.',
            ],
        ];

        foreach ($categories as $cat) {
            Kategori::updateOrCreate(
                ['nama_kategori' => $cat['nama_kategori']],
                array_merge($cat, ['status' => 'aktif'])
            );
        }
    }
}
