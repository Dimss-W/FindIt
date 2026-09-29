<?php

namespace App\Http\Controllers;

use App\Models\Kampus;
use App\Models\Kategori;
use App\Models\Klaim;
use App\Models\LaporanBarang;
use App\Models\Notifikasi;
use App\Services\SmartMatchingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MahasiswaController extends Controller
{
    protected SmartMatchingService $smartMatching;

    public function __construct(SmartMatchingService $smartMatching)
    {
        $this->smartMatching = $smartMatching;
    }

    public function dashboard()
    {
        $user = Auth::user();

        $stats = [
            'hilang' => LaporanBarang::where('user_id', $user->id)->where('jenis_laporan', 'HILANG')->count(),
            'ditemukan' => LaporanBarang::where('user_id', $user->id)->where('jenis_laporan', 'DITEMUKAN')->count(),
            'proses' => LaporanBarang::where('user_id', $user->id)->whereIn('status', ['SEDANG DICARI', 'ADA KEMUNGKINAN COCOK', 'MENUNGGU VERIFIKASI', 'BARANG DIAMANKAN', 'SIAP DIAMBIL'])->count(),
            'dikembalikan' => LaporanBarang::where('user_id', $user->id)->where('status', 'DIKEMBALIKAN')->count(),
            'klaim_saya' => Klaim::where('user_id', $user->id)->count(),
        ];

        $myReports = LaporanBarang::with(['kampus', 'kategori'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $unreadNotifications = Notifikasi::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get();

        return view('mahasiswa.dashboard', compact('user', 'stats', 'myReports', 'unreadNotifications'));
    }

    public function laporHilangForm()
    {
        $campuses = Kampus::where('status', 'aktif')->orderBy('nama_kampus')->get();
        $categories = Kategori::where('status', 'aktif')->orderBy('nama_kategori')->get();
        return view('mahasiswa.lapor-hilang', compact('campuses', 'categories'));
    }

    public function laporHilangSubmit(Request $request)
    {
        $validated = $request->validate([
            'kampus_id' => ['required', 'exists:kampus,id'],
            'kategori_id' => ['required', 'exists:kategori,id'],
            'nama_barang' => ['required', 'string', 'max:255'],
            'lokasi_kejadian' => ['required', 'string', 'max:255'],
            'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
            'waktu_kejadian' => ['nullable', 'date_format:H:i'],
            'deskripsi' => ['required', 'string'],
            'ciri_khusus' => ['nullable', 'string'],
            'foto_barang' => ['nullable', 'image', 'max:3072'],
        ]);

        $kodeLaporan = $this->generateKodeLaporan();

        $fotoPaths = [];
        if ($request->hasFile('foto_barang')) {
            $files = is_array($request->file('foto_barang')) ? $request->file('foto_barang') : [$request->file('foto_barang')];
            foreach ($files as $file) {
                $fotoPaths[] = $file->store('laporan', 'public');
            }
        }
        $fotoString = !empty($fotoPaths) ? json_encode($fotoPaths) : null;

        $laporan = LaporanBarang::create([
            'kode_laporan' => $kodeLaporan,
            'user_id' => Auth::id(),
            'kampus_id' => $validated['kampus_id'],
            'kategori_id' => $validated['kategori_id'],
            'jenis_laporan' => 'HILANG',
            'nama_barang' => $validated['nama_barang'],
            'lokasi_kejadian' => $validated['lokasi_kejadian'],
            'tanggal_kejadian' => $validated['tanggal_kejadian'],
            'waktu_kejadian' => $validated['waktu_kejadian'],
            'deskripsi' => $validated['deskripsi'],
            'ciri_khusus' => $validated['ciri_khusus'],
            'foto_barang' => $fotoString,
            'status' => 'SEDANG DICARI',
        ]);

        // Kirim notifikasi sistem ke mahasiswa
        Notifikasi::create([
            'user_id' => Auth::id(),
            'judul' => "Laporan Kehilangan Dibuat ({$kodeLaporan})",
            'pesan' => "Laporan kehilangan \"{$laporan->nama_barang}\" berhasil dipublikasikan dengan kode {$kodeLaporan}. Sistem kami sedang memantau kecocokan secara berkala.",
            'link' => route('mahasiswa.laporan.detail', $laporan->id),
            'is_read' => false,
        ]);

        // Cek kecocokan otomatis
        $this->smartMatching->notifyOnHighMatch($laporan);

        return redirect()->route('mahasiswa.laporan.detail', $laporan->id)
            ->with('success', "Laporan kehilangan berhasil dikirim dengan Kode Laporan: {$kodeLaporan}");
    }

    public function laporTemukanForm()
    {
        $campuses = Kampus::where('status', 'aktif')->orderBy('nama_kampus')->get();
        $categories = Kategori::where('status', 'aktif')->orderBy('nama_kategori')->get();
        return view('mahasiswa.lapor-temukan', compact('campuses', 'categories'));
    }

    public function laporTemukanSubmit(Request $request)
    {
        $validated = $request->validate([
            'kampus_id' => ['required', 'exists:kampus,id'],
            'kategori_id' => ['required', 'exists:kategori,id'],
            'nama_barang' => ['required', 'string', 'max:255'],
            'lokasi_kejadian' => ['required', 'string', 'max:255'],
            'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
            'waktu_kejadian' => ['nullable', 'date_format:H:i'],
            'deskripsi' => ['required', 'string'],
            'ciri_khusus' => ['nullable', 'string'],
            'foto_barang' => ['required', 'array', 'min:2'],
            'foto_barang.*' => ['image', 'max:3072'],
        ], [
            'foto_barang.required' => 'Wajib melampirkan foto barang temuan.',
            'foto_barang.min' => 'Wajib melampirkan minimal 2 foto barang temuan (misal: tampak depan & tampak belakang / detail).',
            'foto_barang.*.image' => 'File harus berformat gambar valid (JPG, PNG, atau WEBP).',
            'foto_barang.*.max' => 'Ukuran setiap foto tidak boleh lebih dari 3MB.',
        ]);

        $kodeLaporan = $this->generateKodeLaporan();

        $fotoPaths = [];
        if ($request->hasFile('foto_barang')) {
            foreach ($request->file('foto_barang') as $file) {
                $fotoPaths[] = $file->store('laporan', 'public');
            }
        }
        $fotoString = !empty($fotoPaths) ? json_encode($fotoPaths) : null;

        $laporan = LaporanBarang::create([
            'kode_laporan' => $kodeLaporan,
            'user_id' => Auth::id(),
            'kampus_id' => $validated['kampus_id'],
            'kategori_id' => $validated['kategori_id'],
            'jenis_laporan' => 'DITEMUKAN',
            'nama_barang' => $validated['nama_barang'],
            'lokasi_kejadian' => $validated['lokasi_kejadian'],
            'tanggal_kejadian' => $validated['tanggal_kejadian'],
            'waktu_kejadian' => $validated['waktu_kejadian'],
            'deskripsi' => $validated['deskripsi'],
            'ciri_khusus' => $validated['ciri_khusus'],
            'foto_barang' => $fotoString,
            'status' => 'MENUNGGU VERIFIKASI',
        ]);

        // Kirim notifikasi sistem
        Notifikasi::create([
            'user_id' => Auth::id(),
            'judul' => "Laporan Barang Ditemukan ({$kodeLaporan})",
            'pesan' => "Terima kasih telah melaporkan barang temuan \"{$laporan->nama_barang}\". Silakan serahkan fisik barang kepada Admin / Staf Layanan Kampus untuk diamankan.",
            'link' => route('mahasiswa.laporan.detail', $laporan->id),
            'is_read' => false,
        ]);

        // Cek kecocokan otomatis
        $this->smartMatching->notifyOnHighMatch($laporan);

        return redirect()->route('mahasiswa.laporan.detail', $laporan->id)
            ->with('success', "Laporan barang temuan berhasil dibuat ({$kodeLaporan}) dengan 2 foto terlampir. Harap segera serahkan barang fisik ke Ruang Admin / Pelayanan Kampus.");
    }

    public function laporanSaya(Request $request)
    {
        $query = LaporanBarang::with(['kampus', 'kategori'])
            ->where('user_id', Auth::id());

        if ($request->filled('jenis')) {
            $query->where('jenis_laporan', $request->jenis);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $laporans = $query->latest()->paginate(10)->withQueryString();

        return view('mahasiswa.laporan-saya', compact('laporans'));
    }

    public function detailLaporan($id)
    {
        $laporan = LaporanBarang::with(['user', 'kampus', 'kategori', 'penyimpanan.petugas', 'klaim.user', 'pengembalian.petugas'])->findOrFail($id);

        // Cari kemungkinan kecocokan barang
        $matches = $this->smartMatching->findMatchesFor($laporan, 40, 5);

        // Cek apakah user saat ini sudah pernah mengajukan klaim untuk barang ini
        $myClaim = Klaim::where('laporan_id', $laporan->id)
            ->where('user_id', Auth::id())
            ->first();

        return view('mahasiswa.detail-laporan', compact('laporan', 'matches', 'myClaim'));
    }

    public function editLaporan($id)
    {
        $laporan = LaporanBarang::where('user_id', Auth::id())->findOrFail($id);

        if (!$laporan->canBeManagedByStudent()) {
            return back()->with('error', 'Laporan ini sudah diproses oleh petugas dan tidak dapat diedit lagi.');
        }

        $campuses = Kampus::where('status', 'aktif')->orderBy('nama_kampus')->get();
        $categories = Kategori::where('status', 'aktif')->orderBy('nama_kategori')->get();

        return view('mahasiswa.edit-laporan', compact('laporan', 'campuses', 'categories'));
    }

    public function updateLaporan(Request $request, $id)
    {
        $laporan = LaporanBarang::where('user_id', Auth::id())->findOrFail($id);

        if (!$laporan->canBeManagedByStudent()) {
            return back()->with('error', 'Laporan ini tidak dapat diedit lagi.');
        }

        $validated = $request->validate([
            'kampus_id' => ['required', 'exists:kampus,id'],
            'kategori_id' => ['required', 'exists:kategori,id'],
            'nama_barang' => ['required', 'string', 'max:255'],
            'lokasi_kejadian' => ['required', 'string', 'max:255'],
            'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
            'waktu_kejadian' => ['nullable', 'date_format:H:i'],
            'deskripsi' => ['required', 'string'],
            'ciri_khusus' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('foto_barang')) {
            $files = is_array($request->file('foto_barang')) ? $request->file('foto_barang') : [$request->file('foto_barang')];
            $fotoPaths = [];
            foreach ($files as $file) {
                $fotoPaths[] = $file->store('laporan', 'public');
            }
            $validated['foto_barang'] = json_encode($fotoPaths);
        }

        $laporan->update($validated);

        return redirect()->route('mahasiswa.laporan.detail', $laporan->id)->with('success', 'Laporan berhasil diperbarui.');
    }

    public function deleteLaporan($id)
    {
        $laporan = LaporanBarang::where('user_id', Auth::id())->findOrFail($id);

        if (!$laporan->canBeManagedByStudent()) {
            return back()->with('error', 'Laporan ini tidak dapat dihapus karena sudah dalam proses pengamanan petugas.');
        }

        $laporan->delete();

        return redirect()->route('mahasiswa.laporan.saya')->with('success', 'Laporan berhasil dihapus.');
    }

    public function klaimForm($id)
    {
        $laporan = LaporanBarang::with(['kampus', 'kategori', 'penyimpanan'])->findOrFail($id);

        if ($laporan->jenis_laporan !== 'DITEMUKAN') {
            return back()->with('error', 'Klaim hanya dapat diajukan untuk laporan barang yang ditemukan.');
        }

        if (in_array($laporan->status, ['DIKEMBALIKAN', 'DITOLAK'])) {
            return back()->with('error', 'Barang ini sudah dikembalikan kepada pemilik sah atau tidak tersedia.');
        }

        // Cek jika sudah pernah klaim
        $existing = Klaim::where('laporan_id', $laporan->id)->where('user_id', Auth::id())->first();
        if ($existing) {
            return redirect()->route('mahasiswa.klaim.saya')->with('error', 'Anda sudah mengajukan klaim untuk barang ini sebelumnya.');
        }

        return view('mahasiswa.ajukan-klaim', compact('laporan'));
    }

    public function submitKlaim(Request $request, $id)
    {
        $laporan = LaporanBarang::findOrFail($id);

        $validated = $request->validate([
            'deskripsi_klaim' => ['required', 'string', 'min:10'],
            'bukti_kepemilikan' => ['required', 'string', 'min:10'],
            'foto_bukti' => ['nullable', 'image', 'max:3072'],
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_bukti')) {
            $fotoPath = $request->file('foto_bukti')->store('klaim', 'public');
        }

        $klaim = Klaim::create([
            'laporan_id' => $laporan->id,
            'user_id' => Auth::id(),
            'deskripsi_klaim' => $validated['deskripsi_klaim'],
            'bukti_kepemilikan' => $validated['bukti_kepemilikan'],
            'foto_bukti' => $fotoPath,
            'status' => 'MENUNGGU VERIFIKASI',
        ]);

        // Notifikasi ke mahasiswa
        Notifikasi::create([
            'user_id' => Auth::id(),
            'judul' => 'Pengajuan Klaim Berhasil Dikirim',
            'pesan' => "Pengajuan klaim Anda untuk barang \"{$laporan->nama_barang}\" telah diterima dan sedang menunggu verifikasi petugas keamanan kampus {$laporan->kampus->nama_kampus}.",
            'link' => route('mahasiswa.klaim.saya'),
            'is_read' => false,
        ]);

        return redirect()->route('mahasiswa.klaim.saya')->with('success', 'Pengajuan klaim berhasil dikirim! Silakan tunggu verifikasi dari Petugas Keamanan.');
    }

    public function klaimSaya()
    {
        $klaimList = Klaim::with(['laporan.kampus', 'laporan.kategori', 'laporan.penyimpanan', 'petugas'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('mahasiswa.klaim-saya', compact('klaimList'));
    }

    public function notifikasi()
    {
        $notifikasis = Notifikasi::where('user_id', Auth::id())->latest()->paginate(15);
        return view('mahasiswa.notifikasi', compact('notifikasis'));
    }

    public function markNotifikasiAsRead($id)
    {
        $notif = Notifikasi::where('user_id', Auth::id())->findOrFail($id);
        $notif->update(['is_read' => true]);

        if ($notif->link) {
            return redirect($notif->link);
        }
        return back();
    }

    public function markAllNotifikasiAsRead()
    {
        Notifikasi::where('user_id', Auth::id())->update(['is_read' => true]);
        return back()->with('success', 'Semua notifikasi ditandai telah dibaca.');
    }

    private function generateKodeLaporan(): string
    {
        $year = date('Y');
        $count = LaporanBarang::whereYear('created_at', $year)->count() + 1;
        $padded = str_pad($count, 5, '0', STR_PAD_LEFT);
        $code = "FD-{$year}-{$padded}";

        while (LaporanBarang::where('kode_laporan', $code)->exists()) {
            $count++;
            $padded = str_pad($count, 5, '0', STR_PAD_LEFT);
            $code = "FD-{$year}-{$padded}";
        }

        return $code;
    }

    /**
     * PUSAT PELAPORAN TERPADU 1-PINTU (All-in-One Report Hub)
     * Menggabungkan Form Lapor Kehilangan dan Form Lapor Temuan dalam 1 halaman dengan tab switch mulus.
     */
    public function laporHub(Request $request)
    {
        $campuses = Kampus::where('status', 'aktif')->orderBy('nama_kampus')->get();
        $categories = Kategori::where('status', 'aktif')->orderBy('nama_kategori')->get();
        $activeTab = $request->query('tipe', 'hilang'); // 'hilang' atau 'temukan'

        return view('mahasiswa.lapor-hub', compact('campuses', 'categories', 'activeTab'));
    }

    /**
     * PUSAT AKTIVITAS MAHASISWA TERPADU (All-in-One Activity Hub)
     * Menggabungkan Laporan Saya, Klaim Saya & Tiket QR, dan Notifikasi dalam 1 halaman.
     */
    public function aktivitasHub(Request $request)
    {
        $activeTab = $request->query('tab', 'laporan'); // 'laporan', 'klaim', 'notifikasi'

        // 1. Data Laporan Saya
        $queryLaporan = LaporanBarang::with(['kampus', 'kategori'])
            ->where('user_id', Auth::id());
        if ($request->filled('jenis')) {
            $queryLaporan->where('jenis_laporan', $request->jenis);
        }
        $myReports = $queryLaporan->latest()->paginate(10, ['*'], 'page_laporan');

        // 2. Data Klaim Saya
        $myClaims = Klaim::with(['laporan.kampus', 'laporan.kategori', 'petugas'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10, ['*'], 'page_klaim');

        // 3. Notifikasi
        $notifikasis = Notifikasi::where('user_id', Auth::id())
            ->latest()
            ->paginate(10, ['*'], 'page_notif');

        $counts = [
            'laporan' => LaporanBarang::where('user_id', Auth::id())->count(),
            'klaim' => Klaim::where('user_id', Auth::id())->count(),
            'tiket_ready' => Klaim::where('user_id', Auth::id())->where('status', 'DISETUJUI')->count(),
            'notif_unread' => Notifikasi::where('user_id', Auth::id())->where('is_read', false)->count(),
        ];

        return view('mahasiswa.aktivitas-hub', compact('activeTab', 'myReports', 'myClaims', 'notifikasis', 'counts'));
    }
}
