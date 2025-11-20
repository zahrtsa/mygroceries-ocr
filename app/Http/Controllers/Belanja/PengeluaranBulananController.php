<?php

namespace App\Http\Controllers\Belanja;

use App\Http\Controllers\Controller;
use App\Models\PengeluaranBulanan;
use Illuminate\Support\Facades\Auth;

class PengeluaranBulananController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tahun = now()->year;

        // Total pendapatan (asumsi fixed di field user)
        $total_pendapatan = $user->pendapatan_bulanan * 12;
        $budget_belanja = $user->budget_bulanan * 12;

        // Ambil data pengeluaran bulanan tahun ini (dari tabel pengeluaran_bulanans)
        $rekap = PengeluaranBulanan::where('user_id', $user->id)
            ->where('tahun', $tahun)
            ->orderBy('bulan')
            ->get();

        $saldo_bersih = $rekap->sum('saldo_bersih');

        // Untuk display tabel Jan/Feb/Mar Dst
        $daftar_bulanan = [];
        $nama_bulan = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
        foreach ($rekap as $peng) {
            $daftar_bulanan[] = [
                'bulan' => $nama_bulan[$peng->bulan],
                'total_belanja' => $peng->total_pengeluaran,
            ];
        }

        // Untuk donut chart dashboard: hitung persentase (simple contoh)
        $total_pengeluaran = $rekap->sum('total_pengeluaran');
        $max = max($total_pendapatan, 1);
        $persen_pendapatan = round(($total_pendapatan / $max) * 100); // selalu 100%
        $persen_pengeluaran = round(($total_pengeluaran / $max) * 100);
        $persen_saldo = round(($saldo_bersih / $max) * 100);

        return view('laporankeuangan.index', [
            'total_pendapatan' => $total_pendapatan,
            'budget_belanja' => $budget_belanja,
            'saldo_bersih' => $saldo_bersih,
            'laporan_bulanan' => $daftar_bulanan,
            'persen_pendapatan' => $persen_pendapatan,
            'persen_pengeluaran' => $persen_pengeluaran,
            'persen_saldo' => $persen_saldo,
            'tahun' => $tahun,
        ]);
    }
}
