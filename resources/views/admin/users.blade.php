@extends('layouts.app')

@section('title', 'Kelola Data Mahasiswa')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ modalTambah: false, modalImport: false }">
    
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-users text-blue-600"></i>
                Manajemen Data Mahasiswa
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola master data mahasiswa, tambah manual, atau import massal via file Excel / CSV
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Unduh Template -->
            <a href="{{ route('admin.users.template') }}" 
               class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-200 transition-all flex items-center gap-1.5 shadow-sm"
               title="Unduh Format File Excel / CSV">
                <i class="fa-solid fa-file-excel text-emerald-600"></i>
                <span>Template Excel (.xlsx)</span>
            </a>

            <!-- Import Excel -->
            <button type="button" 
                    @click="modalImport = true"
                    class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-emerald-200"></i>
                <span>Import Excel</span>
            </button>

            <!-- Tambah Mahasiswa Manual -->
            <button type="button" 
                    @click="modalTambah = true" 
                    class="px-4 py-2.5 rounded-xl bg-brand-ubsi hover:bg-blue-900 text-white text-xs font-bold shadow-md shadow-blue-900/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-amber-300"></i>
                <span>Tambah Mahasiswa</span>
            </button>
        </div>
    </div>

    <!-- Info Banner: Format Login Mahasiswa -->
    <div class="p-4 rounded-2xl bg-blue-50/80 border border-blue-200/80 text-blue-900 text-xs flex items-start gap-3">
        <i class="fa-solid fa-circle-info text-blue-600 text-base mt-0.5 flex-shrink-0"></i>
        <div class="space-y-0.5">
            <span class="font-bold block text-blue-950">Aturan Login Mahasiswa:</span>
            <p class="leading-relaxed text-[11px] text-blue-800">
                Mahasiswa login hanya menggunakan <strong>NIM</strong> (Username) dan kata sandi berupa <strong>Tanggal Lahir</strong> dengan format <strong><code>YYYY-MM-DD</code></strong> (contoh: <code>2004-05-14</code>).
            </p>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('admin.users') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIM, atau email mahasiswa..." 
                       class="w-full pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500 transition-colors">
            </div>
            
            <select name="kampus_id" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:bg-white focus:outline-none focus:border-blue-500">
                <option value="">Semua Kampus UBSI</option>
                @foreach($campuses as $kmp)
                    <option value="{{ $kmp->id }}" {{ request('kampus_id') == $kmp->id ? 'selected' : '' }}>
                        {{ $kmp->nama_kampus }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-xs font-bold rounded-xl hover:bg-slate-800 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-filter text-xs"></i>
                <span>Terapkan</span>
            </button>

            @if(request('q') || request('kampus_id'))
                <a href="{{ route('admin.users') }}" class="px-3 py-2 bg-slate-100 text-slate-600 hover:text-slate-900 text-xs font-semibold rounded-xl">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Nama Mahasiswa</th>
                        <th class="py-4 px-6">NIM</th>
                        <th class="py-4 px-6">Tgl Lahir (Password)</th>
                        <th class="py-4 px-6">Email & Kontak</th>
                        <th class="py-4 px-6">Kampus Terdaftar</th>
                        <th class="py-4 px-6">Status Akun</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                <div class="text-[10px] text-slate-400">Terdaftar: {{ $u->created_at ? $u->created_at->format('d/m/Y') : '-' }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-1 rounded-lg border border-slate-200">
                                    {{ $u->nim ?? '-' }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @if($u->tanggal_lahir)
                                    <div class="font-mono font-bold text-blue-700 bg-blue-50 px-2 py-1 rounded-lg border border-blue-200 inline-block">
                                        {{ $u->tanggal_lahir->format('Y-m-d') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $u->tanggal_lahir->translatedFormat('d F Y') }}</div>
                                @else
                                    <span class="text-slate-400 italic">Belum diatur</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                <div class="font-medium text-slate-800">{{ $u->email }}</div>
                                <div class="text-[11px] text-slate-400">{{ $u->no_telp ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-800">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-school text-slate-400 text-xs"></i>
                                    <span>{{ $u->kampus->nama_kampus ?? 'Belum Diatur' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @if($u->is_active)
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
                                <form method="POST" action="{{ route('admin.users.toggle', $u->id) }}" class="inline">
                                    @csrf
                                    @if($u->is_active)
                                        <button type="submit" onclick="return confirm('Nonaktifkan akun mahasiswa ini?');" 
                                                class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white font-bold text-xs transition-colors">
                                            Nonaktifkan
                                        </button>
                                    @else
                                        <button type="submit" onclick="return confirm('Aktifkan kembali akun ini?');" 
                                                class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white font-bold text-xs transition-colors">
                                            Aktifkan
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="fa-solid fa-users-slash text-3xl text-slate-300"></i>
                                    <span class="text-xs">Tidak ada data mahasiswa ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 1: TAMBAH MAHASISWA MANUAL           -->
    <!-- ========================================== -->
    <div x-show="modalTambah" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        
        <div @click.away="modalTambah = false" 
             class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
            
            <div class="p-6 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-brand-ubsi flex items-center justify-center font-bold">
                        <i class="fa-solid fa-user-plus text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Tambah Mahasiswa Baru</h3>
                        <p class="text-[11px] text-slate-400">Password otomatis = Tanggal Lahir (YYYY-MM-DD)</p>
                    </div>
                </div>
                <button type="button" @click="modalTambah = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('admin.users.simpan') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <!-- Nama Mahasiswa -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nama Lengkap Mahasiswa <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" required placeholder="Contoh: Dimas Arya Pratama" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none transition-colors">
                </div>

                <!-- NIM & Tanggal Lahir (Grid 2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            NIM (Login Mahasiswa) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nim" required placeholder="Contoh: 12220199" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Tanggal Lahir (Password) <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal_lahir" required 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none transition-colors">
                    </div>
                </div>

                <!-- Kampus Pilihan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Kampus UBSI Terdaftar <span class="text-rose-500">*</span>
                    </label>
                    <select name="kampus_id" required 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none transition-colors">
                        <option value="">-- Pilih Kampus UBSI --</option>
                        @foreach($campuses as $kmp)
                            <option value="{{ $kmp->id }}">{{ $kmp->nama_kampus }} ({{ $kmp->kota }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Email Resmi UBSI & No Telp (Grid 2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Email Resmi UBSI
                        </label>
                        <div class="px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs text-slate-700 font-mono flex items-center justify-between">
                            <span class="truncate">Otomatis: <strong>{NIM}@bsi.ac.id</strong></span>
                            <span class="text-[9px] font-sans text-blue-700 font-bold bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 flex-shrink-0">UBSI</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            No. WhatsApp / HP (Opsional)
                        </label>
                        <input type="text" name="no_telp" placeholder="Contoh: 085712345678" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none transition-colors">
                    </div>
                </div>

                <!-- Info Box -->
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-[11px] flex items-center gap-2">
                    <i class="fa-solid fa-key text-amber-500 text-sm flex-shrink-0"></i>
                    <span>Kata sandi default login mahasiswa adalah <strong>Tanggal Lahir</strong> dengan format <code>YYYY-MM-DD</code> (contoh: <code>2004-05-14</code>).</span>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalTambah = false" 
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-brand-ubsi hover:bg-blue-900 text-white text-xs font-bold shadow-md shadow-blue-900/20 transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i>
                        <span>Simpan Mahasiswa</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: IMPORT EXCEL / CSV MAHASISWA      -->
    <!-- ========================================== -->
    <div x-show="modalImport" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        
        <div @click.away="modalImport = false" 
             class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
            
            <div class="p-6 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-file-excel text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Import Data Mahasiswa dari Excel</h3>
                        <p class="text-[11px] text-slate-400">Sistem otomatis membaca data mahasiswa & kampus terdaftar</p>
                    </div>
                </div>
                <button type="button" @click="modalImport = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf

                <!-- File Input -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pilih File Excel (.xlsx / .csv) <span class="text-rose-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-5 text-center bg-slate-50/50 transition-colors">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-emerald-600 mb-2"></i>
                        <input type="file" name="file_excel" required accept=".xlsx,.xls,.csv,.txt" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-2">Maksimal ukuran file: 10 MB</p>
                    </div>
                </div>

                <!-- Kampus Cadangan (Fallback jika kolom kampus kosong di file) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Kampus Cadangan (Hanya digunakan jika kolom kampus di Excel kosong)
                    </label>
                    <select name="default_kampus_id" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-emerald-500 focus:outline-none transition-colors">
                        @foreach($campuses as $kmp)
                            <option value="{{ $kmp->id }}">{{ $kmp->nama_kampus }} ({{ $kmp->kota }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- 5 Kolom yang dibaca sistem -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
                    <span class="font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-table-columns text-emerald-600"></i>
                        5 Kolom yang Dibaca Sistem dari Excel:
                    </span>
                    <ul class="text-[11px] text-slate-600 list-disc list-inside space-y-1 ml-1">
                        <li><strong>Kolom A:</strong> Nama Mahasiswa</li>
                        <li><strong>Kolom B:</strong> NIM Mahasiswa (Username Login & Email: <code>nim@bsi.ac.id</code>)</li>
                        <li><strong>Kolom C:</strong> Tanggal Lahir (Password Login, Format: <code>YYYY-MM-DD</code>)</li>
                        <li><strong>Kolom D:</strong> Kampus Terdaftar (Asal Kampus UBSI, contoh: <code>Kramat 98</code>, <code>Kaliabang</code>, <code>Margonda</code>, dll.)</li>
                        <li><strong>Kolom E:</strong> No WhatsApp Aktif (Format: <code>08xxxxxxxxxx</code> untuk Notifikasi Otomatis)</li>
                    </ul>
                </div>

                <!-- Download Template Button -->
                <div class="flex items-center justify-between pt-1">
                    <a href="{{ route('admin.users.template') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 flex items-center gap-1.5 bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-200/80 transition-colors">
                        <i class="fa-solid fa-file-excel text-emerald-600"></i>
                        <span>Download Template Excel 5 Kolom (Bersih Tanpa Dummy)</span>
                    </a>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalImport = false" 
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-upload"></i>
                        <span>Mulai Import</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
