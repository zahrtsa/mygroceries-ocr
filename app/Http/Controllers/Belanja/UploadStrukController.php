<?php

namespace App\Http\Controllers;

use App\Models\UploadStruk;
use App\Models\DaftarBelanja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadStrukController extends Controller
{
    // Form upload struk
    public function create($daftarBelanjaId)
    {
        return view('upload_struk.create', compact('daftarBelanjaId'));
    }

    // Simpan file struk
    public function store(Request $request)
    {
        $request->validate([
            'daftar_belanja_id' => 'required|exists:daftar_belanjas,id',
            'file_path' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // max 5MB
        ]);

        // Ambil data daftar belanja beserta user
        $daftarBelanja = DaftarBelanja::with('user')->findOrFail($request->daftar_belanja_id);
        $username = $daftarBelanja->user->username;
        $tanggalBelanja = $daftarBelanja->tanggal_belanja->format('Y-m-d');

        // Folder dinamis: struks/{username}/{YYYY-MM}
        $folderPath = "struks/{$username}/" . $daftarBelanja->tanggal_belanja->format('Y-m');

        // Nama file: {YYYY-MM-DD}_{timestamp}.ext
        $fileName = $tanggalBelanja . '_' . time() . '.' . $request->file('file_path')->extension();

        // Simpan file ke storage/public
        $filePath = $request->file('file_path')->storeAs($folderPath, $fileName, 'public');

        // Simpan record ke database
        $upload = UploadStruk::create([
            'daftar_belanja_id' => $request->daftar_belanja_id,
            'file_path' => $filePath,
            'status_ocr' => 'Belum Diproses',
        ]);

        return redirect()->route('daftar_belanja.show', $request->daftar_belanja_id)
            ->with('success', 'Struk berhasil diupload!');
    }

    // Hapus struk
    public function destroy(UploadStruk $uploadStruk)
    {
        // Hapus file fisik
        Storage::disk('public')->delete($uploadStruk->file_path);

        // Hapus record database
        $uploadStruk->delete();

        return back()->with('success', 'Struk berhasil dihapus!');
    }
}
