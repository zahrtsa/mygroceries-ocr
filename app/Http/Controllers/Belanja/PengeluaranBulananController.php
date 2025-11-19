<?php

namespace App\Http\Controllers\Belanja;

use App\Http\Controllers\Controller;
use App\Models\PengeluaranBulanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengeluaranBulananController extends Controller
{
    public function index()
    {
        $pengeluaran = PengeluaranBulanan::where('user_id', Auth::id())
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        return view('belanja.pengeluaran.index', compact('pengeluaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2000',
            'total_pengeluaran' => 'required|numeric|min:0',
            'saldo_bersih' => 'required|numeric',
        ]);

        PengeluaranBulanan::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
            ],
            [
                'total_pengeluaran' => $request->total_pengeluaran,
                'saldo_bersih' => $request->saldo_bersih,
            ]
        );

        return back()->with('success', 'Data pengeluaran bulan berhasil disimpan!');
    }
}
