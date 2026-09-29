<?php

namespace App\Http\Controllers;

use App\Models\Klaim;
use App\Models\LaporanBarang;
use App\Models\Notifikasi;
use App\Models\Pengembalian;
use App\Models\PenyimpananBarang;
use App\Services\SmartMatchingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasController extends Controller
{
    protected SmartMatchingService $smartMatching;

    public function __construct(SmartMatchingService $smartMatching)
    {
        $this->smartMatching = $smartMatching;
    }

    private function getPetugasKampusId(): int
    {
        $user = Auth::user();
        if ($user->kampus_id) {
            return $user->kampus_id;
        }

        // If admin without fixed campus, allow selecting from request or fallback to first campus
        if (request()->filled('kampus_id')) {
            return (int) request('kampus_id');
        }

        $defaultKampus = \App\Models\Kampus::where('status', 'aktif')->first();
        return $defaultKampus ? $defaultKampus->id : 1;
    }

    public function dashboard()
    {
        $kampusId = $this->getPetugasKampusId();
        $kampus = Auth::user()->kampus ?? \App\Models\Kampus::find($kampusId);

        $stats = [
            'total_ditemukan' => LaporanBarang::where('kampus_id', $kampusId)->where('jenis_laporan', 'DITEMUKAN')->count(),
            'menunggu_verifikasi' => LaporanBarang::where('kampus_id', $kampusId)->where('status', 'MENUNGGU VERIFIKASI')->count(),
            'barang_diamankan' => LaporanBarang::where('kampus_id', $kampusId)->where('status', 'BARANG DIAMANKAN')->count(),
            'siap_diambil' => LaporanBarang::where('kampus_id', $kampusId)->where('status', 'SIAP DIAMBIL')->count(),
            'dikembalikan' => LaporanBarang::where('kampus_id', $kampusId)->where('status', 'DIKEMBALIKAN')->count(),
            'klaim_masuk' => Klaim::whereHas('laporan', fn($q) => $q->where('kampus_id', $kampusId))->where('status', 'MENUNGGU VERIFIKASI')->count(),
        ];

        $recentItems = LaporanBarang::with(['kategori', 'user', 'penyimpanan'])
            ->where('kampus_id', $kampusId)
            ->latest()
            ->take(6)
            ->get();

        return view('petugas.dashboard', compact('kampus', 'stats', 'recentItems'));
    }

    public function barangDitemukan(Request $request)
    {
        $kampusId = $this->getPetugasKampusId();

        $query = LaporanBarang::with(['kategori', 'user', 'penyimpanan'])
            ->where('kampus_id', $kampusId)
            ->where('jenis_laporan', 'DITEMUKAN');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->where('nama_barang', 'LIKE', "%{$request->q}%");
        }

        $items = $query->latest()->paginate(10)->withQueryString();

        return view('petugas.barang-ditemukan', compact('items'));
    }

    public function menungguVerifikasi()
    {
        $kampusId = $this->getPetugasKampusId();

        $items = LaporanBarang::with(['kategori', 'user'])
            ->where('kampus_id', $kampusId)
            ->where('status', 'MENUNGGU VERIFIKASI')
            ->latest()
            ->paginate(10);

        return view('petugas.menunggu-verifikasi', compact('items'));
    }

    public function verifikasiForm($id)
    {
        $kampusId = $this->getPetugasKampusId();

        $laporan = LaporanBarang::where('kampus_id', $kampusId)
            ->where('jenis_laporan', 'DITEMUKAN')
            ->with(['kategori', 'user'])
            ->findOrFail($id);

        return view('petugas.verifikasi-barang', compact('laporan'));
    }

    public function simpanVerifikasi(Request $request, $id)
    {
        $kampusId = $this->getPetugasKampusId();

        $laporan = LaporanBarang::where('kampus_id', $kampusId)->findOrFail($id);

        $validated = $request->validate([
            'lokasi_penyimpanan' => ['required', 'string', 'max:255'],
            'kondisi_barang' => ['required', 'string', 'max:255'],
            'catatan' => ['nullable', 'string'],
        ]);

        // Simpan data penyimpanan fisik
        PenyimpananBarang::updateOrCreate(
            ['laporan_id' => $laporan->id],
            [
                'petugas_id' => Auth::id(),
                'lokasi_penyimpanan' => $validated['lokasi_penyimpanan'],
                'tanggal_diterima' => Carbon::now(),
                'kondisi_barang' => $validated['kondisi_barang'],
                'catatan' => $validated['catatan'],
            ]
        );

        // Update status laporan menjadi BARANG DIAMANKAN
        $laporan->update(['status' => 'BARANG DIAMANKAN']);

        // Kirim notifikasi ke pelapor barang temuan
        Notifikasi::create([
            'user_id' => $laporan->user_id,
            'judul' => 'Barang Ditemukan Telah Diamankan',
            'pesan' => "Laporan barang temuan \"{$laporan->nama_barang}\" telah diverifikasi fisik dan diamankan di {$validated['lokasi_penyimpanan']} oleh Petugas Keamanan.",
            'link' => route('mahasiswa.laporan.detail', $laporan->id),
            'is_read' => false,
        ]);

        // Cek apakah ada laporan kehilangan yang cocok dan kirim notifikasi
        $this->smartMatching->notifyOnHighMatch($laporan);

        return redirect()->route('petugas.barang.diamankan')
            ->with('success', "Barang \"{$laporan->nama_barang}\" berhasil diverifikasi dan disimpan dengan status BARANG DIAMANKAN.");
    }

    public function barangDiamankan(Request $request)
    {
        $kampusId = $this->getPetugasKampusId();

        $items = LaporanBarang::with(['kategori', 'user', 'penyimpanan.petugas'])
            ->where('kampus_id', $kampusId)
            ->where('status', 'BARANG DIAMANKAN')
            ->latest()
            ->paginate(10);

        return view('petugas.barang-diamankan', compact('items'));
    }

    public function donasikanBarang(Request $request, $id)
    {
        $kampusId = $this->getPetugasKampusId();
        $barang = LaporanBarang::where('kampus_id', $kampusId)->findOrFail($id);

        if ($barang->status !== 'BARANG DIAMANKAN') {
            return back()->with('error', 'Hanya barang dengan status BARANG DIAMANKAN yang dapat didonasikan.');
        }

        $barang->update([
            'status' => 'DIDONASIKAN',
            'deskripsi' => $barang->deskripsi . "\n\n[CATATAN SISTEM: Barang telah melewati batas masa simpan dan resmi dialokasikan untuk kegiatan sosial / donasi kampus oleh Petugas " . (Auth::user()->name ?? 'Layanan Kampus') . " pada " . now()->translatedFormat('d F Y H:i') . " WIB]"
        ]);

        return back()->with('success', "Barang \"{$barang->nama_barang}\" ({$barang->kode_laporan}) berhasil dialihkan statusnya menjadi DIDONASIKAN.");
    }

    public function laporanHilang(Request $request)
    {
        $kampusId = $this->getPetugasKampusId();

        $items = LaporanBarang::with(['kategori', 'user'])
            ->where('kampus_id', $kampusId)
            ->where('jenis_laporan', 'HILANG')
            ->latest()
            ->paginate(10);

        return view('petugas.laporan-hilang', compact('items'));
    }

    public function smartMatching(Request $request)
    {
        $kampusId = $this->getPetugasKampusId();

        // Ambil semua laporan kehilangan di kampus ini yang masih aktif
        $lostReports = LaporanBarang::with(['kategori', 'user'])
            ->where('kampus_id', $kampusId)
            ->where('jenis_laporan', 'HILANG')
            ->whereIn('status', ['SEDANG DICARI', 'ADA KEMUNGKINAN COCOK'])
            ->latest()
            ->get();

        $matchedPairs = [];
        foreach ($lostReports as $lost) {
            $matches = $this->smartMatching->findMatchesFor($lost, 50, 3);
            if (!empty($matches)) {
                $matchedPairs[] = [
                    'lost' => $lost,
                    'matches' => $matches,
                ];
            }
        }

        return view('petugas.smart-matching', compact('matchedPairs', 'lostReports'));
    }

    public function pengajuanKlaim(Request $request)
    {
        $kampusId = $this->getPetugasKampusId();

        $query = Klaim::with(['laporan.kategori', 'user', 'petugas'])
            ->whereHas('laporan', fn($q) => $q->where('kampus_id', $kampusId));

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $klaims = $query->latest()->paginate(10)->withQueryString();

        return view('petugas.pengajuan-klaim', compact('klaims'));
    }

    public function detailKlaim($id)
    {
        $kampusId = $this->getPetugasKampusId();

        $klaim = Klaim::with(['laporan.kategori', 'laporan.penyimpanan', 'user', 'petugas'])
            ->whereHas('laporan', fn($q) => $q->where('kampus_id', $kampusId))
            ->findOrFail($id);

        return view('petugas.detail-klaim', compact('klaim'));
    }

    public function verifikasiKlaim(Request $request, $id)
    {
        $kampusId = $this->getPetugasKampusId();

        $klaim = Klaim::with('laporan')
            ->whereHas('laporan', fn($q) => $q->where('kampus_id', $kampusId))
            ->findOrFail($id);

        $validated = $request->validate([
            'keputusan' => ['required', 'in:DISETUJUI,DITOLAK'],
            'catatan_petugas' => ['required', 'string', 'min:5'],
        ]);

        $statusKlaim = $validated['keputusan'];
        $klaim->update([
            'status' => $statusKlaim,
            'catatan_petugas' => $validated['catatan_petugas'],
            'petugas_id' => Auth::id(),
            'tanggal_diverifikasi' => Carbon::now(),
        ]);

        if ($statusKlaim === 'DISETUJUI') {
            // Generate Kode Tiket Pengambilan dan QR Token unik
            $kodeTiket = $klaim->generateTiketPengambilan();

            // Update laporan barang menjadi SIAP DIAMBIL
            $klaim->laporan->update(['status' => 'SIAP DIAMBIL']);

            // Notifikasi ke mahasiswa pemilik klaim
            Notifikasi::create([
                'user_id' => $klaim->user_id,
                'judul' => '🎉 Klaim Disetujui - Tiket Pengambilan Terbit',
                'pesan' => "Selamat! Klaim Anda untuk barang \"{$klaim->laporan->nama_barang}\" telah DISETUJUI dengan Kode Tiket: {$kodeTiket}. Tunjukkan QR Code tiket di aplikasi Anda ke ruang staf/admin kampus untuk serah terima barang.",
                'link' => route('mahasiswa.laporan.detail', $klaim->laporan_id),
                'is_read' => false,
            ]);

            // Kirim Notifikasi WhatsApp Otomatis ke Mahasiswa
            \App\Services\WhatsAppService::notifyClaimApproved($klaim);

            return redirect()->route('petugas.klaim.index')
                ->with('success', "Klaim disetujui! Tiket Pengambilan {$kodeTiket} telah berhasil diterbitkan dan notifikasi WhatsApp telah dikirimkan ke mahasiswa.");
        } else {
            // Notifikasi penolakan
            Notifikasi::create([
                'user_id' => $klaim->user_id,
                'judul' => '❌ Pengajuan Klaim Ditolak',
                'pesan' => "Mohon maaf, pengajuan klaim Anda untuk barang \"{$klaim->laporan->nama_barang}\" ditolak oleh Petugas Keamanan dengan catatan: \"{$validated['catatan_petugas']}\"",
                'link' => route('mahasiswa.klaim.saya'),
                'is_read' => false,
            ]);

            return redirect()->route('petugas.klaim.index')
                ->with('warning', 'Klaim ditolak.');
        }
    }

    public function barangSiapDiambil()
    {
        $kampusId = $this->getPetugasKampusId();

        $items = LaporanBarang::with(['kategori', 'klaim.user', 'penyimpanan'])
            ->where('kampus_id', $kampusId)
            ->where('status', 'SIAP DIAMBIL')
            ->latest()
            ->paginate(10);

        return view('petugas.barang-siap-diambil', compact('items'));
    }

    public function formPengembalian($id)
    {
        $kampusId = $this->getPetugasKampusId();

        $laporan = LaporanBarang::with(['kategori', 'penyimpanan', 'klaim.user'])
            ->where('kampus_id', $kampusId)
            ->where('status', 'SIAP DIAMBIL')
            ->findOrFail($id);

        $approvedClaim = $laporan->klaim->firstWhere('status', 'DISETUJUI');

        return view('petugas.form-pengembalian', compact('laporan', 'approvedClaim'));
    }

    public function simpanPengembalian(Request $request, $id)
    {
        $kampusId = $this->getPetugasKampusId();

        $laporan = LaporanBarang::with(['klaim'])->where('kampus_id', $kampusId)->findOrFail($id);
        $approvedClaim = $laporan->klaim->firstWhere('status', 'DISETUJUI');

        $validated = $request->validate([
            'penerima_id' => ['required', 'exists:users,id'],
            'catatan' => ['nullable', 'string'],
            'foto_penyerahan' => ['nullable', 'image', 'max:3072'],
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_penyerahan')) {
            $fotoPath = $request->file('foto_penyerahan')->store('pengembalian', 'public');
        }

        $nomorBast = Pengembalian::generateNomorBast($laporan->kampus?->kode_kampus ?? 'PST');

        $pengembalian = Pengembalian::create([
            'nomor_bast' => $nomorBast,
            'laporan_id' => $laporan->id,
            'klaim_id' => $approvedClaim?->id,
            'petugas_id' => Auth::id(),
            'user_id' => $validated['penerima_id'],
            'tanggal_pengembalian' => Carbon::now(),
            'catatan' => $validated['catatan'],
            'foto_penyerahan' => $fotoPath,
            'status' => 'DIKEMBALIKAN',
        ]);

        // Update status laporan menjadi DIKEMBALIKAN
        $laporan->update(['status' => 'DIKEMBALIKAN']);

        // Notifikasi ke penerima
        Notifikasi::create([
            'user_id' => $validated['penerima_id'],
            'judul' => '🎉 Barang Telah Berhasil Dikembalikan',
            'pesan' => "Serah terima barang \"{$laporan->nama_barang}\" telah selesai dengan Nomor BAST: {$nomorBast}. Dokumen berita acara resmi telah terbit.",
            'link' => route('mahasiswa.laporan.detail', $laporan->id),
            'is_read' => false,
        ]);

        // Kirim Notifikasi WhatsApp BAST ke Mahasiswa
        \App\Services\WhatsAppService::notifyHandoverSuccess($pengembalian);

        return redirect()->route('petugas.riwayat.pengembalian')
            ->with('success', "Konfirmasi pengembalian barang berhasil disimpan! Nomor BAST {$nomorBast} telah diterbitkan dan notifikasi WhatsApp terkirim.");
    }

    public function riwayatPengembalian()
    {
        $kampusId = $this->getPetugasKampusId();

        $riwayat = Pengembalian::with(['laporan.kampus', 'laporan.kategori', 'petugas', 'user', 'klaim'])
            ->whereHas('laporan', fn($q) => $q->where('kampus_id', $kampusId))
            ->latest('tanggal_pengembalian')
            ->paginate(10);

        return view('petugas.riwayat-pengembalian', compact('riwayat'));
    }

    /**
     * Tampilkan Halaman Scanner QR Serah Terima
     */
    public function scanQrIndex()
    {
        $kampusId = $this->getPetugasKampusId();
        $kampus = Auth::user()->kampus ?? \App\Models\Kampus::find($kampusId);

        return view('petugas.scanner.index', compact('kampus'));
    }

    /**
     * Verifikasi kode tiket atau token QR via AJAX
     */
    public function verifyQrTicket(Request $request)
    {
        $request->validate([
            'query' => ['required', 'string'],
        ]);

        $query = trim($request->input('query'));
        $kampusId = $this->getPetugasKampusId();

        // Cari berdasarkan kode_tiket ATAU qr_token
        $klaim = Klaim::with(['laporan.kategori', 'laporan.kampus', 'user', 'petugas'])
            ->where(function ($q) use ($query) {
                $q->where('kode_tiket', $query)
                  ->orWhere('qr_token', $query);
            })
            ->first();

        if (!$klaim) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan! Pastikan kode tiket atau QR Code sesuai.'
            ], 404);
        }

        // Cek status klaim
        if ($klaim->status !== 'DISETUJUI') {
            return response()->json([
                'success' => false,
                'message' => "Tiket ini tidak valid untuk penyerahan. Status klaim saat ini: {$klaim->status}."
            ], 422);
        }

        // Cek apakah barang sudah diserah-terimakan
        if ($klaim->laporan->status === 'DIKEMBALIKAN') {
            $pengembalian = Pengembalian::where('laporan_id', $klaim->laporan_id)->first();
            return response()->json([
                'success' => false,
                'message' => "Barang ini SUDAH DISERAH-TERIMAKAN pada " . ($pengembalian?->tanggal_pengembalian?->format('d/m/Y H:i') ?? '-') . " dengan Nomor BAST: " . ($pengembalian?->nomor_bast ?? '-'),
                'already_completed' => true,
                'bast_url' => $pengembalian ? route('petugas.bast.cetak', $pengembalian->id) : null
            ], 422);
        }

        // Cek isolasi kampus: Hanya staf di kampus lokasi barang yang berhak memproses serah terima
        if (Auth::user()->isPetugas() && $klaim->laporan->kampus_id !== $kampusId) {
            $namaKampusBarang = $klaim->laporan->kampus?->nama_kampus ?? 'kampus lain';
            return response()->json([
                'success' => false,
                'message' => "Akses Ditolak: Tiket ini terdaftar di unit {$namaKampusBarang}. Barang fisik disimpan di kampus tersebut dan hanya dapat diserahkan oleh staf {$namaKampusBarang}."
            ], 403);
        }

        // Foto barang utama
        $fotoUtama = $klaim->laporan->foto_utama_url ?? asset('images/no-image.png');

        return response()->json([
            'success' => true,
            'klaim_id' => $klaim->id,
            'laporan_id' => $klaim->laporan_id,
            'kode_tiket' => $klaim->kode_tiket,
            'nama_barang' => $klaim->laporan->nama_barang,
            'kategori' => $klaim->laporan->kategori?->nama_kategori ?? 'Umum',
            'kampus' => $klaim->laporan->kampus?->nama_kampus ?? 'UBSI',
            'lokasi_ditemukan' => $klaim->laporan->lokasi_spesifik ?? '-',
            'foto_barang' => $fotoUtama,
            'mahasiswa' => [
                'id' => $klaim->user->id,
                'name' => $klaim->user->name,
                'nim' => $klaim->user->nim ?? '-',
                'email' => $klaim->user->email,
                'no_hp' => $klaim->user->no_hp ?? '-',
                'avatar' => $klaim->user->avatar_url ?? null,
            ],
            'bukti_klaim' => $klaim->deskripsi_klaim,
            'catatan_petugas' => $klaim->catatan_petugas,
            'tanggal_disetujui' => $klaim->tanggal_diverifikasi ? $klaim->tanggal_diverifikasi->translatedFormat('d F Y H:i') : '-',
        ]);
    }

    /**
     * Selesaikan Serah Terima Barang dari Scanner QR
     */
    public function prosesSerahTerimaQr(Request $request)
    {
        $request->validate([
            'klaim_id' => ['required', 'exists:klaim,id'],
            'catatan' => ['nullable', 'string'],
            'foto_penyerahan' => ['nullable', 'image', 'max:3072'],
        ]);

        $klaim = Klaim::with('laporan.kampus', 'user')->findOrFail($request->input('klaim_id'));
        $laporan = $klaim->laporan;

        if (Auth::user()->isPetugas() && $laporan->kampus_id !== $this->getPetugasKampusId()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses Ditolak: Anda tidak memiliki wewenang menyerahkan barang milik unit kampus lain.'
            ], 403);
        }

        if ($laporan->status === 'DIKEMBALIKAN') {
            return response()->json([
                'success' => false,
                'message' => 'Laporan barang ini sudah diserah-terimakan sebelumnya.'
            ], 422);
        }

        $fotoPath = null;
        if ($request->hasFile('foto_penyerahan')) {
            $fotoPath = $request->file('foto_penyerahan')->store('pengembalian', 'public');
        }

        $nomorBast = Pengembalian::generateNomorBast($laporan->kampus?->kode_kampus ?? 'PST');

        $pengembalian = Pengembalian::create([
            'nomor_bast' => $nomorBast,
            'laporan_id' => $laporan->id,
            'klaim_id' => $klaim->id,
            'petugas_id' => Auth::id(),
            'user_id' => $klaim->user_id,
            'tanggal_pengembalian' => Carbon::now(),
            'catatan' => $request->input('catatan', 'Diserahkan langsung melalui pemindaian QR Ticket terverifikasi.'),
            'foto_penyerahan' => $fotoPath,
            'status' => 'DIKEMBALIKAN',
        ]);

        // Perbarui status laporan & klaim
        $laporan->update(['status' => 'DIKEMBALIKAN']);
        $klaim->update(['status' => 'DISETUJUI']);

        // Notifikasi ke mahasiswa
        Notifikasi::create([
            'user_id' => $klaim->user_id,
            'judul' => '🎉 Barang Telah Berhasil Diterima!',
            'pesan' => "Serah terima barang \"{$laporan->nama_barang}\" telah selesai dengan Nomor Berita Acara: {$nomorBast}. Terima kasih telah menggunakan FINDIT UBSI.",
            'link' => route('mahasiswa.laporan.detail', $laporan->id),
            'is_read' => false,
        ]);

        // Kirim Notifikasi WhatsApp BAST ke Mahasiswa
        \App\Services\WhatsAppService::notifyHandoverSuccess($pengembalian);

        return response()->json([
            'success' => true,
            'message' => 'Serah terima barang berhasil diselesaikan! Dokumen BAST siap dicetak dan notifikasi WhatsApp telah dikirim.',
            'nomor_bast' => $nomorBast,
            'bast_url' => route('petugas.bast.cetak', $pengembalian->id),
        ]);
    }

    /**
     * Cetak Berita Acara Serah Terima (BAST) Resmi UBSI
     */
    public function cetakBast($id)
    {
        $pengembalian = Pengembalian::with([
            'laporan.kampus', 
            'laporan.kategori', 
            'petugas', 
            'user', 
            'klaim'
        ])->findOrFail($id);

        return view('petugas.bast.cetak', compact('pengembalian'));
    }

    /**
     * PUSAT INVENTARIS BARANG KAMPUS (Unified Hub / All-in-One)
     * Menggabungkan Verifikasi, Diamankan, Laporan Hilang, dan Smart Matching dalam 1 halaman efisien.
     */
    public function inventarisHub(Request $request)
    {
        $kampusId = $this->getPetugasKampusId();
        $kampus = Auth::user()->kampus ?? \App\Models\Kampus::find($kampusId);
        $activeTab = $request->query('tab', 'verifikasi');

        // 1. Tab Verifikasi (Barang Ditemukan yang Menunggu Verifikasi Fisik)
        $menungguVerifikasi = LaporanBarang::with(['kategori', 'user'])
            ->where('kampus_id', $kampusId)
            ->where('jenis_laporan', 'DITEMUKAN')
            ->where('status', 'MENUNGGU VERIFIKASI')
            ->latest()
            ->paginate(10, ['*'], 'page_verif');

        // 2. Tab Barang Diamankan di Loker Kampus
        $barangDiamankan = LaporanBarang::with(['kategori', 'user', 'penyimpanan.petugas'])
            ->where('kampus_id', $kampusId)
            ->where('status', 'BARANG DIAMANKAN')
            ->latest()
            ->paginate(10, ['*'], 'page_diamankan');

        // 3. Tab Laporan Barang Hilang di Kampus Ini
        $laporanHilang = LaporanBarang::with(['kategori', 'user'])
            ->where('kampus_id', $kampusId)
            ->where('jenis_laporan', 'HILANG')
            ->latest()
            ->paginate(10, ['*'], 'page_hilang');

        // 4. Tab Smart Matching Pasangan Barang
        $lostReports = LaporanBarang::with(['kategori', 'user'])
            ->where('kampus_id', $kampusId)
            ->where('jenis_laporan', 'HILANG')
            ->whereIn('status', ['SEDANG DICARI', 'ADA KEMUNGKINAN COCOK'])
            ->latest()
            ->take(15)
            ->get();

        $matchedPairs = [];
        foreach ($lostReports as $lost) {
            $matches = $this->smartMatching->findMatchesFor($lost, 45, 3);
            if (!empty($matches)) {
                $matchedPairs[] = [
                    'lost' => $lost,
                    'matches' => $matches,
                ];
            }
        }

        $counts = [
            'verifikasi' => LaporanBarang::where('kampus_id', $kampusId)->where('status', 'MENUNGGU VERIFIKASI')->count(),
            'diamankan' => LaporanBarang::where('kampus_id', $kampusId)->where('status', 'BARANG DIAMANKAN')->count(),
            'hilang' => LaporanBarang::where('kampus_id', $kampusId)->where('jenis_laporan', 'HILANG')->count(),
            'matching' => count($matchedPairs),
        ];

        return view('petugas.inventaris-hub', compact(
            'kampus', 'activeTab', 'counts',
            'menungguVerifikasi', 'barangDiamankan', 'laporanHilang', 'matchedPairs'
        ));
    }

    /**
     * PUSAT KLAIM & SERAH TERIMA QR (Unified Hub / All-in-One)
     * Menggabungkan Verifikasi Klaim, Pemindai QR & Input Tiket, serta Riwayat BAST dalam 1 halaman efisien.
     */
    public function serahTerimaHub(Request $request)
    {
        $kampusId = $this->getPetugasKampusId();
        $kampus = Auth::user()->kampus ?? \App\Models\Kampus::find($kampusId);
        $activeTab = $request->query('tab', 'klaim');

        // 1. Daftar Klaim Masuk & Perlu Tindakan
        $klaimQuery = Klaim::with(['laporan.kategori', 'user', 'petugas'])
            ->whereHas('laporan', fn($q) => $q->where('kampus_id', $kampusId));

        if ($request->filled('status_klaim')) {
            $klaimQuery->where('status', $request->status_klaim);
        }

        $klaimList = $klaimQuery->latest()->paginate(10, ['*'], 'page_klaim');

        // 2. Riwayat Serah Terima & BAST
        $riwayatList = Pengembalian::with(['laporan.kategori', 'laporan.kampus', 'user', 'petugas', 'klaim'])
            ->whereHas('laporan', fn($q) => $q->where('kampus_id', $kampusId))
            ->latest('tanggal_pengembalian')
            ->paginate(10, ['*'], 'page_riwayat');

        $counts = [
            'klaim_pending' => Klaim::whereHas('laporan', fn($q) => $q->where('kampus_id', $kampusId))->where('status', 'MENUNGGU VERIFIKASI')->count(),
            'klaim_siap_ambil' => Klaim::whereHas('laporan', fn($q) => $q->where('kampus_id', $kampusId))->where('status', 'DISETUJUI')->count(),
            'riwayat_selesai' => Pengembalian::whereHas('laporan', fn($q) => $q->where('kampus_id', $kampusId))->count(),
        ];

        return view('petugas.serah-terima-hub', compact('kampus', 'activeTab', 'counts', 'klaimList', 'riwayatList'));
    }
}
