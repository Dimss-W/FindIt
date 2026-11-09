<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label_Inventaris_{{ $laporan->kode_laporan }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-ubsi.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- QRCode JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
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
            .label-card {
                box-shadow: none !important;
                border: 2px dashed #000 !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body class="py-10 px-4 flex flex-col items-center justify-center min-h-screen">

    <!-- Action Toolbar (Tampil di layar, tersembunyi saat dicetak) -->
    <div class="no-print max-w-xl w-full mb-6 flex items-center justify-between gap-4 p-4 bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="{{ url()->previous() == url()->current() ? route('petugas.inventaris.hub') : url()->previous() }}" 
               class="px-3.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <div>
                <h4 class="text-xs font-bold text-slate-900">Format Label Inventaris Fisik (Hangtag)</h4>
                <p class="text-[11px] text-slate-500">Ditempel pada barang temuan atau loker penyimpanan</p>
            </div>
        </div>

        <button onclick="window.print()" class="px-4 py-2 rounded-lg bg-blue-900 hover:bg-blue-950 text-white text-xs font-bold shadow-sm transition-all flex items-center gap-2">
            <i class="fa-solid fa-print text-amber-300"></i>
            <span>Cetak Label (Stiker/Hangtag)</span>
        </button>
    </div>

    <!-- Printable Hangtag Card (Standard Label 100mm x 75mm approx) -->
    <div class="label-card max-w-xl w-full bg-white border-2 border-dashed border-slate-400 p-6 rounded-xl shadow-md text-slate-900 relative">
        
        <!-- Perforation guide text -->
        <div class="no-print text-[10px] text-slate-400 font-mono text-center mb-4 pb-2 border-b border-dashed border-slate-200 flex items-center justify-center gap-2">
            <i class="fa-solid fa-scissors"></i>
            <span>Gunting / potong sepanjang garis putus-putus untuk digantungkan pada barang temuan</span>
        </div>

        <!-- Header Label -->
        <div class="flex items-center gap-3 pb-3 border-b-2 border-slate-900">
            <img src="{{ asset('images/logo-ubsi.png') }}" alt="Logo UBSI" class="w-12 h-12 object-contain flex-shrink-0">
            <div class="flex-1">
                <h2 class="text-xs font-extrabold uppercase tracking-wide text-slate-900 leading-tight">
                    UNIVERSITAS BINA SARANA INFORMATIKA
                </h2>
                <h3 class="text-[11px] font-bold text-slate-700 tracking-tight leading-tight">
                    LABEL INVENTARIS FISIK &bull; LOST & FOUND (FINDIT)
                </h3>
                <p class="text-[10px] text-slate-500">
                    Unit Layanan Kampus & Pos Pengamanan Terpadu
                </p>
            </div>
            <div class="text-right">
                <span class="inline-block px-2 py-0.5 rounded bg-slate-900 text-white font-mono text-[10px] font-bold">
                    {{ $laporan->jenis_laporan }}
                </span>
            </div>
        </div>

        <!-- Body Content Grid -->
        <div class="grid grid-cols-3 gap-4 pt-4 items-center">
            
            <!-- Left Info (2 Cols) -->
            <div class="col-span-2 space-y-2.5 text-xs">
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Kode Registrasi Barang</span>
                    <span class="text-base font-black font-mono text-slate-900 tracking-tight">{{ $laporan->kode_laporan }}</span>
                </div>

                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Nama Barang</span>
                    <span class="text-sm font-bold text-slate-900 block leading-tight">{{ $laporan->nama_barang }}</span>
                    <span class="text-[11px] text-slate-600 block mt-0.5">Kategori: {{ $laporan->kategori->nama_kategori ?? '-' }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-200">
                    <div>
                        <span class="text-[9px] font-bold text-slate-500 uppercase block">Lokasi Temuan</span>
                        <span class="text-[11px] font-semibold text-slate-800 leading-tight block">{{ $laporan->lokasi_kejadian }}</span>
                        <span class="text-[10px] text-slate-500 block">{{ $laporan->kampus->nama_kampus ?? 'Kampus UBSI' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-500 uppercase block">Tanggal Diterima</span>
                        <span class="text-[11px] font-semibold text-slate-800 block">
                            {{ $laporan->penyimpanan?->tanggal_diterima ? $laporan->penyimpanan->tanggal_diterima->translatedFormat('d M Y, H:i') : ($laporan->created_at ? $laporan->created_at->translatedFormat('d M Y') : '-') }}
                        </span>
                    </div>
                </div>

                <div class="p-2 bg-slate-100 rounded-lg border border-slate-200">
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="font-bold text-slate-600">Pos / Loker Simpan:</span>
                        <span class="font-black text-slate-900 uppercase">
                            {{ $laporan->penyimpanan?->lokasi_penyimpanan ?? 'Pos Pengamanan Utama' }}
                        </span>
                    </div>
                    @if($laporan->penyimpanan?->kondisi_barang)
                    <div class="text-[10px] text-slate-500 mt-0.5">
                        Kondisi: <strong>{{ $laporan->penyimpanan->kondisi_barang }}</strong>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right QR Code (1 Col) -->
            <div class="col-span-1 flex flex-col items-center justify-center text-center pl-2 border-l border-slate-200">
                <div id="qrcode" class="p-1.5 bg-white border border-slate-300 rounded-lg inline-block"></div>
                <span class="text-[9px] font-mono text-slate-500 mt-1 block">Scan untuk Verifikasi</span>
                <span class="text-[8px] text-slate-400 block">Petugas: {{ $laporan->penyimpanan?->petugas?->name ?? 'Staf Keamanan' }}</span>
            </div>

        </div>

        <!-- Footer Notice -->
        <div class="mt-4 pt-3 border-t border-slate-200 text-[9px] text-slate-500 flex items-center justify-between">
            <span>Maksimal masa simpan: 30 hari kalender sesuai ketentuan operasional UBSI.</span>
            <span class="font-mono text-slate-400">FINDIT-UBSI-REG</span>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var qrContainer = document.getElementById("qrcode");
            if (qrContainer) {
                new QRCode(qrContainer, {
                    text: "{{ route('mahasiswa.laporan.detail', $laporan->id) }}",
                    width: 96,
                    height: 96,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            }
        });
    </script>
</body>
</html>

