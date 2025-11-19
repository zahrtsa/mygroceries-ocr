<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan pengecekan foreign key sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Hapus data lama dengan delete() untuk cascade
        User::query()->delete();

        // Aktifkan kembali pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Data user
        User::create([
            'name' => 'M Kezban Ramadzan',
            'username' => 'kzzban',
            'email' => 'kzzban@gmail.com',
            'password' => Hash::make('password123'), // password default
            'budget_bulanan' => 5000000, // misal 5 juta
            'pendapatan_bulanan' => 8000000, // misal 8 juta
        ]);

        User::create([
            'name' => 'Zahra Tsabitah',
            'username' => 'zahrtsa',
            'email' => 'zahrtsa@gmail.com',
            'password' => Hash::make('password123'),
            'budget_bulanan' => 4000000, // misal 4 juta
            'pendapatan_bulanan' => 6000000, // misal 6 juta
        ]);
    }
}
