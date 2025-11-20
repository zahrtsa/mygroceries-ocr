<?php

namespace App\Http\Controllers\Belanja;

use App\Http\Controllers\Controller;
use App\Models\DaftarBelanja;
use App\Models\ItemBelanja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BelanjaController extends Controller
{
    // List semua item hari ini (GET /belanja/item)
    public function index(Request $request)
    {
        $tanggal = now()->toDateString();
        $search = $request->input('search');
        $userId = Auth::id();

        // Dapatkan atau auto-buat daftar belanja hari ini
        $daftar = DaftarBelanja::firstOrCreate([
            'user_id' => $userId,
            'tanggal_belanja' => $tanggal,
        ], ['total_belanja' => 0]);

        $query = ItemBelanja::where('daftar_belanja_id', $daftar->id);

        // Search semua kolom
        if ($search) {
            $searchLower = strtolower($search);
            $query->where(function ($q) use ($searchLower) {
                $q->whereRaw('LOWER(nama_barang) LIKE ?', ["%{$searchLower}%"])
                    ->orWhereRaw('CAST(qty AS CHAR) LIKE ?', ["%{$searchLower}%"])
                    ->orWhereRaw('CAST(harga_satuan AS CHAR) LIKE ?', ["%{$searchLower}%"])
                    ->orWhereRaw('CAST(total_harga AS CHAR) LIKE ?', ["%{$searchLower}%"])
                    ->orWhereRaw('LOWER(status) LIKE ?', ["%{$searchLower}%"]);
            });
        }

        $itemBelanjas = $query->get();
        $tanggalBelanja = now();

        return view('belanja.index', compact('itemBelanjas', 'tanggalBelanja'));
    }

    // Form tambah item (GET /belanja/item/create)
    public function create()
    {
        return view('belanja.create');
    }

    // Simpan item baru (POST /belanja/item)
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        $userId = Auth::id();
        $tanggal = now()->toDateString();
        $daftar = DaftarBelanja::firstOrCreate([
            'user_id' => $userId,
            'tanggal_belanja' => $tanggal,
        ], ['total_belanja' => 0]);
        $total_harga = $request->qty * $request->harga_satuan;

        $item = ItemBelanja::create([
            'daftar_belanja_id' => $daftar->id,
            'nama_barang' => $request->nama_barang,
            'qty' => $request->qty,
            'harga_satuan' => $request->harga_satuan,
            'total_harga' => $total_harga,
            'status' => 'Belum Dibeli',
        ]);
        $daftar->update(['total_belanja' => $daftar->itemBelanjas()->sum('total_harga')]);

        // Kirim session untuk trigger SweetAlert di layout
        return redirect()->route('belanja.item.index')->with('success', 'Item berhasil ditambahkan!');
    }

    // Form edit barang (GET /belanja/item/{item}/edit)
    public function edit(ItemBelanja $item)
    {
        if ($item->daftarBelanja->user_id !== Auth::id()) {
            abort(403);
        }

        return view('belanja.edit', compact('item'));
    }

    // Simpan perubahan (PATCH /belanja/item/{item})
    public function update(Request $request, ItemBelanja $item)
    {
        if ($item->daftarBelanja->user_id !== Auth::id()) {
            abort(403);
        }
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'status' => 'required|in:Sudah Dibeli,Belum Dibeli',
        ]);
        $item->update([
            'nama_barang' => $request->nama_barang,
            'qty' => $request->qty,
            'harga_satuan' => $request->harga_satuan,
            'total_harga' => $request->qty * $request->harga_satuan,
            'status' => $request->status,
        ]);
        $item->daftarBelanja->update([
            'total_belanja' => $item->daftarBelanja->itemBelanjas()->sum('total_harga'),
        ]);

        // Kirim session untuk trigger SweetAlert di layout
        return redirect()->route('belanja.item.index')->with('success', 'Item berhasil diedit!');
    }

    // Hapus item (DELETE /belanja/item/{item})
    public function destroy(ItemBelanja $item)
    {
        if ($item->daftarBelanja->user_id !== Auth::id()) {
            abort(403);
        }
        $daftar = $item->daftarBelanja;
        $item->delete();
        $daftar->update(['total_belanja' => $daftar->itemBelanjas()->sum('total_harga')]);

        // Kirim session untuk trigger SweetAlert di layout
        return redirect()->route('belanja.item.index')->with('success', 'Item berhasil dihapus!');
    }

    public function rekapanHarian(Request $request)
    {
        $userId = Auth::id();
        $tanggal = $request->input('tanggal', now()->toDateString());

        // Ambil daftar belanja (parent) pada tanggal dipilih, milik user
        $daftar = DaftarBelanja::where('user_id', $userId)
            ->where('tanggal_belanja', $tanggal)
            ->first();

        // Ambil semua item belanja (child) yang terkait daftar & tanggal
        $items = $daftar
            ? ItemBelanja::where('daftar_belanja_id', $daftar->id)->get()
            : collect(); // kosongkan jika tidak ada daftar

        $totalBelanja = $items->sum('total_harga');

        // Daftar semua tanggal belanja user untuk pilihan dropdown
        $optionsTanggal = DaftarBelanja::where('user_id', $userId)
            ->orderBy('tanggal_belanja', 'desc')
            ->pluck('tanggal_belanja')
            ->unique();

        return view('belanja.rekapanharian', compact('items', 'tanggal', 'totalBelanja', 'optionsTanggal'));
    }
}
