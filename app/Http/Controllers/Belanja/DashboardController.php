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
        $user = Auth::user();
        $userId = $user->id;
        $today = now()->toDateString();

        // List belanja hari ini
        $listBelanjaHariIni = DaftarBelanja::where('user_id', $userId)
            ->whereDate('tanggal_belanja', $today)
            ->with('itemBelanjas')
            ->get();

        // Ringkasan harian
        $baseItemQuery = ItemBelanja::whereHas('daftarBelanja', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->whereDate('created_at', $today);

        $totalBarang = (clone $baseItemQuery)->count();
        $totalBelanja = (clone $baseItemQuery)->sum('total_harga');
        $barangSudahDibeli = (clone $baseItemQuery)->where('status', 'Sudah Dibeli')->count();
        $barangBelumDibeli = (clone $baseItemQuery)->where('status', 'Belum Dibeli')->count();

        // Pengeluaran bulan ini (rekap bulanan)
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $pengeluaranBulanIni = PengeluaranBulanan::where('user_id', $userId)
            ->where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->first();

        $total_pengeluaran = $pengeluaranBulanIni->total_pengeluaran ?? 0;
        $budget_bulanan = ($user->budget_bulanan ?? 0);

        // Pendapatan tahunan (contoh: pendapatan_bulanan * 12)
        $total_pendapatan = ($user->pendapatan_bulanan ?? 0) * 12;

        // (opsional) kalau mau tetap pakai bar chart nanti, bisa tetap hitung per hari
        $awalBulan = Carbon::create($tahunIni, $bulanIni, 1)->startOfDay();
        $akhirBulan = (clone $awalBulan)->endOfMonth();

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
            'totalBarang' => $totalBarang,
            'totalBelanja' => $totalBelanja,
            'barangSudahDibeli' => $barangSudahDibeli,
            'barangBelumDibeli' => $barangBelumDibeli,
            'pengeluaranBulanIni' => $pengeluaranBulanIni,
            'labelPengeluaran' => $labelPengeluaran,
            'dataPengeluaran' => $dataPengeluaran,
            'budget_bulanan' => $budget_bulanan,
            'total_pendapatan' => $total_pendapatan,
            'total_pengeluaran' => $total_pengeluaran,
        ]);
    }
}
