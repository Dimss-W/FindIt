@extends('layouts.app')

@section('title', 'Ajukan Klaim Kepemilikan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('mahasiswa.dashboard') }}" class="hover:text-slate-600">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <a href="{{ route('mahasiswa.laporan.detail', $laporan->id) }}" class="hover:text-slate-600">{{ $laporan->kode_laporan }}</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Ajukan Klaim</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-key text-amber-500"></i>
                Pengajuan Klaim Kepemilikan Barang
            </h1>
        </div>
        <a href="{{ route('mahasiswa.laporan.detail', $laporan->id) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">
            &larr; Batal & Kembali
        </a>
    </div>

    <!-- Banner Info Barang yang Diklaim -->
    <div class="bg-gradient-to-r from-blue-900 to-brand-ubsi p-6 rounded-3xl text-white shadow-md flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center text-amber-300 text-2xl font-bold flex-shrink-0">
            <i class="fa-solid {{ $laporan->kategori->icon }}"></i>
        </div>
        <div class="min-w-0 flex-1">
            <span class="text-[10px] font-mono font-bold text-blue-200 bg-white/10 px-2 py-0.5 rounded">
                {{ $laporan->kode_laporan }} • {{ $laporan->kampus->nama_kampus }}
            </span>
            <h2 class="text-xl font-black tracking-tight mt-1 text-white truncate">{{ $laporan->nama_barang }}</h2>
            <p class="text-xs text-blue-200 line-clamp-1 mt-0.5">Lokasi temuan: {{ $laporan->lokasi_kejadian }}</p>
        </div>
    </div>

    <!-- Alert Ketentuan Klaim -->
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-900 text-xs flex items-start gap-3">
        <i class="fa-solid fa-shield-halved text-amber-600 text-base mt-0.5 flex-shrink-0"></i>
        <div class="space-y-1">
            <span class="font-bold block">Verifikasi Ketat Petugas Keamanan:</span>
            <p class="leading-relaxed">
                Untuk mencegah penyalahgunaan, Anda wajib memberikan bukti atau ciri unik barang yang tidak dipublikasikan ke umum (misal: isi dalam dompet, wallpaper handphone, goresan khusus, atau menunjukkan KTM).
            </p>
        </div>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('mahasiswa.klaim.submit', $laporan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Deskripsi Ciri Khusus -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Deskripsi Ciri Khusus / Tanda Pengenal Unik <span class="text-rose-500">*</span>
                </label>
                <textarea name="deskripsi_klaim" rows="3" required 
                          placeholder="Contoh: Dompet memiliki goresan kecil di bagian belakang dan terdapat kartu mahasiswa atas nama saya, ada uang tunai pecahan..." 
                          class="w-full px-4 py-3 bg-slate-50 border @error('deskripsi_klaim') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">{{ old('deskripsi_klaim') }}</textarea>
                @error('deskripsi_klaim')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bukti Kepemilikan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Informasi & Bukti Kepemilikan <span class="text-rose-500">*</span>
                </label>
                <textarea name="bukti_kepemilikan" rows="3" required 
                          placeholder="Jelaskan bukti yang Anda miliki (contoh: nomor seri perangkat, nama di KTM/KTP, nota pembelian, stnk kendaraan, dll.)..." 
                          class="w-full px-4 py-3 bg-slate-50 border @error('bukti_kepemilikan') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">{{ old('bukti_kepemilikan') }}</textarea>
                @error('bukti_kepemilikan')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Upload Foto Bukti Tambahan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Upload Foto Bukti Tambahan (Opsional)
                </label>
                <input type="file" name="foto_bukti" accept="image/*" 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-600 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-100 file:text-brand-ubsi cursor-pointer">
                <p class="text-[11px] text-slate-400 mt-1">Lampirkan foto tanda terima, foto Anda dengan barang tersebut, atau foto kartu identitas (Maks 3MB)</p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('mahasiswa.laporan.detail', $laporan->id) }}" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                    Batal
                </a>
                <button type="submit" class="px-7 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-lg shadow-amber-500/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>KIRIM PENGAJUAN KLAIM</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
