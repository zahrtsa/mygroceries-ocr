<?php

namespace App\Http\Controllers\Belanja;

use App\Http\Controllers\Controller;
use App\Models\DaftarBelanja;
use App\Models\ItemBelanja;
use App\Models\PengeluaranBulanan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $userId = $user->id;
        $today  = now()->toDateString();

        // List belanja hari ini (barang)
        $listBelanjaHariIni = DaftarBelanja::where('user_id', $userId)
            ->whereDate('tanggal_belanja', $today)
            ->with('itemBelanjas')
            ->get();

        // Ringkasan harian -> tetap pakai ItemBelanja (khusus barang)
        $baseItemQuery = ItemBelanja::whereHas('daftarBelanja', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereDate('created_at', $today);

        $totalBarang       = (clone $baseItemQuery)->count();
        $totalBelanja      = (clone $baseItemQuery)->sum('total_harga');
        $barangSudahDibeli = (clone $baseItemQuery)->where('status', 'Sudah Dibeli')->count();
        $barangBelumDibeli = (clone $baseItemQuery)->where('status', 'Belum Dibeli')->count();

        // Rekap budget / pengeluaran bulan ini -> PAKAI PengeluaranBulanan
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $pengeluaranBulanIni = PengeluaranBulanan::where('user_id', $userId)
            ->where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->first();

        $total_pengeluaran = $pengeluaranBulanIni->total_pengeluaran ?? 0;
        $budget_bulanan    = $user->budget_bulanan ?? 0;

        // Pendapatan tahunan (opsional)
        $total_pendapatan = ($user->pendapatan_bulanan ?? 0) * 12;

        // Chart per hari juga sebaiknya pakai PengeluaranBulanan atau Receipt.
        // Kalau mau tetap pakai ItemBelanja untuk grafik barang, kita bedakan labelnya.
        $awalBulan  = Carbon::create($tahunIni, $bulanIni, 1)->startOfDay();
        $akhirBulan = (clone $awalBulan)->endOfMonth();

        // Grafik: pengeluaran harian berbasis PengeluaranBulanan sudah diakumulasi,
        // tetapi kalau mau tetap pakai item, kita buat jelas bahwa ini "total harga item",
        // bukan "real budget".
        $pengeluaranPerHari = ItemBelanja::selectRaw('DATE(created_at) as tanggal, SUM(total_harga) as total')
            ->whereHas('daftarBelanja', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereBetween('created_at', [$awalBulan, $akhirBulan])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $labelPengeluaran = $pengeluaranPerHari->map(function ($row) {
            return Carbon::parse($row->tanggal)->format('d');
        });

        $dataPengeluaran = $pengeluaranPerHari->pluck('total');

        return view('dashboard.index', [
            'listBelanjaHariIni' => $listBelanjaHariIni,

            // ringkasan barang harian
            'totalBarang'        => $totalBarang,
            'totalBelanja'       => $totalBelanja,
            'barangSudahDibeli'  => $barangSudahDibeli,
            'barangBelumDibeli'  => $barangBelumDibeli,

            // data budget & pengeluaran bulan (dari pengeluaran_bulanans)
            'pengeluaranBulanIni'=> $pengeluaranBulanIni,
            'budget_bulanan'     => $budget_bulanan,
            'total_pendapatan'   => $total_pendapatan,
            'total_pengeluaran'  => $total_pengeluaran,

            // grafik (berbasis item, jika masih ingin)
            'labelPengeluaran'   => $labelPengeluaran,
            'dataPengeluaran'    => $dataPengeluaran,
        ]);
    }
}
