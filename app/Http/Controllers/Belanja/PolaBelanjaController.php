<?php

namespace App\Http\Controllers\Belanja;

use App\Http\Controllers\Controller;
use App\Models\PolaBelanja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PolaBelanjaController extends Controller
{
    public function index()
    {
        $pola = PolaBelanja::where('user_id', Auth::id())->first();
        return view('belanja.pola.index', compact('pola'));
    }

    public function update(Request $request, PolaBelanja $polaBelanja)
    {
        $request->validate([
            'rata_hari_antar_belanja' => 'nullable|integer|min:1',
            'tanggal_prediksi_berikutnya' => 'nullable|date',
            'total_pengeluaran_bulan_ini' => 'nullable|numeric|min:0',
        ]);

        $polaBelanja->update([
            'rata_hari_antar_belanja' => $request->rata_hari_antar_belanja,
            'tanggal_prediksi_berikutnya' => $request->tanggal_prediksi_berikutnya,
            'total_pengeluaran_bulan_ini' => $request->total_pengeluaran_bulan_ini,
            'diperbarui_pada' => now(),
        ]);

        return back()->with('success', 'Data pola belanja berhasil diperbarui!');
    }
}
