@extends('layouts.app')

@section('title', 'Kelola Kampus UBSI')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ modalTambah: false, editModal: false, editData: {} }">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-school-flag text-amber-500"></i>
                Manajemen Multi-Kampus UBSI
            </h1>
            <p class="text-xs text-slate-500 mt-1">Kelola unit Kampus Utama Jabodetabek dan PSDKU seluruh Indonesia</p>
        </div>

        <button @click="modalTambah = true" 
                class="px-4 py-2.5 rounded-xl bg-brand-ubsi hover:bg-blue-900 text-white text-xs font-bold shadow-md shadow-blue-900/10 transition-all flex items-center gap-2">
            <i class="fa-solid fa-plus text-amber-300"></i>
            <span>Tambah Kampus Baru</span>
        </button>
    </div>

    <!-- Search Form -->
    <div class="flex items-center justify-between">
        <form action="{{ route('admin.kampus') }}" method="GET" class="flex items-center gap-2 max-w-md w-full">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama kampus / kota / kode..." 
                   class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-blue-500">
            <button type="submit" class="px-4 py-2.5 bg-slate-900 text-white text-xs font-bold rounded-xl hover:bg-slate-800">Cari</button>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Kode</th>
                        <th class="py-4 px-6">Nama Kampus UBSI</th>
                        <th class="py-4 px-6">Kota / Wilayah</th>
                        <th class="py-4 px-6">Alamat</th>
                        <th class="py-4 px-6 text-center">Laporan Aktif</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($campuses as $kmp)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900">{{ $kmp->kode_kampus }}</td>
                            <td class="py-4 px-6 font-bold text-slate-900">{{ $kmp->nama_kampus }}</td>
                            <td class="py-4 px-6 font-medium text-slate-700">{{ $kmp->kota }}</td>
                            <td class="py-4 px-6 max-w-xs text-slate-500 truncate">{{ $kmp->alamat ?? '-' }}</td>
                            <td class="py-4 px-6 text-center font-bold text-blue-600">
                                {{ $kmp->laporan_barang_count }} Laporan
                            </td>
                            <td class="py-4 px-6">
                                @if($kmp->status === 'aktif')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase">Aktif</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 uppercase">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="editData = {{ json_encode($kmp) }}; editModal = true" 
                                            class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('admin.kampus.toggle', $kmp->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs">
                                            {{ $kmp->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-400">Tidak ada kampus yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $campuses->links() }}
        </div>
    </div>

    <!-- Modal Tambah Kampus -->
    <div x-show="modalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalTambah = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Tambah Unit Kampus UBSI Baru</h3>
                <button @click="modalTambah = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form action="{{ route('admin.kampus.simpan') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Kampus (Unik) <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_kampus" required placeholder="Contoh: KMP-SMG" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Kampus UBSI <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_kampus" required placeholder="Contoh: UBSI Kampus Semarang" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kota / Kabupaten <span class="text-rose-500">*</span></label>
                        <input type="text" name="kota" required placeholder="Kota" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" placeholder="Jalan, RT/RW, Kecamatan..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="modalTambah = false" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-bold text-slate-700">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-ubsi text-xs font-bold text-white hover:bg-blue-900">Simpan Kampus</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Kampus -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="editModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Edit Data Kampus</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="'/admin/kampus/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Kampus</label>
                    <input type="text" name="kode_kampus" :value="editData.kode_kampus" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Kampus UBSI</label>
                    <input type="text" name="nama_kampus" :value="editData.nama_kampus" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kota</label>
                        <input type="text" name="kota" :value="editData.kota" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
                            <option value="aktif" :selected="editData.status === 'aktif'">Aktif</option>
                            <option value="nonaktif" :selected="editData.status === 'nonaktif'">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat</label>
                    <textarea name="alamat" rows="2" :value="editData.alamat" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none"></textarea>
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
