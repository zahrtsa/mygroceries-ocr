<?php

namespace App\Http\Controllers\Belanja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaftarBelanja;
use App\Models\ItemBelanja;
use App\Models\PengeluaranBulanan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // List belanja hari ini
        $today = now()->toDateString();
        $listBelanjaHariIni = DaftarBelanja::where('user_id', $userId)
            ->where('tanggal_belanja', $today)
            ->with('items') // load relasi items
            ->get();

        // Ringkasan harian
        $totalBarang = ItemBelanja::whereHas('daftarBelanja', fn($q) => $q->where('user_id', $userId))
            ->whereDate('created_at', $today)
            ->count();

        $totalBelanja = ItemBelanja::whereHas('daftarBelanja', fn($q) => $q->where('user_id', $userId))
            ->whereDate('created_at', $today)
            ->sum('total_harga');

        $barangSudahDibeli = ItemBelanja::whereHas('daftarBelanja', fn($q) => $q->where('user_id', $userId))
            ->whereDate('created_at', $today)
            ->where('status', 'Sudah Dibeli')
            ->count();

        $barangBelumDibeli = ItemBelanja::whereHas('daftarBelanja', fn($q) => $q->where('user_id', $userId))
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
