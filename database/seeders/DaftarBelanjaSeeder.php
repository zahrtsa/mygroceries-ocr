<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DaftarBelanja;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DaftarBelanjaSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // matikan dulu FK
        DaftarBelanja::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // hidupkan lagi FK


        $users = User::all();

        foreach ($users as $user) {
            // buat beberapa daftar belanja per user
            for ($i = 0; $i < 3; $i++) {
                DaftarBelanja::create([
                    'user_id' => $user->id,
                    'tanggal_belanja' => Carbon::today()->subDays($i),
                    'total_belanja' => 0, // nanti bisa dihitung dari item
                ]);
            }
        }
    }
}
