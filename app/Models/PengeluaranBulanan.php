<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranBulanan extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_bulanans';

    protected $fillable = [
        'user_id',
        'bulan',
        'tahun',
        'total_pengeluaran',
        'saldo_bersih',
        'dibuat_pada',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
