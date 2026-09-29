@extends('layouts.app')

@section('title', 'Statistik & Laporan Lengkap')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    
    <!-- HEADER KHUSUS CETAK RESMI (Hanya muncul saat print) -->
    <div class="hidden print:block mb-6">
        <div class="flex items-center gap-4 pb-3 border-b-2 border-black">
            <img src="{{ asset('images/logo-ubsi.png') }}" alt="Logo Resmi UBSI" class="w-16 h-16 object-contain flex-shrink-0">
            <div class="text-center flex-1">
                <h3 class="text-[12px] tracking-widest font-semibold uppercase">YAYASAN BINA SARANA INFORMATIKA</h3>
                <h1 class="text-lg font-bold tracking-tight uppercase">UNIVERSITAS BINA SARANA INFORMATIKA</h1>
                <p class="text-[11px] font-bold uppercase">PUSAT LAYANAN TERPADU LOST & FOUND (FINDIT)</p>
                <p class="text-[10px] text-gray-600">Jl. Kramat Raya No. 98, Jakarta Pusat &bull; www.bsi.ac.id</p>
            </div>
        </div>
        <div class="text-center mt-3 mb-2">
            <h2 class="text-sm font-bold uppercase tracking-wider underline underline-offset-2">
                LAPORAN REKAPITULASI & STATISTIK SISTEM LOST & FOUND
            </h2>
            <p class="text-[10px] font-mono text-gray-600 mt-0.5">
                Dicetak real-time: {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('D MMMM Y, HH:mm:ss') }} WIB &bull; Administrator: {{ Auth::user()->name }}
            </p>
        </div>
    </div>

    <!-- Screen Header (Disembunyikan saat cetak) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 no-print">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-emerald-600"></i>
                Laporan & Statistik Sistem FINDIT
            </h1>
            <p class="text-xs text-slate-500 mt-1">Rekapitulasi efektivitas penemuan dan pengembalian barang di seluruh 27 unit kampus UBSI</p>
        </div>

        <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm hover:shadow transition-all flex items-center gap-2">
            <i class="fa-solid fa-print text-amber-300"></i>
            <span>Cetak Dokumen Resmi</span>
        </button>
    </div>

    <!-- Ringkasan Metrik (Tampilan Layar & Cetak Rapi) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 print:grid-cols-4 print:gap-2">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center print:border-black print:p-3 print:rounded-none">
            <div class="text-2xl sm:text-3xl font-black text-slate-900 print:text-xl">{{ number_format($totalLaporan) }}</div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1 print:text-[9px] print:text-black">Total Laporan Masuk</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center print:border-black print:p-3 print:rounded-none">
            <div class="text-2xl sm:text-3xl font-black text-rose-600 print:text-xl print:text-black">{{ number_format($totalHilang) }}</div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1 print:text-[9px] print:text-black">Barang Kehilangan</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center print:border-black print:p-3 print:rounded-none">
            <div class="text-2xl sm:text-3xl font-black text-emerald-600 print:text-xl print:text-black">{{ number_format($totalDitemukan) }}</div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1 print:text-[9px] print:text-black">Barang Ditemukan</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center print:border-black print:p-3 print:rounded-none">
            <div class="text-2xl sm:text-3xl font-black text-purple-600 print:text-xl print:text-black">{{ $successRate }}%</div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1 print:text-[9px] print:text-black">Tingkat Pengembalian</div>
        </div>
    </div>

    <!-- Tabel Rekapitulasi per Kampus UBSI -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden print:border-black print:rounded-none print:shadow-none">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between print:p-2 print:border-black">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Rekapitulasi Aktivitas per Kampus UBSI</h3>
                <p class="text-[11px] text-slate-500 mt-0.5 print:hidden">Distribusi barang hilang, temuan, dan status pengembalian di tiap unit kampus</p>
            </div>
            <span class="text-xs font-semibold text-slate-400 font-mono print:text-[9px] print:text-black">27 Kampus Terdata</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse print:text-[10px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider print:bg-gray-100 print:border-black print:text-black">
                        <th class="py-3 px-4 border-r border-slate-200 print:border-black w-14 text-center">Kode</th>
                        <th class="py-3 px-4 border-r border-slate-200 print:border-black">Nama Unit Kampus</th>
                        <th class="py-3 px-4 border-r border-slate-200 print:border-black">Kota / Wilayah</th>
                        <th class="py-3 px-3 text-center border-r border-slate-200 print:border-black w-24">Hilang</th>
                        <th class="py-3 px-3 text-center border-r border-slate-200 print:border-black w-24">Temuan</th>
                        <th class="py-3 px-3 text-center border-r border-slate-200 print:border-black w-28">Dikembalikan</th>
                        <th class="py-3 px-4 text-center font-bold print:border-black w-28">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700 print:divide-black print:text-[10px]">
                    @foreach($kampusStats as $ks)
                        <tr class="hover:bg-slate-50/80 transition-colors print:border-b print:border-black">
                            <td class="py-2.5 px-4 font-mono font-bold text-slate-700 border-r border-slate-100 print:border-black text-center">{{ str_replace('KMP-', '', $ks->kode_kampus) }}</td>
                            <td class="py-2.5 px-4 font-bold text-slate-900 border-r border-slate-100 print:border-black">{{ $ks->nama_kampus }}</td>
                            <td class="py-2.5 px-4 text-slate-600 border-r border-slate-100 print:border-black">{{ $ks->kota }}</td>
                            <td class="py-2.5 px-3 text-center text-rose-600 font-bold border-r border-slate-100 print:border-black print:text-black">{{ $ks->hilang_count }}</td>
                            <td class="py-2.5 px-3 text-center text-emerald-600 font-bold border-r border-slate-100 print:border-black print:text-black">{{ $ks->ditemukan_count }}</td>
                            <td class="py-2.5 px-3 text-center text-purple-600 font-bold border-r border-slate-100 print:border-black print:text-black">{{ $ks->returned_count }}</td>
                            <td class="py-2.5 px-4 text-center font-bold text-blue-900 bg-slate-50/50 print:bg-transparent print:text-black">{{ $ks->laporan_barang_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel Rekapitulasi per Kategori Barang -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden print:border-black print:rounded-none print:shadow-none print:mt-4">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between print:p-2 print:border-black">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Rekapitulasi Berdasarkan Kategori Barang</h3>
                <p class="text-[11px] text-slate-500 mt-0.5 print:hidden">Distribusi frekuensi barang berdasarkan jenis dan kategori inventaris</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse print:text-[10px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider print:bg-gray-100 print:border-black print:text-black">
                        <th class="py-3 px-4 border-r border-slate-200 print:border-black w-10 text-center">No</th>
                        <th class="py-3 px-4 border-r border-slate-200 print:border-black">Kategori Barang</th>
                        <th class="py-3 px-4 border-r border-slate-200 print:border-black">Keterangan / Contoh Barang</th>
                        <th class="py-3 px-4 text-center font-bold print:border-black w-32">Total Laporan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700 print:divide-black print:text-[10px]">
                    @foreach($kategoriStats as $idx => $kats)
                        <tr class="hover:bg-slate-50/80 transition-colors print:border-b print:border-black">
                            <td class="py-2.5 px-4 text-center font-bold border-r border-slate-100 print:border-black">{{ $loop->iteration }}</td>
                            <td class="py-2.5 px-4 font-bold text-slate-900 border-r border-slate-100 print:border-black">
                                <span>{{ $kats->nama_kategori }}</span>
                            </td>
                            <td class="py-2.5 px-4 text-slate-600 text-[11px] border-r border-slate-100 print:border-black">{{ $kats->deskripsi ?? '-' }}</td>
                            <td class="py-2.5 px-4 text-center font-bold text-blue-900 print:text-black">{{ $kats->laporan_barang_count }} Laporan</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- PENGESAHAN DOKUMEN CETAK (Hanya muncul saat print) -->
    <div class="hidden print:block mt-8 pt-4">
        <div class="grid grid-cols-2 text-center text-xs">
            <div class="flex flex-col items-center">
                <p class="text-gray-600">Diverifikasi oleh,</p>
                <p class="font-bold">Administrator Sistem FINDIT,</p>
                <div class="h-16"></div>
                <div class="w-48 border-b border-black"></div>
                <p class="font-bold uppercase mt-1">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-gray-600">Unit Pengelola Sistem Informasi</p>
            </div>
            <div class="flex flex-col items-center">
                <p class="text-gray-600">Jakarta, {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('D MMMM Y') }}</p>
                <p class="font-bold">Mengetahui,</p>
                <p class="text-[11px] text-gray-700">Kepala Unit Layanan Terpadu Kampus,</p>
                <div class="h-16"></div>
                <div class="w-48 border-b border-black"></div>
                <p class="font-bold uppercase mt-1">Koordinator Layanan UBSI</p>
                <p class="text-[10px] text-gray-600">Universitas Bina Sarana Informatika</p>
            </div>
        </div>

        <div class="mt-8 pt-2 border-t border-gray-400 flex items-center justify-between text-[9px] text-gray-500 font-mono">
            <span>FINDIT UBSI &bull; Laporan Resmi Terverifikasi Universitas Bina Sarana Informatika</span>
            <span>Halaman 1 dari 1</span>
        </div>
    </div>

</div>
@endsection
