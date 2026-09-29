<?php

namespace App\Http\Controllers;

use App\Models\Kampus;
use App\Models\Kategori;
use App\Models\Klaim;
use App\Models\LaporanBarang;
use App\Models\Pengembalian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_laporan' => LaporanBarang::count(),
            'barang_hilang' => LaporanBarang::where('jenis_laporan', 'HILANG')->count(),
            'barang_ditemukan' => LaporanBarang::where('jenis_laporan', 'DITEMUKAN')->count(),
            'barang_diamankan' => LaporanBarang::where('status', 'BARANG DIAMANKAN')->count(),
            'barang_dikembalikan' => LaporanBarang::where('status', 'DIKEMBALIKAN')->count(),
            'total_mahasiswa' => User::where('role', 'mahasiswa')->count(),
            'total_petugas' => User::where('role', 'petugas')->count(),
            'total_kampus' => Kampus::count(),
        ];

        // Laporan per Kampus
        $laporanPerKampus = Kampus::withCount('laporanBarang')
            ->having('laporan_barang_count', '>', 0)
            ->orderByDesc('laporan_barang_count')
            ->take(8)
            ->get();

        // Laporan per Kategori
        $laporanPerKategori = Kategori::withCount('laporanBarang')
            ->orderByDesc('laporan_barang_count')
            ->get();

        // Statistik Status Barang
        $statusCounts = [
            'Sedang Dicari' => LaporanBarang::where('status', 'SEDANG DICARI')->count(),
            'Menunggu Verifikasi' => LaporanBarang::where('status', 'MENUNGGU VERIFIKASI')->count(),
            'Ada Kemungkinan Cocok' => LaporanBarang::where('status', 'ADA KEMUNGKINAN COCOK')->count(),
            'Barang Diamankan' => LaporanBarang::where('status', 'BARANG DIAMANKAN')->count(),
            'Siap Diambil' => LaporanBarang::where('status', 'SIAP DIAMBIL')->count(),
            'Dikembalikan' => LaporanBarang::where('status', 'DIKEMBALIKAN')->count(),
        ];

        $recentLaporans = LaporanBarang::with(['kampus', 'kategori', 'user'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'laporanPerKampus', 'laporanPerKategori', 'statusCounts', 'recentLaporans'));
    }

    // ==========================================
    // MANAJEMEN MAHASISWA
    // ==========================================
    public function users(Request $request)
    {
        $query = User::with('kampus')->where('role', 'mahasiswa');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('email', 'LIKE', "%{$q}%")
                    ->orWhere('nim', 'LIKE', "%{$q}%");
            });
        }

        if ($request->filled('kampus_id')) {
            $query->where('kampus_id', $request->kampus_id);
        }

        $users = $query->latest()->paginate(12)->withQueryString();
        $campuses = Kampus::orderBy('nama_kampus')->get();

        return view('admin.users', compact('users', 'campuses'));
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun mahasiswa {$user->name} berhasil {$statusStr}.");
    }

    /**
     * Tambah Data Mahasiswa Manual
     * Format email resmi UBSI: nim@bsi.ac.id
     * Password otomatis diset = tanggal lahir format yyyy-mm-dd
     */
    public function simpanUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:30', 'unique:users,nim'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'kampus_id' => ['required', 'exists:kampus,id'],
            'no_telp' => ['nullable', 'string', 'max:25'],
        ]);

        $nim = trim($validated['nim']);
        $tglLahir = Carbon::parse($validated['tanggal_lahir'])->format('Y-m-d');
        // Email mahasiswa resmi UBSI selalu berformat nim@bsi.ac.id
        $email = "{$nim}@bsi.ac.id";

        $user = User::updateOrCreate(
            ['nim' => $nim],
            [
                'name' => $validated['name'],
                'email' => $email,
                'tanggal_lahir' => $tglLahir,
                'kampus_id' => $validated['kampus_id'],
                'no_telp' => $validated['no_telp'] ?? null,
                'role' => 'mahasiswa',
                'is_active' => true,
                'password' => Hash::make($tglLahir), // Password = tanggal lahir format yyyy-mm-dd
            ]
        );

        return redirect()->route('admin.users')
            ->with('success', "Mahasiswa {$user->name} (NIM: {$nim}, Email: {$email}) berhasil ditambahkan. Login: Username = NIM, Password = {$tglLahir} (YYYY-MM-DD).");
    }

    /**
     * Import Data Mahasiswa dari Excel / CSV
     * Kolom wajib: Nama Mahasiswa, NIM, Tanggal Lahir (YYYY-MM-DD), Kampus Terdaftar
     * Email otomatis: nim@bsi.ac.id
     * Password otomatis: tanggal lahir format yyyy-mm-dd
     */
    public function importExcelUser(Request $request)
    {
        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:10240'],
            'default_kampus_id' => ['nullable', 'exists:kampus,id'],
        ]);

        $file = $request->file('file_excel');
        $defaultKampusId = $request->input('default_kampus_id') ?? Kampus::first()?->id;

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file spreadsheet: ' . $e->getMessage());
        }

        if (empty($rows)) {
            return back()->with('error', 'File Excel kosong atau tidak berisi data.');
        }

        // Cari baris header atau tentukan indeks kolom default (5 Kolom)
        $headerRowIndex = null;
        $colMap = [
            'nama' => 'A',
            'nim' => 'B',
            'tanggal_lahir' => 'C',
            'kampus' => 'D',
            'no_telp' => 'E',
            'email' => null,
        ];

        // Deteksi header dari baris-baris pertama secara fleksibel
        $rowCounter = 0;
        foreach ($rows as $rowIndex => $row) {
            $rowCounter++;
            if ($rowCounter > 5) break;

            foreach ($row as $colKey => $cellValue) {
                $cellLower = strtolower(trim((string)$cellValue));

                // Deteksi kolom kampus terdaftar terlebih dahulu sebelum nama
                if (str_contains($cellLower, 'kampus') || str_contains($cellLower, 'cabang') || str_contains($cellLower, 'asal') || str_contains($cellLower, 'lokasi')) {
                    $colMap['kampus'] = $colKey;
                    $headerRowIndex = $rowIndex;
                } elseif (str_contains($cellLower, 'nim') || str_contains($cellLower, 'nomor induk') || str_contains($cellLower, 'npm')) {
                    $colMap['nim'] = $colKey;
                    $headerRowIndex = $rowIndex;
                } elseif (str_contains($cellLower, 'tgl') || str_contains($cellLower, 'lahir') || str_contains($cellLower, 'birth') || str_contains($cellLower, 'tanggal')) {
                    $colMap['tanggal_lahir'] = $colKey;
                    $headerRowIndex = $rowIndex;
                } elseif (str_contains($cellLower, 'wa') || str_contains($cellLower, 'whatsapp') || str_contains($cellLower, 'telp') || str_contains($cellLower, 'phone') || str_contains($cellLower, 'hp') || str_contains($cellLower, 'kontak')) {
                    $colMap['no_telp'] = $colKey;
                    $headerRowIndex = $rowIndex;
                } elseif (str_contains($cellLower, 'nama') || str_contains($cellLower, 'name') || str_contains($cellLower, 'mahasiswa') || str_contains($cellLower, 'mhs')) {
                    $colMap['nama'] = $colKey;
                    $headerRowIndex = $rowIndex;
                }
            }

            if ($headerRowIndex !== null) {
                break;
            }
        }

        $allCampuses = Kampus::all();
        $importedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($rows as $rowIndex => $row) {
            // Lewati baris header jika ditemukan
            if ($headerRowIndex !== null && $rowIndex <= $headerRowIndex) {
                continue;
            }

            $nama = trim((string)($row[$colMap['nama']] ?? ''));
            $nim = trim((string)($row[$colMap['nim']] ?? ''));
            $tglRaw = $row[$colMap['tanggal_lahir']] ?? null;

            // Bersihkan NIM
            $nim = preg_replace('/[^a-zA-Z0-9_-]/', '', $nim);

            if (empty($nama) || empty($nim)) {
                $skippedCount++;
                continue;
            }

            // Normalisasi Tanggal Lahir (Mendukung Serial Excel, YYYY-MM-DD, DD/MM/YYYY, dsb.)
            $tglLahir = null;
            if (!empty($tglRaw)) {
                if (is_numeric($tglRaw)) {
                    try {
                        $tglLahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$tglRaw)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $tglLahir = null;
                    }
                } else {
                    $tglString = trim((string)$tglRaw);
                    try {
                        $tglLahir = Carbon::parse($tglString)->format('Y-m-d');
                    } catch (\Exception $e) {
                        if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/', $tglString, $m)) {
                            $tglLahir = sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
                        } elseif (preg_match('/^(\d{4})[\/\-\.](\d{1,2})[\/\-\.](\d{1,2})$/', $tglString, $m)) {
                            $tglLahir = sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
                        }
                    }
                }
            }

            if (!$tglLahir) {
                $tglLahir = '2004-01-01'; // Fallback aman
            }

            // Deteksi Kampus Terdaftar dari Excel
            $kampusId = $defaultKampusId;
            if (!empty($colMap['kampus']) && !empty($row[$colMap['kampus']])) {
                $rawKampus = trim((string)$row[$colMap['kampus']]);
                $cleanRaw = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $rawKampus));

                $matchedKmp = $allCampuses->first(function ($item) use ($rawKampus, $cleanRaw) {
                    $cleanKode = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $item->kode_kampus));
                    $cleanKodeShort = str_replace('KMP', '', $cleanKode);

                    // 1. Cocokkan kode kampus (KMP-KRM, KRM, dsb.)
                    if ($cleanRaw === $cleanKode || $cleanRaw === $cleanKodeShort) {
                        return true;
                    }

                    // 2. Cocokkan nama kampus utuh atau substring
                    if (stripos($item->nama_kampus, $rawKampus) !== false || stripos($rawKampus, $item->nama_kampus) !== false) {
                        return true;
                    }

                    // 3. Cocokkan nama cabang tanpa embel-embel "UBSI Kampus" dan tanda kurung kota
                    $stripped = trim(preg_replace('/^UBSI Kampus /i', '', $item->nama_kampus));
                    $cleanName = trim(preg_replace('/\s*\(.*?\)/', '', $stripped)); // e.g. "Kramat 98", "Kaliabang"
                    if (stripos($rawKampus, $cleanName) !== false || stripos($cleanName, $rawKampus) !== false) {
                        return true;
                    }

                    // 4. Word-by-word token matching (misal: "Kramat", "Kaliabang", "Margonda", "Salemba")
                    $words = explode(' ', strtolower($rawKampus));
                    foreach ($words as $w) {
                        $w = trim($w);
                        if (strlen($w) >= 4 && !in_array($w, ['ubsi', 'kampus', 'gedung', 'kota', 'pusat', 'timur', 'barat', 'utara', 'selatan'])) {
                            if (stripos($item->nama_kampus, $w) !== false) {
                                return true;
                            }
                        }
                    }

                    return false;
                });

                if ($matchedKmp) {
                    $kampusId = $matchedKmp->id;
                }
            }

            // Format Email resmi Mahasiswa UBSI: nim@bsi.ac.id
            $email = "{$nim}@bsi.ac.id";

            // Normalisasi Nomor WhatsApp Aktif
            $noTelp = null;
            if (!empty($colMap['no_telp']) && !empty($row[$colMap['no_telp']])) {
                $rawTelp = trim((string)$row[$colMap['no_telp']]);
                $cleanTelp = preg_replace('/[^0-9+]/', '', $rawTelp);
                // Standarisasi awalan 62 / +62 ke format 08
                if (str_starts_with($cleanTelp, '+62')) {
                    $cleanTelp = '0' . substr($cleanTelp, 3);
                } elseif (str_starts_with($cleanTelp, '62') && strlen($cleanTelp) > 10) {
                    $cleanTelp = '0' . substr($cleanTelp, 2);
                }
                $noTelp = $cleanTelp;
            }

            $existing = User::where('nim', $nim)->orWhere('email', $email)->first();

            if ($existing) {
                $existing->update([
                    'name' => $nama,
                    'nim' => $nim,
                    'email' => $email,
                    'tanggal_lahir' => $tglLahir,
                    'kampus_id' => $kampusId,
                    'no_telp' => $noTelp ?? $existing->no_telp,
                    'password' => Hash::make($tglLahir), // Password = tanggal lahir format yyyy-mm-dd
                    'is_active' => true,
                ]);
                $updatedCount++;
            } else {
                User::create([
                    'name' => $nama,
                    'nim' => $nim,
                    'email' => $email,
                    'tanggal_lahir' => $tglLahir,
                    'kampus_id' => $kampusId,
                    'no_telp' => $noTelp,
                    'role' => 'mahasiswa',
                    'is_active' => true,
                    'password' => Hash::make($tglLahir), // Password = tanggal lahir format yyyy-mm-dd
                ]);
                $importedCount++;
            }
        }

        $msg = "Proses import berhasil: {$importedCount} data mahasiswa baru ditambahkan";
        if ($updatedCount > 0) {
            $msg .= ", {$updatedCount} data diperbarui";
        }
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} baris kosong/dilewati)";
        }
        $msg .= ". Nomor WA aktif, email resmi, dan kata sandi login (YYYY-MM-DD) otomatis diset.";

        return redirect()->route('admin.users')->with('success', $msg);
    }

    /**
     * Download Template File Excel (.xlsx) Mahasiswa Tanpa Data Dummy
     * Format 5 Kolom: Nama Mahasiswa, NIM, Tanggal Lahir (YYYY-MM-DD), Kampus Terdaftar, No WhatsApp Aktif
     */
    public function downloadTemplateUser()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Mahasiswa');

        // Header Kolom (5 Kolom Utama: Nama, NIM, Tanggal Lahir, Kampus Terdaftar, No WhatsApp Aktif)
        $headers = [
            'A1' => 'Nama Mahasiswa',
            'B1' => 'NIM',
            'C1' => 'Tanggal Lahir (YYYY-MM-DD)',
            'D1' => 'Kampus Terdaftar',
            'E1' => 'No WhatsApp Aktif',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Styling Header (Warna Navy UBSI #1E3A8A, Teks Putih Bold, Center)
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 11,
                'name' => 'Calibri',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1E3A8A'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF94A3B8'],
                ],
            ],
        ];

        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Format kolom sebagai Text (@) agar data angka/tanggal/no WA tidak diubah otomatis oleh Excel
        $sheet->getStyle('A')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        $sheet->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        $sheet->getStyle('C')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        $sheet->getStyle('D')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        $sheet->getStyle('E')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

        // Atur lebar kolom yang pas dan rapi
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(38);
        $sheet->getColumnDimension('E')->setWidth(26);

        // Freeze baris header
        $sheet->freezePane('A2');

        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_import_mahasiswa.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
        ]);
    }

    // ==========================================
    // MANAJEMEN PETUGAS KEAMANAN
    // ==========================================
    public function petugas()
    {
        $officers = User::with('kampus')->where('role', 'petugas')->latest()->paginate(10);
        $campuses = Kampus::where('status', 'aktif')->orderBy('nama_kampus')->get();

        return view('admin.petugas', compact('officers', 'campuses'));
    }

    public function simpanPetugas(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'nim' => ['nullable', 'string', 'max:50'],
            'no_telp' => ['required', 'string', 'max:20'],
            'kampus_id' => ['required', 'exists:kampus,id'],
            'password' => ['required', Password::min(6)],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nim' => $validated['nim'],
            'no_telp' => $validated['no_telp'],
            'kampus_id' => $validated['kampus_id'],
            'role' => 'petugas',
            'is_active' => true,
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Akun Petugas Keamanan berhasil ditambahkan.');
    }

    public function updatePetugas(Request $request, $id)
    {
        $petugas = User::where('role', 'petugas')->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $petugas->id],
            'nim' => ['nullable', 'string', 'max:50'],
            'no_telp' => ['required', 'string', 'max:20'],
            'kampus_id' => ['required', 'exists:kampus,id'],
            'password' => ['nullable', Password::min(6)],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nim' => $validated['nim'],
            'no_telp' => $validated['no_telp'],
            'kampus_id' => $validated['kampus_id'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $petugas->update($data);

        return back()->with('success', "Data petugas {$petugas->name} berhasil diperbarui.");
    }

    public function togglePetugasStatus($id)
    {
        $petugas = User::where('role', 'petugas')->findOrFail($id);
        $petugas->is_active = !$petugas->is_active;
        $petugas->save();

        $statusStr = $petugas->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun petugas {$petugas->name} berhasil {$statusStr}.");
    }

    // ==========================================
    // MANAJEMEN MULTI-KAMPUS
    // ==========================================
    public function kampus(Request $request)
    {
        $query = Kampus::withCount(['laporanBarang', 'users']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('nama_kampus', 'LIKE', "%{$q}%")
                ->orWhere('kota', 'LIKE', "%{$q}%")
                ->orWhere('kode_kampus', 'LIKE', "%{$q}%");
        }

        $campuses = $query->orderBy('nama_kampus')->paginate(12)->withQueryString();

        return view('admin.kampus', compact('campuses'));
    }

    public function simpanKampus(Request $request)
    {
        $validated = $request->validate([
            'kode_kampus' => ['required', 'string', 'max:30', 'unique:kampus,kode_kampus'],
            'nama_kampus' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kota' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        Kampus::create($validated);

        return back()->with('success', 'Kampus UBSI baru berhasil ditambahkan.');
    }

    public function updateKampus(Request $request, $id)
    {
        $kampus = Kampus::findOrFail($id);

        $validated = $request->validate([
            'kode_kampus' => ['required', 'string', 'max:30', 'unique:kampus,kode_kampus,' . $kampus->id],
            'nama_kampus' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kota' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $kampus->update($validated);

        return back()->with('success', "Data {$kampus->nama_kampus} berhasil diperbarui.");
    }

    public function toggleKampusStatus($id)
    {
        $kampus = Kampus::findOrFail($id);
        $kampus->status = ($kampus->status === 'aktif') ? 'nonaktif' : 'aktif';
        $kampus->save();

        return back()->with('success', "Status kampus {$kampus->nama_kampus} diubah menjadi {$kampus->status}.");
    }

    // ==========================================
    // MANAJEMEN KATEGORI BARANG
    // ==========================================
    public function kategori()
    {
        $kategoris = Kategori::withCount('laporanBarang')->orderBy('nama_kategori')->get();
        return view('admin.kategori', compact('kategoris'));
    }

    public function simpanKategori(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori,nama_kategori'],
            'icon' => ['required', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        Kategori::create($validated);

        return back()->with('success', 'Kategori barang baru berhasil ditambahkan.');
    }

    public function updateKategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori,nama_kategori,' . $kategori->id],
            'icon' => ['required', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $kategori->update($validated);

        return back()->with('success', "Kategori {$kategori->nama_kategori} berhasil diperbarui.");
    }

    // ==========================================
    // MODERASI SELURUH LAPORAN
    // ==========================================
    public function semuaLaporan(Request $request)
    {
        $query = LaporanBarang::with(['kampus', 'kategori', 'user', 'penyimpanan']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_barang', 'LIKE', "%{$q}%")
                    ->orWhere('kode_laporan', 'LIKE', "%{$q}%");
            });
        }

        if ($request->filled('kampus_id')) {
            $query->where('kampus_id', $request->kampus_id);
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_laporan', $request->jenis);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $laporans = $query->latest()->paginate(15)->withQueryString();
        $campuses = Kampus::orderBy('nama_kampus')->get();
        $categories = Kategori::orderBy('nama_kategori')->get();

        return view('admin.semua-laporan', compact('laporans', 'campuses', 'categories'));
    }

    public function hapusLaporan($id)
    {
        $laporan = LaporanBarang::findOrFail($id);
        $kode = $laporan->kode_laporan;
        $laporan->delete();

        return back()->with('success', "Laporan {$kode} berhasil dihapus oleh admin.");
    }

    // ==========================================
    // MONITORING SELURUH KLAIM & PENGEMBALIAN
    // ==========================================
    public function semuaKlaim(Request $request)
    {
        $query = Klaim::with(['laporan.kampus', 'laporan.kategori', 'user', 'petugas']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $klaims = $query->latest()->paginate(15)->withQueryString();

        return view('admin.semua-klaim', compact('klaims'));
    }

    public function statistik()
    {
        $totalLaporan = LaporanBarang::count();
        $totalHilang = LaporanBarang::where('jenis_laporan', 'HILANG')->count();
        $totalDitemukan = LaporanBarang::where('jenis_laporan', 'DITEMUKAN')->count();
        $totalDikembalikan = LaporanBarang::where('status', 'DIKEMBALIKAN')->count();
        $successRate = $totalLaporan > 0 ? round(($totalDikembalikan / $totalLaporan) * 100, 1) : 0;

        $kampusStats = Kampus::withCount([
            'laporanBarang',
            'laporanBarang as hilang_count' => fn($q) => $q->where('jenis_laporan', 'HILANG'),
            'laporanBarang as ditemukan_count' => fn($q) => $q->where('jenis_laporan', 'DITEMUKAN'),
            'laporanBarang as returned_count' => fn($q) => $q->where('status', 'DIKEMBALIKAN'),
        ])->orderByDesc('laporan_barang_count')->get();

        $kategoriStats = Kategori::withCount('laporanBarang')->orderByDesc('laporan_barang_count')->get();

        return view('admin.statistik', compact('totalLaporan', 'totalHilang', 'totalDitemukan', 'totalDikembalikan', 'successRate', 'kampusStats', 'kategoriStats'));
    }
}
