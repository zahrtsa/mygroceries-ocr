<?php

namespace App\Http\Controllers\Belanja;

use App\Http\Controllers\Controller;
use App\Models\ItemBelanja;
use App\Models\DaftarBelanja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemBelanjaController extends Controller
{
    public function store(Request $request, $daftarBelanjaId)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        $daftar = DaftarBelanja::findOrFail($daftarBelanjaId);
        if ($daftar->user_id !== Auth::id()) abort(403);

        $total_harga = $request->qty * $request->harga_satuan;

        ItemBelanja::create([
            'daftar_belanja_id' => $daftarBelanjaId,
            'nama_barang' => $request->nama_barang,
            'qty' => $request->qty,
            'harga_satuan' => $request->harga_satuan,
            'total_harga' => $total_harga,
        ]);

        $daftar->update([
            'total_belanja' => $daftar->itemBelanja()->sum('total_harga')
        ]);

        return back()->with('success', 'Item berhasil ditambahkan!');
    }

    public function update(Request $request, ItemBelanja $itemBelanja)
    {
        if ($itemBelanja->daftarBelanja->user_id !== Auth::id()) abort(403);

        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'status' => 'required|in:Sudah Dibeli,Belum Dibeli',
        ]);

        $itemBelanja->update([
            'nama_barang' => $request->nama_barang,
            'qty' => $request->qty,
            'harga_satuan' => $request->harga_satuan,
            'total_harga' => $request->qty * $request->harga_satuan,
            'status' => $request->status,
        ]);

        $itemBelanja->daftarBelanja->update([
            'total_belanja' => $itemBelanja->daftarBelanja->itemBelanja()->sum('total_harga')
        ]);

        return back()->with('success', 'Item berhasil diperbarui!');
    }

    public function destroy(ItemBelanja $itemBelanja)
    {
        if ($itemBelanja->daftarBelanja->user_id !== Auth::id()) abort(403);

        $daftar = $itemBelanja->daftarBelanja;
        $itemBelanja->delete();

        $daftar->update([
            'total_belanja' => $daftar->itemBelanja()->sum('total_harga')
        ]);

        return back()->with('success', 'Item berhasil dihapus!');
    }
}
