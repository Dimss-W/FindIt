@extends('layouts.app')

@section('title', 'Verifikasi & Amankan Barang Fisik')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('petugas.dashboard') }}" class="hover:text-slate-600">Dashboard Petugas</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <a href="{{ route('petugas.menunggu.verifikasi') }}" class="hover:text-slate-600">Menunggu Verifikasi</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-600">{{ $laporan->kode_laporan }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-vault text-emerald-600"></i>
                Verifikasi Fisik & Catat Penyimpanan Barang
            </h1>
        </div>
        <a href="{{ route('petugas.menunggu.verifikasi') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">
            &larr; Kembali
        </a>
    </div>

    <!-- Info Barang yang Diserahkan -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <span class="text-[10px] font-mono font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">
                    {{ $laporan->kode_laporan }}
                </span>
                <h2 class="text-xl font-bold text-slate-900 mt-1">{{ $laporan->nama_barang }}</h2>
                <p class="text-xs text-slate-500">Diserahkan oleh: <strong>{{ $laporan->user->name }}</strong> (NIM: {{ $laporan->user->nim ?? '-' }})</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 uppercase">
                Menunggu Verifikasi
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block text-[11px]">Kategori:</span>
                <span class="font-semibold text-slate-800">{{ $laporan->kategori->nama_kategori }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Lokasi Ditemukan:</span>
                <span class="font-semibold text-slate-800">{{ $laporan->lokasi_kejadian }}</span>
            </div>
            <div class="col-span-2">
                <span class="text-slate-400 block text-[11px]">Deskripsi dari Penemu:</span>
                <p class="text-slate-600 italic bg-slate-50 p-2.5 rounded-xl border border-slate-100 mt-0.5">
                    "{{ $laporan->deskripsi }}"
                </p>
            </div>
            @if($laporan->ciri_khusus)
                <div class="col-span-2">
                    <span class="text-slate-400 block text-[11px]">Ciri-ciri Khusus yang Dicatat:</span>
                    <p class="text-slate-700 font-medium mt-0.5">{{ $laporan->ciri_khusus }}</p>
                </div>
            @endif

            <!-- Foto Barang yang Diunggah Penemu (Minimal 2 Foto) -->
            @if(count($laporan->foto_list) > 0)
                <div class="col-span-2 pt-3 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-camera text-blue-600"></i>
                            <span>Foto Fisik Dilampirkan Penemu ({{ count($laporan->foto_list) }} Foto)</span>
                        </span>
                        <span class="text-[11px] text-emerald-700 font-semibold bg-emerald-50 px-2.5 py-0.5 rounded-full">
                            Memenuhi Syarat Foto
                        </span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($laporan->foto_list as $index => $foto)
                            <a href="{{ asset('storage/' . $foto) }}" target="_blank" class="relative group rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 aspect-square shadow-sm block">
                                <img src="{{ asset('storage/' . $foto) }}" alt="Foto #{{ $index + 1 }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <span class="absolute bottom-1.5 left-1.5 bg-black/60 text-white text-[10px] font-bold px-2 py-0.5 rounded backdrop-blur-xs">
                                    Foto #{{ $index + 1 }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Klik foto untuk melihat ukuran penuh pada tab baru.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Form Pengamanan Fisik -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <div class="mb-5 pb-4 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-check text-blue-600"></i>
                Formulir Penerimaan & Penyimpanan Fisik Admin Layanan Kampus
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Pastikan barang telah Anda terima secara fisik sebelum mengubah status menjadi <strong>BARANG DIAMANKAN</strong>.
            </p>
        </div>

        <form action="{{ route('petugas.verifikasi.simpan', $laporan->id) }}" method="POST" class="space-y-5">
            @csrf

            <!-- Lokasi Penyimpanan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Lokasi Penyimpanan Barang Fisik <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="lokasi_penyimpanan" value="{{ old('lokasi_penyimpanan') }}" required 
                       placeholder="Contoh: Pos Security Gedung A - Lemari 2 / Brankas Lost & Found Laci 03" 
                       class="w-full px-4 py-3 bg-slate-50 border @error('lokasi_penyimpanan') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                @error('lokasi_penyimpanan')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-slate-400 mt-1">Tuliskan nomor pos, laci, atau rak penyimpanan secara spesifik untuk memudahkan penyerahan.</p>
            </div>

            <!-- Kondisi Barang -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Kondisi Fisik Barang Saat Diterima <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="kondisi_barang" value="{{ old('kondisi_barang', 'Baik, utuh, dan berfungsi normal') }}" required 
                       placeholder="Contoh: Baik / Lecet halus di sudut kanan / Layar retak / dsb." 
                       class="w-full px-4 py-3 bg-slate-50 border @error('kondisi_barang') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                @error('kondisi_barang')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Catatan Admin -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Catatan Khusus Admin / Staf Layanan Kampus
                </label>
                <textarea name="catatan" rows="3" 
                          placeholder="Catatan tambahan seperti identitas penemu, kelengkapan aksesoris, barang berharga di dalamnya, dsb..." 
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">{{ old('catatan') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('petugas.menunggu.verifikasi') }}" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                    Batal
                </a>
                <button type="submit" class="px-7 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-shield-check"></i>
                    <span>SIMPAN & UBAH STATUS KE "BARANG DIAMANKAN"</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
