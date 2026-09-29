@extends('layouts.app')

@section('title', 'Profil Saya - FINDIT UBSI')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    
    <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
            <i class="fa-solid fa-user-gear text-slate-500"></i>
            Pengaturan Akun & Profil
        </h1>
        <p class="text-xs text-slate-500 mt-1">Perbarui data profil kontak, kampus, dan keamanan kata sandi Anda</p>
    </div>

    <!-- User Information Header Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-brand-ubsi text-white flex items-center justify-center font-black text-xl shadow-md overflow-hidden">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                @endif
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                <p class="text-xs text-slate-500">{{ $user->email }} • <span class="capitalize font-bold text-brand-ubsi">{{ $user->role }}</span></p>
                @if($user->kampus)
                    <div class="text-[11px] text-slate-600 font-semibold mt-1">
                        <i class="fa-solid fa-school text-blue-500 mr-1"></i> {{ $user->kampus->nama_kampus }}
                    </div>
                @endif
            </div>
        </div>

        <div>
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $user->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }} uppercase">
                {{ $user->is_active ? 'Akun Aktif' : 'Nonaktif' }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Form Update Data Profil -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Ubah Data Diri</h3>
                <p class="text-xs text-slate-400">Pastikan nomor WhatsApp aktif untuk koordinasi serah terima barang</p>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                </div>

                @if($user->isMahasiswa())
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">NIM (Nomor Induk Mahasiswa)</label>
                        <input type="text" name="nim" value="{{ old('nim', $user->nim) }}" required 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kampus Utama / PSDKU</label>
                        <select name="kampus_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
                            @foreach($campuses as $kmp)
                                <option value="{{ $kmp->id }}" {{ old('kampus_id', $user->kampus_id) == $kmp->id ? 'selected' : '' }}>
                                    {{ $kmp->nama_kampus }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No. WhatsApp / HP</label>
                    <input type="text" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Foto Profil Avatar</label>
                    <input type="file" name="avatar" accept="image/*" 
                           class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:bg-blue-100 file:text-brand-ubsi cursor-pointer">
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-ubsi text-white text-xs font-bold hover:bg-blue-900 transition-all shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Form Update Password -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Ganti Kata Sandi</h3>
                <p class="text-xs text-slate-400">Jaga keamanan akun sistem informasi Anda</p>
            </div>

            <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" required placeholder="••••••••" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kata Sandi Baru</label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi baru" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-all shadow-sm">
                        Ubah Kata Sandi
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
