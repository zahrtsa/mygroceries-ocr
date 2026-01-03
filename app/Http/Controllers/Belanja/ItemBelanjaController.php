<?php

namespace App\Http\Controllers\Belanja;

use App\Http\Controllers\Controller;
use App\Models\ItemBelanja;
use App\Models\DaftarBelanja;
use App\Models\PengeluaranBulanan;
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

        $validated = $request->validate([
            'nama_barang'   => 'required|string|max:255',
            'qty'           => 'required|integer|min:1',
            'harga_satuan'  => 'required|numeric|min:0',
        ]);

        $total_harga = $validated['qty'] * $validated['harga_satuan'];

        $item = ItemBelanja::create([
            'daftar_belanja_id' => $daftarBelanja->id,
            'nama_barang'       => $validated['nama_barang'],
            'qty'               => $validated['qty'],
            'harga_satuan'      => $validated['harga_satuan'],
            'total_harga'       => $total_harga,
            'status'            => 'Belum Dibeli',
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

    public function update(Request $request, ItemBelanja $item)
    {
        if ($item->daftarBelanja->user_id !== Auth::id()) abort(403);

        $oldTotal  = $item->total_harga;
        $oldStatus = $item->status;

        $validated = $request->validate([
            'nama_barang'   => 'required|string|max:255',
            'qty'           => 'required|integer|min:1',
            'harga_satuan'  => 'required|numeric|min:0',
            'status'        => 'required|in:Sudah Dibeli,Belum Dibeli',
        ]);

        $item->update([
            'nama_barang'   => $validated['nama_barang'],
            'qty'           => $validated['qty'],
            'harga_satuan'  => $validated['harga_satuan'],
            'total_harga'   => $validated['qty'] * $validated['harga_satuan'],
            'status'        => $validated['status'],
        ]);

        $item->daftarBelanja->update([
            'total_belanja' => $item->daftarBelanja->itemBelanjas()->sum('total_harga'),
        ]);

        $newTotal  = $item->total_harga;
        $newStatus = $item->status;

        $selisih = 0;
        if ($oldStatus === 'Sudah Dibeli' && $newStatus === 'Sudah Dibeli') {
            $selisih = $newTotal - $oldTotal;
        } elseif ($oldStatus === 'Belum Dibeli' && $newStatus === 'Sudah Dibeli') {
            $selisih = +$newTotal;
        } elseif ($oldStatus === 'Sudah Dibeli' && $newStatus === 'Belum Dibeli') {
            $selisih = -$oldTotal;
        }

        if ($selisih != 0) {
            $this->updatePengeluaranBulananFromItem($item, $selisih);
        }

        return back()->with('success', 'Item berhasil diubah!');
    }

    public function destroy(ItemBelanja $item)
    {
        if ($item->daftarBelanja->user_id !== Auth::id()) abort(403);

        if ($item->status === 'Sudah Dibeli' && $item->total_harga > 0) {
            $this->updatePengeluaranBulananFromItem($item, -$item->total_harga);
        }

        $daftar = $item->daftarBelanja;
        $item->delete();

        $daftar->update([
            'total_belanja' => $daftar->itemBelanjas()->sum('total_harga')
        ]);

        return back()->with('success', 'Item berhasil dihapus!');
    }

    private function updatePengeluaranBulananFromItem(ItemBelanja $item, float $pertambahan)
    {
        $user  = $item->daftarBelanja->user;
        $date  = $item->daftarBelanja->tanggal_belanja ?? now();
        $bulan = $date->format('n');
        $tahun = $date->format('Y');

        $pengeluaran = PengeluaranBulanan::firstOrCreate(
            [
                'user_id' => $user->id,
                'bulan'   => $bulan,
                'tahun'   => $tahun,
            ],
            [
                'total_pengeluaran' => 0,
                'saldo_bersih'      => 0,
            ]
        );

        $pengeluaran->total_pengeluaran += $pertambahan;
        $pengeluaran->saldo_bersih = ($user->budget_bulanan ?? 0) - $pengeluaran->total_pengeluaran;
        $pengeluaran->save();
    }
}
