<?php

namespace App\Http\Controllers\Belanja;

use App\Http\Controllers\Controller;
use App\Models\DaftarBelanja;
use App\Models\ItemBelanja;
use App\Models\PengeluaranBulanan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = now()->toDateString();

        // List belanja hari ini, pakai whereDate agar format tanggal cocok
        $listBelanjaHariIni = DaftarBelanja::where('user_id', $userId)
            ->whereDate('tanggal_belanja', $today)
            ->with('itemBelanjas')
            ->get();

        // Ringkasan harian (gunakan whereDate pada field created_at)
        $totalBarang = ItemBelanja::whereHas('daftarBelanja', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->whereDate('created_at', $today)
            ->count();

        $totalBelanja = ItemBelanja::whereHas('daftarBelanja', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->whereDate('created_at', $today)
            ->sum('total_harga');

        $barangSudahDibeli = ItemBelanja::whereHas('daftarBelanja', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->whereDate('created_at', $today)
            ->where('status', 'Sudah Dibeli')
            ->count();

        $barangBelumDibeli = ItemBelanja::whereHas('daftarBelanja', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->whereDate('created_at', $today)
            ->where('status', 'Belum Dibeli')
            ->count();

        // Pengeluaran bulan ini
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $pengeluaranBulanIni = PengeluaranBulanan::where('user_id', $userId)
            ->where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->first();

        //dd($listBelanjaHariIni);

        return view('dashboard.index', [
            'listBelanjaHariIni' => $listBelanjaHariIni,
            'totalBarang' => $totalBarang,
            'totalBelanja' => $totalBelanja,
            'barangSudahDibeli' => $barangSudahDibeli,
            'barangBelumDibeli' => $barangBelumDibeli,
            'pengeluaranBulanIni' => $pengeluaranBulanIni,
        ]);
    }
}
