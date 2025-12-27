<?php

namespace App\Http\Controllers\Belanja;

use App\Http\Controllers\Controller;
use App\Models\PengeluaranBulanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengeluaranBulananController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        // Tahun dipilih (default: tahun sekarang)
        $tahunDipilih = (int) $request->input('tahun', now()->year);

        // Ambil semua tahun yang ada data sejak 2023
        $daftarTahun = PengeluaranBulanan::where('user_id', $userId)
            ->where('tahun', '>=', 2023)
            ->select('tahun')
            ->distinct()
            ->orderBy('tahun', 'asc')
            ->pluck('tahun')
            ->toArray();

        // Kalau belum ada data sama sekali, isi 2023..tahun sekarang
        if (empty($daftarTahun)) {
            $currentYear = now()->year;
            $daftarTahun = range(2023, $currentYear);
        }

        // Kalau tahun dipilih belum ada di list, tambahkan biar bisa dipilih
        if (!in_array($tahunDipilih, $daftarTahun, true)) {
            $daftarTahun[] = $tahunDipilih;
            sort($daftarTahun);
        }

        // Ambil data tahun terpilih
        $rekap = PengeluaranBulanan::where('user_id', $userId)
            ->where('tahun', $tahunDipilih)
            ->orderBy('bulan')
            ->get();

        $adaDataTahunIni = $rekap->isNotEmpty();

        // Mapping ke array untuk tabel
        $nama_bulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $daftar_bulanan = $rekap->map(function ($peng) use ($nama_bulan) {
            return [
                'bulan' => $nama_bulan[$peng->bulan] ?? $peng->bulan,
                'total_belanja' => $peng->total_pengeluaran,
            ];
        });

        // Ringkasan untuk card atas (pakai logika lama kamu)
        $total_pendapatan = ($user->pendapatan_bulanan ?? 0) * 12;
        $budget_belanja = ($user->budget_bulanan ?? 0);
        $total_pengeluaran = $rekap->sum('total_pengeluaran');
        $saldo_bersih = $rekap->sum('saldo_bersih');

        $max = max($total_pendapatan, 1);
        $persen_pendapatan = round(($total_pendapatan / $max) * 100);
        $persen_pengeluaran = round(($total_pengeluaran / $max) * 100);
        $persen_saldo = round(($saldo_bersih / $max) * 100);

        return view('laporankeuangan.index', [
            'tahun' => $tahunDipilih,
            'daftar_tahun' => $daftarTahun,
            'laporan_bulanan' => $daftar_bulanan,
            'adaDataTahunIni' => $adaDataTahunIni,
            'total_pendapatan' => $total_pendapatan,
            'budget_belanja' => $budget_belanja,
            'total_pengeluaran' => $total_pengeluaran,
            'saldo_bersih' => $saldo_bersih,
            'persen_pendapatan' => $persen_pendapatan,
            'persen_pengeluaran' => $persen_pengeluaran,
            'persen_saldo' => $persen_saldo,
        ]);
    }
}
