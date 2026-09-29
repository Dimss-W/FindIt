@extends('layouts.app')

@section('title', 'Pusat Inventaris Barang Kampus')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Page Header & Campus Context -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-blue-50 text-brand-ubsi border border-blue-200 mb-2">
                <i class="fa-solid fa-building-columns"></i>
                {{ $kampus->nama_kampus ?? 'Unit Kampus UBSI' }}
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-brand-ubsi"></i>
                Pusat Inventaris & Pengelolaan Barang
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Semua data di bawah ini diisolasi khusus untuk wilayah operasional <strong>{{ $kampus->nama_kampus ?? 'Kampus Ini' }}</strong>.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('petugas.serah_terima.hub', ['tab' => 'scanner']) }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-qrcode"></i>
                <span>Buka Scanner QR</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards (4 Tabs Shortcut) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="?tab=verifikasi" class="p-4 rounded-2xl border transition-all text-left flex flex-col justify-between {{ $activeTab === 'verifikasi' ? 'bg-amber-500 text-white border-amber-600 shadow-md shadow-amber-500/20' : 'bg-white text-slate-700 border-slate-200/80 hover:border-amber-400' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider {{ $activeTab === 'verifikasi' ? 'text-amber-100' : 'text-slate-400' }}">1. Verifikasi Temuan</span>
                <i class="fa-solid fa-clock-rotate-left {{ $activeTab === 'verifikasi' ? 'text-amber-200' : 'text-amber-500' }}"></i>
            </div>
            <div class="text-2xl font-black mt-2">{{ $counts['verifikasi'] }} <span class="text-xs font-normal opacity-80">item</span></div>
            <span class="text-[10px] mt-1 {{ $activeTab === 'verifikasi' ? 'text-amber-100' : 'text-slate-400' }}">Perlu cek fisik & amankan</span>
        </a>

        <a href="?tab=diamankan" class="p-4 rounded-2xl border transition-all text-left flex flex-col justify-between {{ $activeTab === 'diamankan' ? 'bg-emerald-600 text-white border-emerald-700 shadow-md shadow-emerald-600/20' : 'bg-white text-slate-700 border-slate-200/80 hover:border-emerald-400' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider {{ $activeTab === 'diamankan' ? 'text-emerald-100' : 'text-slate-400' }}">2. Di Loker Kampus</span>
                <i class="fa-solid fa-vault {{ $activeTab === 'diamankan' ? 'text-emerald-200' : 'text-emerald-500' }}"></i>
            </div>
            <div class="text-2xl font-black mt-2">{{ $counts['diamankan'] }} <span class="text-xs font-normal opacity-80">item</span></div>
            <span class="text-[10px] mt-1 {{ $activeTab === 'diamankan' ? 'text-emerald-100' : 'text-slate-400' }}">Tersimpan aman di loker</span>
        </a>

        <a href="?tab=hilang" class="p-4 rounded-2xl border transition-all text-left flex flex-col justify-between {{ $activeTab === 'hilang' ? 'bg-rose-600 text-white border-rose-700 shadow-md shadow-rose-600/20' : 'bg-white text-slate-700 border-slate-200/80 hover:border-rose-400' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider {{ $activeTab === 'hilang' ? 'text-rose-100' : 'text-slate-400' }}">3. Laporan Kehilangan</span>
                <i class="fa-solid fa-magnifying-glass-arrow-right {{ $activeTab === 'hilang' ? 'text-rose-200' : 'text-rose-500' }}"></i>
            </div>
            <div class="text-2xl font-black mt-2">{{ $counts['hilang'] }} <span class="text-xs font-normal opacity-80">item</span></div>
            <span class="text-[10px] mt-1 {{ $activeTab === 'hilang' ? 'text-rose-100' : 'text-slate-400' }}">Laporan mahasiswa di kampus ini</span>
        </a>

        <a href="?tab=matching" class="p-4 rounded-2xl border transition-all text-left flex flex-col justify-between {{ $activeTab === 'matching' ? 'bg-indigo-600 text-white border-indigo-700 shadow-md shadow-indigo-600/20' : 'bg-white text-slate-700 border-slate-200/80 hover:border-indigo-400' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider {{ $activeTab === 'matching' ? 'text-indigo-100' : 'text-slate-400' }}">4. Smart Matching</span>
                <i class="fa-solid fa-bolt {{ $activeTab === 'matching' ? 'text-amber-300' : 'text-amber-500' }}"></i>
            </div>
            <div class="text-2xl font-black mt-2">{{ $counts['matching'] }} <span class="text-xs font-normal opacity-80">pasang</span></div>
            <span class="text-[10px] mt-1 {{ $activeTab === 'matching' ? 'text-indigo-100' : 'text-slate-400' }}">Potensi barang cocok</span>
        </a>
    </div>

    <!-- TAB 1: MENUNGGU VERIFIKASI FISIK -->
    @if($activeTab === 'verifikasi')
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Barang Temuan Menunggu Verifikasi Fisik</h3>
                <p class="text-xs text-slate-500 mt-0.5">Barang yang dilaporkan oleh penemu di {{ $kampus->nama_kampus }} dan perlu diperiksa serta dicatat lokasi simpannya.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                {{ $menungguVerifikasi->total() }} Menunggu
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Foto</th>
                        <th class="py-4 px-6">Nama Barang & Kode</th>
                        <th class="py-4 px-6">Kategori</th>
                        <th class="py-4 px-6">Pelapor / Penemu</th>
                        <th class="py-4 px-6">Waktu Penemuan</th>
                        <th class="py-4 px-6 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($menungguVerifikasi as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-6">
                                <img src="{{ $item->foto_utama_url ?? asset('images/no-image.png') }}" alt="{{ $item->nama_barang }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200">
                            </td>
                            <td class="py-3 px-6">
                                <span class="font-bold text-slate-900 block">{{ $item->nama_barang }}</span>
                                <span class="font-mono text-[11px] text-slate-400">{{ $item->kode_laporan }}</span>
                            </td>
                            <td class="py-3 px-6 font-medium text-slate-600">{{ $item->kategori?->nama_kategori }}</td>
                            <td class="py-3 px-6">
                                <span class="font-semibold text-slate-900">{{ $item->user?->name }}</span>
                                <span class="block text-[11px] text-slate-400">{{ $item->user?->nim ?? 'Tamu' }}</span>
                            </td>
                            <td class="py-3 px-6 text-slate-500">
                                {{ $item->tanggal_kejadian ? $item->tanggal_kejadian->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="py-3 px-6 text-right">
                                <a href="{{ route('petugas.verifikasi.form', $item->id) }}" class="px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-sm transition-all inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                    <span>Verifikasi Fisik</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-circle-check text-4xl text-emerald-300 mb-2 block"></i>
                                Tidak ada barang temuan yang menunggu verifikasi saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($menungguVerifikasi->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $menungguVerifikasi->links() }}</div>
        @endif
    </div>
    @endif

    <!-- TAB 2: BARANG DIAMANKAN DI LOKER KAMPUS -->
    @if($activeTab === 'diamankan')
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Inventaris Fisik Barang Diamankan di Loker Kampus</h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar barang yang tersimpan aman di brankas/loker keamanan {{ $kampus->nama_kampus }} dan siap diambil pemilik sah.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                {{ $barangDiamankan->total() }} Tersimpan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Foto</th>
                        <th class="py-4 px-6">Barang & Kode</th>
                        <th class="py-4 px-6">Lokasi Simpan Fisik</th>
                        <th class="py-4 px-6">Kondisi & Catatan</th>
                        <th class="py-4 px-6">Petugas Pengaman</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($barangDiamankan as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-6">
                                <img src="{{ $item->foto_utama_url ?? asset('images/no-image.png') }}" alt="{{ $item->nama_barang }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200">
                            </td>
                            <td class="py-3 px-6">
                                <span class="font-bold text-slate-900 block">{{ $item->nama_barang }}</span>
                                <span class="font-mono text-[11px] text-slate-400">{{ $item->kode_laporan }}</span>
                            </td>
                            <td class="py-3 px-6 font-semibold text-emerald-700">
                                <i class="fa-solid fa-location-pin text-xs mr-1"></i>
                                {{ $item->penyimpanan?->lokasi_penyimpanan ?? 'Pos Layanan Kampus' }}
                            </td>
                            <td class="py-3 px-6 max-w-xs text-slate-600">
                                <div>{{ $item->penyimpanan?->kondisi_barang ?? 'Kondisi baik' }}</div>
                                @if($item->isExpiredForDonation())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 mt-1 rounded-md bg-amber-50 text-amber-800 font-bold border border-amber-300 text-[10px]">
                                        <i class="fa-solid fa-triangle-exclamation text-amber-500"></i> {{ $item->days_stored }} Hari (Siap Didonasikan)
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-400 block mt-0.5">{{ $item->days_stored }} hari tersimpan</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-slate-600">
                                {{ $item->penyimpanan?->petugas?->name ?? 'Staf Kampus' }}
                            </td>
                            <td class="py-3 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('mahasiswa.laporan.detail', $item->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all">
                                        Detail
                                    </a>
                                    @if($item->isExpiredForDonation())
                                        <form action="{{ route('petugas.barang.donasikan', $item->id) }}" method="POST" onsubmit="return confirm('Konfirmasi alokasikan barang {{ $item->nama_barang }} untuk kegiatan sosial/donasi kampus (Masa simpan > 90 hari)?')">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition-all shadow-sm flex items-center gap-1">
                                                <i class="fa-solid fa-hand-holding-heart"></i> Donasi
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-box-archive text-4xl text-slate-300 mb-2 block"></i>
                                Belum ada barang yang tersimpan di loker kampus saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($barangDiamankan->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $barangDiamankan->links() }}</div>
        @endif
    </div>
    @endif

    <!-- TAB 3: LAPORAN KEHILANGAN DI KAMPUS INI -->
    @if($activeTab === 'hilang')
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Laporan Kehilangan Mahasiswa di {{ $kampus->nama_kampus }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar laporan kehilangan yang dilaporkan terjadi di lingkungan {{ $kampus->nama_kampus }}.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200">
                {{ $laporanHilang->total() }} Laporan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Barang & Kode</th>
                        <th class="py-4 px-6">Kategori</th>
                        <th class="py-4 px-6">Lokasi Terakhir</th>
                        <th class="py-4 px-6">Mahasiswa Pelapor</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($laporanHilang as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-6">
                                <span class="font-bold text-slate-900 block">{{ $item->nama_barang }}</span>
                                <span class="font-mono text-[11px] text-slate-400">{{ $item->kode_laporan }}</span>
                            </td>
                            <td class="py-3 px-6 font-medium text-slate-600">{{ $item->kategori?->nama_kategori }}</td>
                            <td class="py-3 px-6 text-slate-600">{{ $item->lokasi_kejadian }}</td>
                            <td class="py-3 px-6">
                                <span class="font-semibold text-slate-900">{{ $item->user?->name }}</span>
                                <span class="block text-[11px] text-slate-400">NIM. {{ $item->user?->nim ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $item->status_badge['bg'] }} uppercase">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-right">
                                <a href="{{ route('mahasiswa.laporan.detail', $item->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all">
                                    Buka Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-clipboard-check text-4xl text-slate-300 mb-2 block"></i>
                                Tidak ada laporan kehilangan di kampus ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($laporanHilang->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $laporanHilang->links() }}</div>
        @endif
    </div>
    @endif

    <!-- TAB 4: SMART MATCHING PAIRS -->
    @if($activeTab === 'matching')
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden p-6 space-y-6">
        <div>
            <h3 class="text-base font-bold text-slate-900">Pencocokan Cerdas (Smart Auto-Matching)</h3>
            <p class="text-xs text-slate-500 mt-0.5">Sistem secara otomatis mendeteksi kecocokan antara laporan kehilangan dan barang temuan di {{ $kampus->nama_kampus }}.</p>
        </div>

        @forelse($matchedPairs as $pair)
            @php $lost = $pair['lost']; @endphp
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600">Barang Hilang:</span>
                        <h4 class="text-sm font-bold text-slate-900">{{ $lost->nama_barang }} ({{ $lost->kode_laporan }})</h4>
                        <p class="text-xs text-slate-500">Pelapor: {{ $lost->user?->name }} &bull; Lokasi: {{ $lost->lokasi_kejadian }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs font-bold">
                        {{ count($pair['matches']) }} Kandidat Cocok
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($pair['matches'] as $m)
                        @php $candidate = $m['item']; @endphp
                        <div class="p-4 rounded-xl bg-white border border-slate-200 flex items-center justify-between gap-3">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    {{ $m['score'] }}% Kemiripan
                                </span>
                                <h5 class="text-xs font-bold text-slate-900 mt-1">{{ $candidate->nama_barang }}</h5>
                                <p class="text-[11px] text-slate-500">{{ $candidate->kode_laporan }} &bull; {{ $candidate->penyimpanan?->lokasi_penyimpanan ?? 'Pos Layanan' }}</p>
                            </div>
                            <a href="{{ route('mahasiswa.laporan.detail', $candidate->id) }}" class="px-3 py-1.5 rounded-lg bg-brand-ubsi hover:bg-blue-900 text-white font-bold text-xs transition-all whitespace-nowrap">
                                Cek Barang
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-slate-400">
                <i class="fa-solid fa-wand-magic-sparkles text-4xl text-indigo-300 mb-2 block"></i>
                Tidak ada pasangan barang hilang dan temuan yang memiliki kemiripan tinggi saat ini.
            </div>
        @endforelse
    </div>
    @endif

</div>
@endsection
