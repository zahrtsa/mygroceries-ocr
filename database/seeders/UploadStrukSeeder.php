<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DaftarBelanja;
use App\Models\UploadStruk;
use Illuminate\Support\Facades\Storage;

class UploadStrukSeeder extends Seeder
{
    public function run(): void
    {
        $daftarBelanjas = DaftarBelanja::with('user')->get();

        foreach ($daftarBelanjas as $daftar) {
            $user = $daftar->user;
            $bulan = $daftar->tanggal_belanja->format('m-Y');
            
            // Folder: storage/app/struks/{username}/{bulan}/
            $folderPath = "struks/{$user->username}/{$bulan}";
            
            // Buat folder jika belum ada
            Storage::makeDirectory($folderPath);

            // Dummy file (bisa pakai file kosong txt/png untuk seeder)
            $fileName = $daftar->tanggal_belanja->format('Y-m-d') . '_struk.pdf';
            $filePath = "{$folderPath}/{$fileName}";

            // Buat file dummy
            Storage::put($filePath, 'Dummy struk content');

            UploadStruk::create([
                'daftar_belanja_id' => $daftar->id,
                'file_path' => $filePath,
                'status_ocr' => 'Belum Diproses',
                'uploaded_at' => $daftar->tanggal_belanja,
            ]);
        }
    }
}
