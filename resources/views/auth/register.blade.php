@extends('layouts.public')

@section('title', 'Daftar Mahasiswa - FINDIT UBSI')

@section('content')
<div class="min-h-[calc(100vh-140px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl w-full space-y-6">
        
        <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/50">
            <div class="text-center mb-8">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-ubsi to-blue-600 flex items-center justify-center text-white text-2xl font-black mx-auto mb-3 shadow-lg shadow-blue-500/20">
                    <i class="fa-solid fa-user-plus text-amber-300"></i>
                </div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Registrasi Akun Mahasiswa</h2>
                <p class="text-xs text-slate-500 mt-1">Daftarkan diri Anda untuk melacak barang hilang & mengajukan klaim</p>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required 
                               placeholder="Nama sesuai KTM" 
                               class="w-full px-4 py-3 bg-slate-50 border @error('name') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                        @error('name')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NIM -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIM (Nomor Induk Mahasiswa)</label>
                        <input type="text" name="nim" value="{{ old('nim') }}" required 
                               placeholder="Contoh: 12220199" 
                               class="w-full px-4 py-3 bg-slate-50 border @error('nim') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                        @error('nim')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Aktif</label>
                        <input type="email" name="email" value="{{ old('email') }}" required 
                               placeholder="nama@bsi.ac.id / email@gmail.com" 
                               class="w-full px-4 py-3 bg-slate-50 border @error('email') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                        @error('email')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- No WhatsApp / Telp -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. WhatsApp / HP</label>
                        <input type="text" name="no_telp" value="{{ old('no_telp') }}" required 
                               placeholder="Contoh: 081234567890" 
                               class="w-full px-4 py-3 bg-slate-50 border @error('no_telp') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                        @error('no_telp')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Kampus Mahasiswa -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-school text-blue-500 mr-1"></i> Kampus Utama / PSDKU Anda
                    </label>
                    <select name="kampus_id" required 
                            class="w-full px-4 py-3 bg-slate-50 border @error('kampus_id') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all cursor-pointer">
                        <option value="">-- Pilih Kampus UBSI Anda --</option>
                        @foreach($campuses as $kmp)
                            <option value="{{ $kmp->id }}" {{ old('kampus_id') == $kmp->id ? 'selected' : '' }}>
                                {{ $kmp->nama_kampus }} ({{ $kmp->kota }})
                            </option>
                        @endforeach
                    </select>
                    @error('kampus_id')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi</label>
                        <input type="password" name="password" required 
                               placeholder="Minimal 6 karakter" 
                               class="w-full px-4 py-3 bg-slate-50 border @error('password') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                        @error('password')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ulangi Kata Sandi</label>
                        <input type="password" name="password_confirmation" required 
                               placeholder="Ketik ulang kata sandi" 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-2xl bg-brand-ubsi hover:bg-blue-900 text-white text-xs font-bold shadow-lg shadow-blue-900/20 transition-all flex items-center justify-center gap-2 mt-4">
                    <i class="fa-solid fa-user-check"></i>
                    <span>Daftar Sekarang</span>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">
                    Sudah memiliki akun?
                    <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-800">Masuk di sini</a>
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
