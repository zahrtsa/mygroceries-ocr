<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemBelanja extends Model
{
    use HasFactory;

    protected $table = 'item_belanjas';

    protected $fillable = [
        'daftar_belanja_id',
        'nama_barang',
        'qty',
        'harga_satuan',
        'total_harga',
        'status',
    ];

    public function daftarBelanja()
    {
        return $this->belongsTo(DaftarBelanja::class, 'daftar_belanja_id');
    }
}
