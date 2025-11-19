<?php

namespace App\Http\Controllers\Belanja;

use App\Http\Controllers\Controller;
use App\Models\DaftarBelanja;
use App\Models\ItemBelanja;
use Illuminate\Http\Request;

class DaftarBelanjaController extends Controller
{
    // Tampilkan semua daftar belanja user
    public function index()
    {
        $daftarBelanja = DaftarBelanja::with('items')->get();
        return view('daftar_belanja.index', compact('daftarBelanja'));
    }

    // Form buat daftar belanja baru
    public function create()
    {
        return view('daftar_belanja.create');
    }

    // Simpan daftar belanja baru
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_belanja' => 'required|date',
        ]);

        $daftar = DaftarBelanja::create([
            'user_id' => $request->user_id,
            'tanggal_belanja' => $request->tanggal_belanja,
            'total_belanja' => 0,
        ]);

        return redirect()->route('daftar_belanja.index')
            ->with('success', 'Daftar belanja berhasil dibuat!');
    }

    // Detail daftar belanja beserta itemnya
    public function show(DaftarBelanja $daftarBelanja)
    {
        $daftarBelanja->load('items');
        return view('daftar_belanja.show', compact('daftarBelanja'));
    }

    // Hapus daftar belanja beserta itemnya
    public function destroy(DaftarBelanja $daftarBelanja)
    {
        $daftarBelanja->delete();
        return redirect()->route('daftar_belanja.index')
            ->with('success', 'Daftar belanja berhasil dihapus!');
    }
}
