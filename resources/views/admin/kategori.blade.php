@extends('layouts.app')

@section('title', 'Kelola Kategori Barang')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ modalTambah: false, editModal: false, editData: {} }">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-tags text-purple-600"></i>
                Manajemen Kategori Barang
            </h1>
            <p class="text-xs text-slate-500 mt-1">Kelola kategori barang di lingkungan kampus UBSI</p>
        </div>

        <button @click="modalTambah = true" 
                class="px-4 py-2.5 rounded-xl bg-brand-ubsi hover:bg-blue-900 text-white text-xs font-bold shadow-md shadow-blue-900/10 transition-all flex items-center gap-2">
            <i class="fa-solid fa-plus text-amber-300"></i>
            <span>Tambah Kategori</span>
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Icon</th>
                        <th class="py-4 px-6">Nama Kategori</th>
                        <th class="py-4 px-6">Deskripsi & Contoh Item</th>
                        <th class="py-4 px-6 text-center">Jumlah Laporan</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($kategoris as $cat)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6">
                                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-base">
                                    <i class="fa-solid {{ $cat->icon }}"></i>
                                </div>
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-900">{{ $cat->nama_kategori }}</td>
                            <td class="py-4 px-6 max-w-md text-slate-500 text-[11px] leading-relaxed">{{ $cat->deskripsi ?? '-' }}</td>
                            <td class="py-4 px-6 text-center font-bold text-brand-ubsi">
                                {{ $cat->laporan_barang_count }} Barang
                            </td>
                            <td class="py-4 px-6">
                                @if($cat->status === 'aktif')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase">Aktif</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 uppercase">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <button @click="editData = {{ json_encode($cat) }}; editModal = true" 
                                        class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-slate-400">Belum ada kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Kategori -->
    <div x-show="modalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalTambah = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Tambah Kategori Baru</h3>
                <button @click="modalTambah = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form action="{{ route('admin.kategori.simpan') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_kategori" required placeholder="Contoh: Aksesoris Motor" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Icon FontAwesome <span class="text-rose-500">*</span></label>
                        <input type="text" name="icon" required placeholder="fa-tag / fa-laptop" value="fa-tag" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
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
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi / Contoh Barang</label>
                    <textarea name="deskripsi" rows="2" placeholder="Contoh barang dalam kategori ini..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="modalTambah = false" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-bold text-slate-700">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-ubsi text-xs font-bold text-white hover:bg-blue-900">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Kategori -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="editModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Edit Kategori</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="'/admin/kategori/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Kategori</label>
                    <input type="text" name="nama_kategori" :value="editData.nama_kategori" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Icon FontAwesome</label>
                        <input type="text" name="icon" :value="editData.icon" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">
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
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" :value="editData.deskripsi" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none"></textarea>
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
