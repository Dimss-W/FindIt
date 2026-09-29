<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAST_{{ str_replace('/', '_', $pengembalian->nomor_bast) }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-ubsi.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Standar Kertas Formal A4 Dinas */
        @page {
            size: A4 portrait;
            margin: 15mm 20mm 15mm 20mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background-color: #f8fafc;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .sans-ui {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Border double kop surat resmi dinas */
        .kop-line {
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 4px;
            margin: 8px 0 16px 0;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .document-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 auto !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="py-8 px-4 flex flex-col items-center">

    <!-- Action Bar (Hanya tampil di layar, otomatis hilang saat cetak/PDF) -->
    <div class="no-print sans-ui max-w-3xl w-full mb-6 flex items-center justify-between gap-4 p-4 bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="{{ url()->previous() == url()->current() ? route('petugas.riwayat.pengembalian') : url()->previous() }}" 
               class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <div>
                <h4 class="text-xs font-bold text-slate-900">Format Resmi Berita Acara (BAST)</h4>
                <p class="text-[11px] text-slate-500">Standar Surat Dinas Universitas Bina Sarana Informatika</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-blue-900 hover:bg-blue-950 text-white text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-print text-amber-300"></i>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Official Document Sheet (A4) -->
    <div class="document-sheet max-w-3xl w-full bg-white shadow-xl p-12 sm:p-16 border border-slate-200 text-black leading-relaxed">
        
        <!-- KOP SURAT RESMI UNIVERSITAS BINA SARANA INFORMATIKA -->
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/logo-ubsi.png') }}" alt="Logo Resmi UBSI" class="w-20 h-20 object-contain flex-shrink-0">
            <div class="text-center flex-1">
                <h3 class="text-[13px] tracking-widest font-semibold uppercase leading-tight">YAYASAN BINA SARANA INFORMATIKA</h3>
                <h1 class="text-xl font-bold tracking-tight uppercase leading-tight">UNIVERSITAS BINA SARANA INFORMATIKA</h1>
                <p class="text-[12px] font-bold uppercase mt-0.5">
                    UNIT LAYANAN KAMPUS & PUSAT LOST & FOUND (FINDIT)
                </p>
                <p class="text-[11px] leading-tight text-gray-700 mt-1">
                    Kampus {{ $pengembalian->laporan?->kampus?->nama_kampus ?? 'UBSI' }} &bull; {{ $pengembalian->laporan?->kampus?->alamat ?? 'Jl. Kramat Raya No. 98, Senen, Jakarta Pusat' }}
                </p>
                <p class="text-[10px] text-gray-600">
                    Laman Resmi: www.bsi.ac.id | Sistem Layanan: findit.bsi.ac.id
                </p>
            </div>
        </div>

        <!-- Garis Kop Surat Dinas -->
        <div class="kop-line"></div>

        <!-- JUDUL DOKUMEN & NOMOR REGISTRASI -->
        <div class="text-center my-4">
            <h2 class="text-base font-bold uppercase tracking-wider underline underline-offset-4">
                BERITA ACARA SERAH TERIMA BARANG
            </h2>
            <p class="text-xs font-bold mt-1">
                Nomor: {{ $pengembalian->nomor_bast }}
            </p>
        </div>

        <!-- KALIMAT PEMBUKA FORMAL -->
        @php
            $tglSerah = $pengembalian->tanggal_pengembalian ? \Carbon\Carbon::parse($pengembalian->tanggal_pengembalian)->locale('id') : \Carbon\Carbon::now('Asia/Jakarta')->locale('id');
            $hariIndo = $tglSerah->isoFormat('dddd');
            $tanggalIndo = $tglSerah->isoFormat('D MMMM Y');
            $jamIndo = $tglSerah->format('H:i');
        @endphp
        <p class="text-xs text-justify indent-8 my-3 leading-relaxed">
            Pada hari ini, <strong>{{ $hariIndo }}</strong>, tanggal <strong>{{ $tanggalIndo }}</strong>, pukul <strong>{{ $jamIndo }} WIB</strong>, bertempat di Kantor Layanan Kampus <strong>{{ $pengembalian->laporan?->kampus?->nama_kampus ?? 'Universitas Bina Sarana Informatika' }}</strong>, telah dilaksanakan serah terima fisik barang temuan melalui sistem terintegrasi FINDIT UBSI, oleh dan antara pihak-pihak di bawah ini:
        </p>

        <!-- DATA PARA PIHAK (BERSIH & TEPAT) -->
        <div class="my-4 text-xs space-y-3">
            <!-- Pihak Pertama -->
            <div>
                <p class="font-bold">1. PIHAK PERTAMA (Yang Menyerahkan):</p>
                <table class="ml-4 mt-1 w-full text-xs">
                    <tr>
                        <td class="w-32 py-0.5 text-gray-700">Nama Petugas</td>
                        <td class="w-4 py-0.5">:</td>
                        <td class="py-0.5 font-bold uppercase">{{ $pengembalian->petugas?->name ?? 'Petugas Layanan Kampus' }}</td>
                    </tr>
                    <tr>
                        <td class="py-0.5 text-gray-700">Unit Kerja</td>
                        <td class="py-0.5">:</td>
                        <td class="py-0.5">Staf Layanan & Keamanan Kampus {{ $pengembalian->laporan?->kampus?->nama_kampus ?? 'UBSI' }}</td>
                    </tr>
                    <tr>
                        <td class="py-0.5 text-gray-700">No. Registrasi Staf</td>
                        <td class="py-0.5">:</td>
                        <td class="py-0.5 font-mono">{{ $pengembalian->petugas?->nim ?? 'STF-UBSI' }}</td>
                    </tr>
                </table>
            </div>

            <!-- Pihak Kedua -->
            <div>
                <p class="font-bold">2. PIHAK KEDUA (Yang Menerima / Pemilik Sah):</p>
                <table class="ml-4 mt-1 w-full text-xs">
                    <tr>
                        <td class="w-32 py-0.5 text-gray-700">Nama Mahasiswa</td>
                        <td class="w-4 py-0.5">:</td>
                        <td class="py-0.5 font-bold uppercase">{{ $pengembalian->user?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-0.5 text-gray-700">NIM</td>
                        <td class="py-0.5">:</td>
                        <td class="py-0.5 font-mono font-bold">{{ $pengembalian->user?->nim ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-0.5 text-gray-700">No. Kontak / WhatsApp</td>
                        <td class="py-0.5">:</td>
                        <td class="py-0.5">{{ $pengembalian->user?->no_telp ?? ($pengembalian->user?->no_hp ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td class="py-0.5 text-gray-700">Nomor Tiket Klaim</td>
                        <td class="py-0.5">:</td>
                        <td class="py-0.5 font-mono font-bold">{{ $pengembalian->klaim?->kode_tiket ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- KALIMAT PENGANTAR BARANG -->
        <p class="text-xs text-justify my-2 leading-relaxed">
            PIHAK PERTAMA menyerahkan barang temuan kepada PIHAK KEDUA, dan PIHAK KEDUA menyatakan telah menerima barang miliknya dengan rincian sebagai berikut:
        </p>

        <!-- TABEL FORMAL BARANG YANG DISERAHKAN -->
        <div class="my-3">
            <table class="w-full border-collapse border border-black text-xs">
                <thead>
                    <tr class="bg-gray-100 border-b border-black">
                        <th class="border border-black py-1.5 px-2 text-center w-10 font-bold">No</th>
                        <th class="border border-black py-1.5 px-3 text-left font-bold">Nama / Jenis Barang</th>
                        <th class="border border-black py-1.5 px-3 text-left w-36 font-bold">Kategori</th>
                        <th class="border border-black py-1.5 px-3 text-left w-48 font-bold">Kondisi Fisik Barang</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-black py-2 px-2 text-center font-bold">1</td>
                        <td class="border border-black py-2 px-3">
                            <span class="font-bold uppercase">{{ $pengembalian->laporan?->nama_barang }}</span>
                            @if($pengembalian->laporan?->ciri_khusus)
                                <span class="block text-[11px] text-gray-700 mt-0.5 italic">Ciri: {{ $pengembalian->laporan->ciri_khusus }}</span>
                            @endif
                        </td>
                        <td class="border border-black py-2 px-3">
                            {{ $pengembalian->laporan?->kategori?->nama_kategori ?? 'Umum' }}
                        </td>
                        <td class="border border-black py-2 px-3 text-[11px]">
                            {{ $pengembalian->catatan ?: 'Lengkap, utuh, dan sesuai dengan bukti kepemilikan sah.' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- KLAUSUL HUKUM RESMI -->
        <p class="text-[11px] text-justify indent-8 my-3 leading-relaxed">
            Dengan ditandatanganinya Berita Acara ini, maka hak kepemilikan barang telah kembali sepenuhnya kepada PIHAK KEDUA, dan segala tanggung jawab pengamanan barang oleh pihak Universitas Bina Sarana Informatika dinyatakan telah <strong>SELESAI</strong>. Kedua belah pihak menyatakan tidak ada tuntutan apa pun di kemudian hari.
        </p>

        <p class="text-xs my-2">
            Demikian Berita Acara Serah Terima ini dibuat dengan sebenarnya dalam rangkap 2 (dua) untuk dipergunakan sebagaimana mestinya.
        </p>

        <!-- BLOK TANDA TANGAN DUA PIHAK RESMI -->
        <div class="mt-8 text-xs">
            <div class="text-right mb-3 text-xs font-semibold">
                {{ $pengembalian->laporan?->kampus?->kota ?? 'Jakarta' }}, {{ $tanggalIndo }}
            </div>
            <div class="grid grid-cols-2 text-center text-xs">
                <!-- Pihak Kedua -->
                <div class="flex flex-col items-center">
                    <p class="font-bold">PIHAK KEDUA,</p>
                    <p class="text-[11px] text-gray-700">Yang Menerima Barang,</p>
                    
                    <!-- Ruang tanda tangan basah / stempel -->
                    <div class="h-20 flex items-center justify-center text-gray-300 italic text-[10px]">
                        ( Tanda Tangan )
                    </div>

                    <div class="w-48 border-b border-black"></div>
                    <p class="font-bold uppercase mt-1">{{ $pengembalian->user?->name ?? 'Mahasiswa' }}</p>
                    <p class="font-mono text-[11px]">NIM. {{ $pengembalian->user?->nim ?? '-' }}</p>
                </div>

                <!-- Pihak Pertama -->
                <div class="flex flex-col items-center">
                    <p class="font-bold">PIHAK PERTAMA,</p>
                    <p class="text-[11px] text-gray-700">Staf Layanan Kampus UBSI,</p>
                    
                    <!-- Ruang tanda tangan basah / stempel -->
                    <div class="h-20 flex items-center justify-center text-gray-300 italic text-[10px]">
                        ( Tanda Tangan & Stempel )
                    </div>

                    <div class="w-48 border-b border-black"></div>
                    <p class="font-bold uppercase mt-1">{{ $pengembalian->petugas?->name ?? 'Petugas Layanan' }}</p>
                    <p class="text-[11px] text-gray-700">Petugas FINDIT UBSI</p>
                </div>
            </div>
        </div>

        <!-- FOOTER DOKUMEN RESMI (MINIMALIS) -->
        <div class="mt-12 pt-3 border-t border-gray-300 flex items-center justify-between text-[10px] text-gray-500 font-mono">
            <span>Sistem Terpadu Lost & Found &bull; Universitas Bina Sarana Informatika</span>
            <span>Dicetak real-time: {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('D MMMM Y, HH:mm:ss') }} WIB</span>
        </div>

    </div>

</body>
</html>
