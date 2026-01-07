<?php

namespace App\Http\Controllers\Belanja;

use App\Http\Controllers\Controller;
use App\Models\DaftarBelanja;
use App\Models\ItemBelanja;
use App\Models\PengeluaranBulanan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BelanjaController extends Controller
{
    // ================= LIST & CRUD ITEM HARIAN =================

    // List semua item hari ini (GET /belanja/item)
    public function index(Request $request)
    {
        $tanggal = now()->toDateString();
        $search = $request->input('search');
        $userId = Auth::id();

        // Dapatkan atau auto-buat daftar belanja hari ini
        $daftar = DaftarBelanja::firstOrCreate(
            [
                'user_id' => $userId,
                'tanggal_belanja' => $tanggal,
            ],
            ['total_belanja' => 0]
        );

        $query = ItemBelanja::where('daftar_belanja_id', $daftar->id);

        // Search semua kolom
        if ($search) {
            $searchLower = strtolower($search);
            $query->where(function ($q) use ($searchLower) {
                $q->whereRaw('LOWER(nama_barang) LIKE ?', ["%{$searchLower}%"])
                    ->orWhereRaw('CAST(qty AS CHAR) LIKE ?', ["%{$searchLower}%"])
                    ->orWhereRaw('CAST(harga_satuan AS CHAR) LIKE ?', ["%{$searchLower}%"])
                    ->orWhereRaw('CAST(total_harga AS CHAR) LIKE ?', ["%{$searchLower}%"])
                    ->orWhereRaw('LOWER(status) LIKE ?', ["%{$searchLower}%"]);
            });
        }

        // PAGINATION untuk list belanja
        $itemBelanjas = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $tanggalBelanja = now();

        return view('belanja.index', compact('itemBelanjas', 'tanggalBelanja'));
    }

    // Form tambah item (GET /belanja/item/create)
    public function create()
    {
        return view('belanja.create');
    }

    // Simpan item baru (POST /belanja/item)
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $userId = $user->id;
        $tanggal = now()->toDateString();

        $bulanIni = now()->month;
        $tahunIni = now()->year;

        // Total belanja bulan ini (semua item, apa pun status)
        $totalBelanjaBulanIni = ItemBelanja::whereHas('daftarBelanja', function ($q) use ($userId, $bulanIni, $tahunIni) {
            $q->where('user_id', $userId)
              ->whereMonth('tanggal_belanja', $bulanIni)
              ->whereYear('tanggal_belanja', $tahunIni);
        })
            ->sum('total_harga');

        $budgetBulanan = $user->budget_bulanan ?? 0;
        $total_harga = $request->qty * $request->harga_satuan;

        // Cek batas budget sebelum simpan
        if ($budgetBulanan > 0 && ($totalBelanjaBulanIni + $total_harga) > $budgetBulanan) {
            $sisaBudget = max($budgetBulanan - $totalBelanjaBulanIni, 0);

            return redirect()
                ->route('belanja.item.index')
                ->with(
                    'error',
                    'Belanja bulan ini sudah melewati atau akan melewati budget bulanan. '.
                    'Budget bulanan: Rp '.number_format($budgetBulanan, 0, ',', '.').
                    ' | Sisa budget: Rp '.number_format($sisaBudget, 0, ',', '.')
                );
        }

        // Masih dalam batas budget -> simpan
        $daftar = DaftarBelanja::firstOrCreate(
            [
                'user_id' => $userId,
                'tanggal_belanja' => $tanggal,
            ],
            ['total_belanja' => 0]
        );

        $item = ItemBelanja::create([
            'daftar_belanja_id' => $daftar->id,
            'nama_barang' => $request->nama_barang,
            'qty' => $request->qty,
            'harga_satuan' => $request->harga_satuan,
            'total_harga' => $total_harga,
            'status' => 'Belum Dibeli',
        ]);

        $daftar->update([
            'total_belanja' => $daftar->itemBelanjas()->sum('total_harga'),
        ]);

        // Item baru default "Belum Dibeli", jadi belum menyentuh PengeluaranBulanan

        return redirect()->route('belanja.item.index')->with('success', 'Item berhasil ditambahkan!');
    }

    // Form edit barang (GET /belanja/item/{item}/edit)
    public function edit(ItemBelanja $item)
    {
        if ($item->daftarBelanja->user_id !== Auth::id()) {
            abort(403);
        }

        return view('belanja.edit', compact('item'));
    }

    // Simpan perubahan (PATCH /belanja/item/{item})
    public function update(Request $request, ItemBelanja $item)
    {
        if ($item->daftarBelanja->user_id !== Auth::id()) {
            abort(403);
        }

        $user = Auth::user();

        // MODE TOGGLE STATUS CEPAT (dari list)
        if ($request->has('status') && !$request->has('nama_barang')) {
            $request->validate([
                'status' => 'required|in:Sudah Dibeli,Belum Dibeli',
            ]);

            $statusBaru = $request->status;
            $statusLama = $item->status;

            if ($statusBaru === $statusLama) {
                return back();
            }

            $budgetBulanan = $user->budget_bulanan ?? 0;

            if ($budgetBulanan > 0) {
                $userId = $user->id;
                $tanggal = $item->daftarBelanja->tanggal_belanja;
                $bulanIni = $tanggal->month;
                $tahunIni = $tanggal->year;

                // Total "Sudah Dibeli" lain di bulan ini (kecuali item ini)
                $totalSudahDibeli = ItemBelanja::whereHas('daftarBelanja', function ($q) use ($userId, $bulanIni, $tahunIni) {
                    $q->where('user_id', $userId)
                      ->whereMonth('tanggal_belanja', $bulanIni)
                      ->whereYear('tanggal_belanja', $tahunIni);
                })
                    ->where('status', 'Sudah Dibeli')
                    ->where('id', '!=', $item->id)
                    ->sum('total_harga');

                // Kalau status baru 'Sudah Dibeli', tambahkan harga item ini
                if ($statusBaru === 'Sudah Dibeli') {
                    $totalSudahDibeli += $item->total_harga;
                }

                if ($totalSudahDibeli > $budgetBulanan) {
                    $sisaBudget = max($budgetBulanan - ($totalSudahDibeli - $item->total_harga), 0);

                    return back()->with(
                        'error',
                        'Tidak bisa menandai item ini sebagai "Sudah Dibeli" karena akan melewati budget bulanan. '.
                        'Budget bulanan: Rp '.number_format($budgetBulanan, 0, ',', '.').
                        ' | Sisa budget: Rp '.number_format($sisaBudget, 0, ',', '.')
                    );
                }
            }

            // Lolos cek budget -> update status
            $item->update([
                'status' => $statusBaru,
            ]);

            // Update pengeluaran bulanan berdasarkan perubahan status
            $selisih = 0;
            if ($statusLama === 'Belum Dibeli' && $statusBaru === 'Sudah Dibeli') {
                $selisih = +$item->total_harga;
            } elseif ($statusLama === 'Sudah Dibeli' && $statusBaru === 'Belum Dibeli') {
                $selisih = -$item->total_harga;
            }

            if ($selisih != 0) {
                $this->updatePengeluaranBulananFromItem($item, $selisih);
            }

            return back()->with('success', 'Status berhasil diubah!');
        }

        // MODE UPDATE PENUH (dari form edit)
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'status' => 'required|in:Sudah Dibeli,Belum Dibeli',
        ]);

        $oldTotal = $item->total_harga;
        $oldStatus = $item->status;

        $item->update([
            'nama_barang' => $request->nama_barang,
            'qty' => $request->qty,
            'harga_satuan' => $request->harga_satuan,
            'total_harga' => $request->qty * $request->harga_satuan,
            'status' => $request->status,
        ]);

        $item->daftarBelanja->update([
            'total_belanja' => $item->daftarBelanja->itemBelanjas()->sum('total_harga'),
        ]);

        $newTotal = $item->total_harga;
        $newStatus = $item->status;

        // Hitung selisih pengeluaran hanya untuk bagian "Sudah Dibeli"
        $selisih = 0;
        if ($oldStatus === 'Sudah Dibeli' && $newStatus === 'Sudah Dibeli') {
            $selisih = $newTotal - $oldTotal;
        } elseif ($oldStatus === 'Belum Dibeli' && $newStatus === 'Sudah Dibeli') {
            $selisih = +$newTotal;
        } elseif ($oldStatus === 'Sudah Dibeli' && $newStatus === 'Belum Dibeli') {
            $selisih = -$oldTotal;
        }

        if ($selisih != 0) {
            $this->updatePengeluaranBulananFromItem($item, $selisih);
        }

        return redirect()->route('belanja.item.index')->with('success', 'Item berhasil diedit!');
    }

    // Hapus item (DELETE /belanja/item/{item})
    public function destroy(ItemBelanja $item)
    {
        if ($item->daftarBelanja->user_id !== Auth::id()) {
            abort(403);
        }

        // Jika item yang dihapus sudah dibeli, kurangi pengeluaran bulanan
        if ($item->status === 'Sudah Dibeli' && $item->total_harga > 0) {
            $this->updatePengeluaranBulananFromItem($item, -$item->total_harga);
        }

        $daftar = $item->daftarBelanja;
        $item->delete();

        $daftar->update([
            'total_belanja' => $daftar->itemBelanjas()->sum('total_harga'),
        ]);

        return redirect()->route('belanja.item.index')->with('success', 'Item berhasil dihapus!');
    }

    // ================= REKAP HARIAN =================

    public function rekapanHarian(Request $request)
    {
        $userId = Auth::id();
        $tanggal = $request->input('tanggal', now()->toDateString());

        $daftar = DaftarBelanja::where('user_id', $userId)
            ->where('tanggal_belanja', $tanggal)
            ->first();

        if ($daftar) {
            $items = ItemBelanja::where('daftar_belanja_id', $daftar->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString();

            $totalBelanja = ItemBelanja::where('daftar_belanja_id', $daftar->id)
                ->sum('total_harga');
        } else {
            $items = collect();
            $totalBelanja = 0;
        }

        $optionsTanggal = DaftarBelanja::where('user_id', $userId)
            ->orderBy('tanggal_belanja', 'desc')
            ->pluck('tanggal_belanja')
            ->unique();

        return view('belanja.rekapanharian', compact('items', 'tanggal', 'totalBelanja', 'optionsTanggal'));
    }

    // ================= LAPORAN PENGELUARAN TAHUNAN =================

    // GET /belanja/pengeluaran
    public function pengeluaranIndex(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        // Tahun yang dipilih (default: tahun sekarang)
        $tahun = (int) $request->input('tahun', now()->year);

        // Daftar tahun yang punya data pengeluaran
        $daftar_tahun = PengeluaranBulanan::where('user_id', $userId)
            ->select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

        if (empty($daftar_tahun)) {
            $daftar_tahun = [$tahun];
        }

        // Data pengeluaran satu tahun
        $pengeluaran_tahunan = PengeluaranBulanan::where('user_id', $userId)
            ->where('tahun', $tahun)
            ->get();

        $adaDataTahunIni = $pengeluaran_tahunan->isNotEmpty();

        // Sesuaikan perhitungan ini dengan field di model User/PengeluaranBulanan milikmu
        $total_pendapatan = $user->total_pendapatan_tahun ?? 0;          // kalau belum ada, sementara 0
        $budget_belanja = $user->budget_bulanan ? $user->budget_bulanan * 12 : 0;
        $total_pengeluaran = $pengeluaran_tahunan->sum('total_pengeluaran');
        $saldo_bersih = $total_pendapatan - $total_pengeluaran;

        // Persentase untuk donut
        $total_for_pie = max($total_pendapatan, 0);
        if ($total_for_pie <= 0) {
            $persen_pendapatan = 0;
            $persen_pengeluaran = 0;
            $persen_saldo = 0;
        } else {
            $persen_pengeluaran = round(($total_pengeluaran / $total_for_pie) * 100);
            $persen_saldo = round((max($saldo_bersih, 0) / $total_for_pie) * 100);
            $persen_pendapatan = max(0, 100 - $persen_pengeluaran - $persen_saldo);
        }

        // Laporan bulanan dengan nama bulan lengkap
        $laporan_bulanan = [];
        for ($bulan = 1; $bulan <= 12; ++$bulan) {
            $totalBelanjaBulan = $pengeluaran_tahunan
                ->where('bulan', $bulan)
                ->sum('total_pengeluaran');

            $laporan_bulanan[] = [
                'bulan' => Carbon::create(null, $bulan, 1)->translatedFormat('F'),
                'total_belanja' => $totalBelanjaBulan,
            ];
        }

        return view('belanja.pengeluaran-index', [
            'tahun' => $tahun,
            'daftar_tahun' => $daftar_tahun,
            'adaDataTahunIni' => $adaDataTahunIni,
            'total_pendapatan' => $total_pendapatan,
            'budget_belanja' => $budget_belanja,
            'total_pengeluaran' => $total_pengeluaran,
            'saldo_bersih' => $saldo_bersih,
            'persen_pendapatan' => $persen_pendapatan,
            'persen_pengeluaran' => $persen_pengeluaran,
            'persen_saldo' => $persen_saldo,
            'laporan_bulanan' => $laporan_bulanan,
        ]);
    }

    // ================= HELPER PENGELUARAN DARI ITEM =================

    private function updatePengeluaranBulananFromItem(ItemBelanja $item, float $pertambahan)
    {
        $user = $item->daftarBelanja->user;
        $date = $item->daftarBelanja->tanggal_belanja ?? now();
        $bulan = $date->format('n');
        $tahun = $date->format('Y');

        $pengeluaran = PengeluaranBulanan::firstOrCreate(
            [
                'user_id' => $user->id,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ],
            [
                'total_pengeluaran' => 0,
                'saldo_bersih' => 0,
            ]
        );

        $pengeluaran->total_pengeluaran += $pertambahan;
        $pengeluaran->saldo_bersih = ($user->budget_bulanan ?? 0) - $pengeluaran->total_pengeluaran;
        $pengeluaran->save();
    }
}
