@extends('layouts.app')

@section('title', 'Konfirmasi Serah Terima Barang')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('petugas.dashboard') }}" class="hover:text-slate-600">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <a href="{{ route('petugas.barang.siap_diambil') }}" class="hover:text-slate-600">Siap Diambil</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Serah Terima {{ $laporan->kode_laporan }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-champagne-glasses text-purple-600"></i>
                Konfirmasi Penyerahan Barang Fisik
            </h1>
        </div>
        <a href="{{ route('petugas.barang.siap_diambil') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">
            &larr; Kembali
        </a>
    </div>

    <!-- Summary Box -->
    <div class="bg-gradient-to-r from-purple-900 via-indigo-900 to-slate-900 p-6 rounded-3xl text-white shadow-md flex items-center justify-between">
        <div class="space-y-1">
            <span class="text-[10px] font-mono font-bold text-purple-300 bg-white/10 px-2 py-0.5 rounded">
                {{ $laporan->kode_laporan }} • {{ $laporan->kampus->nama_kampus }}
            </span>
            <h2 class="text-xl font-black text-white mt-1">{{ $laporan->nama_barang }}</h2>
            <p class="text-xs text-purple-200">
                Lokasi Simpan: {{ $laporan->penyimpanan->lokasi_penyimpanan ?? 'Pos Security' }}
            </p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-purple-300 text-2xl font-bold">
            <i class="fa-solid fa-handshake"></i>
        </div>
    </div>

    <!-- Form Serah Terima -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
        <div class="pb-4 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Validasi Identitas Penerima</h3>
            <p class="text-xs text-slate-500 mt-0.5">Pastikan mahasiswa telah menunjukkan kartu identitas fisik (KTM atau KTP asli) yang sah sebelum menekan tombol konfirmasi.</p>
        </div>

        <form action="{{ route('petugas.pengembalian.simpan', $laporan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Penerima (Auto selected from approved claim or selectable) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Mahasiswa yang Mengambil Barang <span class="text-rose-500">*</span>
                </label>
                @if($approvedClaim)
                    <input type="hidden" name="penerima_id" value="{{ $approvedClaim->user_id }}">
                    <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200 text-xs text-blue-900 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-sm block">{{ $approvedClaim->user->name }}</span>
                            <span class="text-blue-700">NIM: {{ $approvedClaim->user->nim ?? '-' }} • Telp: {{ $approvedClaim->user->no_telp ?? '-' }}</span>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase">
                            Klaim Terverifikasi Sah
                        </span>
                    </div>
                @else
                    <select name="penerima_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:outline-none">
                        @foreach($laporan->klaim as $cl)
                            <option value="{{ $cl->user_id }}">{{ $cl->user->name }} (NIM: {{ $cl->user->nim }})</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Petugas yang Menyerahkan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Petugas yang Menyerahkan</label>
                    <input type="text" value="{{ Auth::user()->name }} (Pos Security {{ Auth::user()->kampus->nama_kampus }})" disabled 
                           class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-2xl text-xs text-slate-600 font-medium">
                </div>

                <!-- Tanggal Pengambilan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Waktu Serah Terima</label>
                    <input type="text" value="{{ now()->translatedFormat('d F Y, H:i') }} WIB" disabled 
                           class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-2xl text-xs text-slate-600 font-medium">
                </div>
            </div>

            <!-- Catatan Serah Terima -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Catatan Berita Acara Penyerahan
                </label>
                <textarea name="catatan" rows="3" 
                          placeholder="Contoh: Barang diserahkan dalam keadaan lengkap dan baik. Pemilik menunjukkan KTM fisik dan memeriksa fungsi barang..." 
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">Barang telah diserahkan di Kantor Layanan Kampus {{ Auth::user()->kampus->nama_kampus ?? 'UBSI' }} dan telah diperiksa langsung oleh pemilik yang bersangkutan dalam keadaan baik.</textarea>
            </div>

            <!-- Foto Bukti Penyerahan (Opsional) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Foto Dokumentasi Serah Terima (Opsional)
                </label>
                <input type="file" name="foto_penyerahan" accept="image/*" 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-600 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-100 file:text-purple-800 hover:file:bg-purple-200 cursor-pointer">
                <p class="text-[11px] text-slate-400 mt-1">Dokumentasi foto serah terima antara petugas dan pemilik</p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('petugas.barang.siap_diambil') }}" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                    Batal
                </a>
                <button type="submit" class="px-7 py-3 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-lg shadow-purple-600/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-check-double"></i>
                    <span>KONFIRMASI BARANG DIKEMBALIKAN</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
