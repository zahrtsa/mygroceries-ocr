<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolaBelanja extends Model
{
    use HasFactory;

    protected $table = 'pola_belanja';

    protected $fillable = [
        'user_id',
        'rata_hari_antar_belanja',
        'tanggal_prediksi_berikutnya',
        'total_pengeluaran_bulan_ini',
        'diperbarui_pada',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
