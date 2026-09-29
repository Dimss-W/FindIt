@extends('layouts.app')

@section('title', 'Dashboard Administrator')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    
    <!-- Admin Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-slate-950 via-brand-ubsi to-blue-900 text-white shadow-xl shadow-blue-950/10 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="space-y-2 relative z-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400/10 border border-amber-400/20 text-amber-300 text-xs font-bold">
                <i class="fa-solid fa-crown"></i> Master Control Panel
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                Dashboard Administrator FINDIT
            </h1>
            <p class="text-blue-200 text-xs sm:text-sm max-w-xl">
                Akses global pengawasan seluruh aktivitas lost & found di 27 unit kampus Universitas Bina Sarana Informatika.
            </p>
        </div>

        <div class="flex items-center gap-3 relative z-10 flex-shrink-0 flex-wrap">
            <a href="{{ route('admin.kampus') }}" class="px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-school-flag text-amber-300"></i>
                <span>Kelola Kampus ({{ $stats['total_kampus'] }})</span>
            </a>
            <a href="{{ route('admin.petugas') }}" class="px-4 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold shadow-lg shadow-amber-500/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-user-shield"></i>
                <span>Admin Kampus ({{ $stats['total_petugas'] }})</span>
            </a>
        </div>
    </div>

    <!-- Quick Operational Action Hub (Admin Handling Custody & Returns) -->
    <div class="bg-gradient-to-br from-blue-50/70 via-indigo-50/40 to-white p-6 sm:p-7 rounded-3xl border border-blue-100 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xs shadow-sm">
                    <i class="fa-solid fa-hand-holding-box"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Aksi Operasional Barang (Admin Layanan Kampus)</h2>
                    <p class="text-[11px] text-slate-500">Alur penerimaan fisik, verifikasi foto & kondisi, persetujuan klaim, serta konfirmasi penyerahan barang</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-brand-ubsi self-start sm:self-auto">
                <i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i> Mode Admin Aktif
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Verifikasi Temuan Masuk -->
            <a href="{{ route('petugas.menunggu.verifikasi') }}" class="p-4 rounded-2xl bg-white border border-amber-200/80 hover:border-amber-400 hover:shadow-md transition-all group flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-amber-100 text-amber-800">Langkah 1</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-amber-700 transition-colors">Verifikasi Temuan Masuk</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Terima fisik barang dari pelapor & periksa minimal 2 foto fisik.</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-100 text-[11px] font-bold text-amber-600 flex items-center gap-1">
                    <span>Buka Antrean</span> &rarr;
                </div>
            </a>

            <!-- 2. Barang Diamankan (Loker) -->
            <a href="{{ route('petugas.barang.diamankan') }}" class="p-4 rounded-2xl bg-white border border-emerald-200/80 hover:border-emerald-400 hover:shadow-md transition-all group flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-vault"></i>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">Langkah 2</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">Barang Diamankan (Loker)</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Kelola penyimpanan fisik & pantau smart matching otomatis.</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-100 text-[11px] font-bold text-emerald-600 flex items-center gap-1">
                    <span>Lihat Brankas</span> &rarr;
                </div>
            </a>

            <!-- 3. Verifikasi Klaim Mahasiswa -->
            <a href="{{ route('petugas.klaim.index') }}" class="p-4 rounded-2xl bg-white border border-blue-200/80 hover:border-blue-400 hover:shadow-md transition-all group flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-brand-ubsi flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-blue-100 text-brand-ubsi">Langkah 3</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-700 transition-colors">Verifikasi Bukti Klaim</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Cek bukti kepemilikan pelapor & setujui status siap diambil.</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-100 text-[11px] font-bold text-blue-600 flex items-center gap-1">
                    <span>Proses Klaim</span> &rarr;
                </div>
            </a>

            <!-- 4. Konfirmasi Serah Terima -->
            <a href="{{ route('petugas.barang.siap_diambil') }}" class="p-4 rounded-2xl bg-white border border-purple-200/80 hover:border-purple-400 hover:shadow-md transition-all group flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-handshake"></i>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-purple-100 text-purple-800">Langkah 4</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-purple-700 transition-colors">Konfirmasi Penyerahan</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Catat tanda terima fisik, cetak berita acara, dan selesaikan kasus.</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-100 text-[11px] font-bold text-purple-600 flex items-center gap-1">
                    <span>Serahkan Barang</span> &rarr;
                </div>
            </a>
        </div>
    </div>

    <!-- 5 Admin Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- 1. Total Laporan -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-brand-ubsi flex items-center justify-center text-lg mb-2">
                <i class="fa-solid fa-chart-simple"></i>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($stats['total_laporan']) }}</div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Total Laporan</div>
        </div>

        <!-- 2. Barang Hilang -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg mb-2">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($stats['barang_hilang']) }}</div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Barang Hilang</div>
        </div>

        <!-- 3. Barang Ditemukan -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg mb-2">
                <i class="fa-solid fa-hand-holding-hand"></i>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($stats['barang_ditemukan']) }}</div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Barang Ditemukan</div>
        </div>

        <!-- 4. Barang Diamankan -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg mb-2">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($stats['barang_diamankan']) }}</div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Barang Diamankan</div>
        </div>

        <!-- 5. Barang Dikembalikan -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg mb-2">
                <i class="fa-solid fa-champagne-glasses"></i>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($stats['barang_dikembalikan']) }}</div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Dikembalikan</div>
        </div>
    </div>

    <!-- Charts Section: Laporan per Kampus & Laporan per Kategori -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Bar Chart: Laporan per Kampus (col-span-2) -->
        <div class="lg:col-span-2 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Laporan Berdasarkan Kampus UBSI</h3>
                    <p class="text-xs text-slate-500">Jumlah sebaran laporan barang hilang & ditemukan per unit kampus</p>
                </div>
                <a href="{{ route('admin.kampus') }}" class="text-xs font-semibold text-blue-600 hover:underline">Kelola Kampus</a>
            </div>

            <div class="h-72">
                <canvas id="campusChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart: Laporan per Kategori (col-span-1) -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Laporan per Kategori</h3>
                    <p class="text-xs text-slate-500">Proporsi jenis barang yang dilaporkan</p>
                </div>
                <a href="{{ route('admin.kategori') }}" class="text-xs font-semibold text-blue-600 hover:underline">Kategori</a>
            </div>

            <div class="h-72 flex items-center justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Statistik Status Barang Cards -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="text-base font-bold text-slate-900">Statistik Status Barang Saat Ini</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($statusCounts as $stName => $stCount)
                @php
                    $stBadge = match($stName) {
                        'Sedang Dicari' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'color' => 'text-rose-600'],
                        'Menunggu Verifikasi' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'color' => 'text-amber-600'],
                        'Ada Kemungkinan Cocok' => ['bg' => 'bg-yellow-50 text-yellow-800 border-yellow-200', 'color' => 'text-yellow-600'],
                        'Barang Diamankan' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'color' => 'text-emerald-600'],
                        'Siap Diambil' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'color' => 'text-blue-600'],
                        'Dikembalikan' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200', 'color' => 'text-purple-600'],
                        default => ['bg' => 'bg-slate-50 text-slate-700 border-slate-200', 'color' => 'text-slate-600'],
                    };
                @endphp
                <div class="p-4 rounded-2xl {{ $stBadge['bg'] }} border text-center">
                    <div class="text-2xl font-black {{ $stBadge['color'] }}">{{ $stCount }}</div>
                    <div class="text-[10px] font-bold uppercase tracking-wider mt-1">{{ $stName }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Table: Laporan Global Terkini -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Laporan Masuk Terbaru (Semua Kampus)</h3>
                <p class="text-xs text-slate-500 mt-0.5">Monitoring laporan real-time di seluruh cabang UBSI</p>
            </div>
            <a href="{{ route('admin.laporan.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                <span>Buka Moderasi Laporan</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Kode</th>
                        <th class="py-4 px-6">Barang</th>
                        <th class="py-4 px-6">Kampus UBSI</th>
                        <th class="py-4 px-6">Pelapor</th>
                        <th class="py-4 px-6">Jenis</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($recentLaporans as $rep)
                        @php
                            $badge = $rep->status_badge;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900">{{ $rep->kode_laporan }}</td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $rep->nama_barang }}</div>
                                <div class="text-[11px] text-slate-400">{{ $rep->kategori->nama_kategori }}</div>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-800">{{ $rep->kampus->nama_kampus }}</td>
                            <td class="py-4 px-6">
                                <div class="font-semibold text-slate-900">{{ $rep->user->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $rep->user->email }}</div>
                            </td>
                            <td class="py-4 px-6">
                                @if($rep->jenis_laporan === 'HILANG')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 uppercase">HILANG</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase">TEMUAN</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badge['bg'] }} uppercase inline-flex items-center gap-1">
                                    <i class="fa-solid {{ $badge['icon'] }}"></i>
                                    {{ $rep->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('mahasiswa.laporan.detail', $rep->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">
                                    Lihat Detail &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">Belum ada laporan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart 1: Laporan per Kampus
        const campusCtx = document.getElementById('campusChart').getContext('2d');
        const campusData = @json($laporanPerKampus);

        new Chart(campusCtx, {
            type: 'bar',
            data: {
                labels: campusData.map(c => c.nama_kampus.replace('UBSI Kampus ', '')),
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: campusData.map(c => c.laporan_barang_count),
                    backgroundColor: '#3b82f6',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });

        // Chart 2: Laporan per Kategori
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryData = @json($laporanPerKategori);

        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryData.map(k => k.nama_kategori),
                datasets: [{
                    data: categoryData.map(k => k.laporan_barang_count),
                    backgroundColor: [
                        '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#64748b'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 10 } }
                    }
                }
            }
        });
    });
</script>
@endsection
