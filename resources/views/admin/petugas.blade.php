@extends('layouts.app')

@section('title', 'Kelola Admin Layanan Kampus')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ modalTambah: false, editModal: false, editData: {} }">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-user-shield text-emerald-600"></i>
                Manajemen Admin Layanan Kampus
            </h1>
            <p class="text-xs text-slate-500 mt-1">Kelola akun admin penerima, verifikator kondisi fisik, dan petugas penyerahan barang di unit kampus UBSI</p>
        </div>

        <button @click="modalTambah = true" 
                class="px-4 py-2.5 rounded-xl bg-brand-ubsi hover:bg-blue-900 text-white text-xs font-bold shadow-md shadow-blue-900/10 transition-all flex items-center gap-2">
            <i class="fa-solid fa-user-plus text-amber-300"></i>
            <span>Tambah Admin Kampus</span>
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Nama Admin / Petugas</th>
                        <th class="py-4 px-6">ID / No. Induk</th>
                        <th class="py-4 px-6">Email Login</th>
                        <th class="py-4 px-6">No. Telepon</th>
                        <th class="py-4 px-6">Penempatan Kampus UBSI</th>
                        <th class="py-4 px-6">Status Akun</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($officers as $petugas)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-bold text-slate-900">{{ $petugas->name }}</td>
                            <td class="py-4 px-6 font-mono font-bold text-slate-700">{{ $petugas->nim ?? '-' }}</td>
                            <td class="py-4 px-6 text-slate-600">{{ $petugas->email }}</td>
                            <td class="py-4 px-6 text-slate-600">{{ $petugas->no_telp ?? '-' }}</td>
                            <td class="py-4 px-6 font-semibold text-brand-ubsi">
                                <i class="fa-solid fa-school mr-1 text-slate-400"></i>
                                {{ $petugas->kampus->nama_kampus ?? 'Belum Ditugaskan' }}
                            </td>
                            <td class="py-4 px-6">
                                @if($petugas->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200 uppercase">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="editData = {{ json_encode($petugas) }}; editModal = true" 
                                            class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('admin.petugas.toggle', $petugas->id) }}" class="inline">
                                        @csrf
                                        @if($petugas->is_active)
                                            <button type="submit" onclick="return confirm('Nonaktifkan petugas ini?');" 
                                                    class="px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white font-bold text-xs transition-colors">
                                                Nonaktifkan
                                            </button>
                                        @else
                                            <button type="submit" onclick="return confirm('Aktifkan kembali petugas ini?');" 
                                                    class="px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white font-bold text-xs transition-colors">
                                                Aktifkan
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-400">Belum ada akun petugas terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $officers->links() }}
        </div>
    </div>

    <!-- Modal Tambah Petugas -->
    <div x-show="modalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalTambah = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Tambah Akun Petugas Baru</h3>
                <button @click="modalTambah = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form action="{{ route('admin.petugas.simpan') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap Petugas <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Nama petugas security" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Login <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required placeholder="petugas@findit.bsi.ac.id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">ID Security / No Induk</label>
                        <input type="text" name="nim" placeholder="SEC-01" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No. HP / WhatsApp <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_telp" required placeholder="0812345678" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kata Sandi <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required placeholder="Min 6 karakter" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Penempatan Kampus UBSI <span class="text-rose-500">*</span></label>
                    <select name="kampus_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-blue-500 cursor-pointer">
                        <option value="">-- Pilih Kampus Penempatan --</option>
                        @foreach($campuses as $kmp)
                            <option value="{{ $kmp->id }}">{{ $kmp->nama_kampus }} ({{ $kmp->kota }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="modalTambah = false" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-bold text-slate-700">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-ubsi text-xs font-bold text-white hover:bg-blue-900">Simpan Petugas</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Petugas -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="editModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Edit Data Petugas</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="'/admin/petugas/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" name="name" :value="editData.name" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email</label>
                        <input type="email" name="email" :value="editData.email" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">ID Security</label>
                        <input type="text" name="nim" :value="editData.nim" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No. HP</label>
                        <input type="text" name="no_telp" :value="editData.no_telp" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Ganti Password (Opsional)</label>
                        <input type="password" name="password" placeholder="Biarkan kosong jika tidak diubah" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Penempatan Kampus</label>
                    <select name="kampus_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none cursor-pointer">
                        @foreach($campuses as $kmp)
                            <option value="{{ $kmp->id }}" :selected="editData.kampus_id == {{ $kmp->id }}">
                                {{ $kmp->nama_kampus }} ({{ $kmp->kota }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-bold text-slate-700">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-ubsi text-xs font-bold text-white hover:bg-blue-900">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
