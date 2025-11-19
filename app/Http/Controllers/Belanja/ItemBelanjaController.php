<?php
namespace App\Http\Controllers\Belanja;

use App\Http\Controllers\Controller;
use App\Models\ItemBelanja;
use App\Models\DaftarBelanja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemBelanjaController extends Controller
{
    public function create(DaftarBelanja $daftarBelanja)
    {
        if ($daftarBelanja->user_id !== Auth::id()) abort(403);
        return view('item_belanja.create', compact('daftarBelanja'));
    }

    public function store(Request $request, DaftarBelanja $daftarBelanja)
    {
        if ($daftarBelanja->user_id !== Auth::id()) abort(403);

        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
        ]);
        $total_harga = $request->qty * $request->harga_satuan;

        ItemBelanja::create([
            'daftar_belanja_id' => $daftarBelanja->id,
            'nama_barang' => $request->nama_barang,
            'qty' => $request->qty,
            'harga_satuan' => $request->harga_satuan,
            'total_harga' => $total_harga,
            'status' => 'Belum Dibeli',
        ]);
        $daftarBelanja->update([
            'total_belanja' => $daftarBelanja->itemBelanjas()->sum('total_harga')
        ]);
        return redirect()->route('belanja.daftar.show', $daftarBelanja->id)
            ->with('success', 'Item berhasil ditambahkan!');
    }

    public function edit(ItemBelanja $item)
    {
        if ($item->daftarBelanja->user_id !== Auth::id()) abort(403);
        return view('item_belanja.edit', ['itemBelanja' => $item]);
    }

    // Update status (and other fields if needed)
    public function update(Request $request, ItemBelanja $item)
    {
        if ($item->daftarBelanja->user_id !== Auth::id()) abort(403);

        $request->validate([
            'status' => 'required|in:Sudah Dibeli,Belum Dibeli',
        ]);
        $item->update([
            'status' => $request->status,
        ]);
        return back()->with('success', 'Status berhasil diubah!');
    }

    public function destroy(ItemBelanja $item)
    {
        if ($item->daftarBelanja->user_id !== Auth::id()) abort(403);
        $daftar = $item->daftarBelanja;
        $item->delete();
        $daftar->update([
            'total_belanja' => $daftar->itemBelanjas()->sum('total_harga')
        ]);
        return back()->with('success', 'Item berhasil dihapus!');
    }
}