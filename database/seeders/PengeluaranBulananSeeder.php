<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengeluaranBulanan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PengeluaranBulananSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan pengecekan foreign key sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        PengeluaranBulanan::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $users = User::all();

        foreach ($users as $user) {
            for ($month = 1; $month <= 12; $month++) {
                $totalPengeluaran = rand((int)($user->budget_bulanan * 0.5), (int)$user->budget_bulanan);
                $saldoBersih = $user->pendapatan_bulanan - $totalPengeluaran;

                PengeluaranBulanan::create([
                    'user_id' => $user->id,
                    'bulan' => $month,
                    'tahun' => date('Y'),
                    'total_pengeluaran' => $totalPengeluaran,
                    'saldo_bersih' => $saldoBersih,
                ]);
            }
        }
    }
}
