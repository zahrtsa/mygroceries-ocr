<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemBelanja;
use App\Models\DaftarBelanja;
use Illuminate\Support\Facades\DB;

class ItemBelanjaSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan foreign key sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        ItemBelanja::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $items = [
            ['nama' => 'Apel', 'harga_satuan' => 5000],
            ['nama' => 'Pisang', 'harga_satuan' => 3000],
            ['nama' => 'Bayam', 'harga_satuan' => 2000],
            ['nama' => 'Telur', 'harga_satuan' => 2500],
            ['nama' => 'Beras 5kg', 'harga_satuan' => 60000],
            ['nama' => 'Minyak Goreng 1L', 'harga_satuan' => 14000],
        ];

        $daftarBelanjas = DaftarBelanja::all();

        foreach ($daftarBelanjas as $daftar) {
            $selected = collect($items)->random(rand(2, 4));

            foreach ($selected as $item) {
                $qty = rand(1, 5);
                ItemBelanja::create([
                    'daftar_belanja_id' => $daftar->id,
                    'nama_barang' => $item['nama'],
                    'qty' => $qty,
                    'harga_satuan' => $item['harga_satuan'],
                    'total_harga' => $item['harga_satuan'] * $qty,
                    'status' => 'Belum Dibeli',
                ]);
            }

            // update total_belanja di daftar belanja
            $daftar->update([
                'total_belanja' => $daftar->itemBelanjas()->sum('total_harga'),
            ]);
        }
    }
}
