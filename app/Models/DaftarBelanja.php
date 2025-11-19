<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarBelanja extends Model
{
    use HasFactory;

    protected $table = 'daftar_belanjas';

    // Sesuaikan fillable dengan kolom yang ada di migration
    protected $fillable = [
        'user_id',
        'tanggal_belanja',
        'total_belanja',
    ];

    // Cast kolom tanggal_belanja ke datetime agar bisa pakai ->format()
    protected $casts = [
        'tanggal_belanja' => 'datetime',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke item belanja
    public function itemBelanjas()
    {
        return $this->hasMany(ItemBelanja::class, 'daftar_belanja_id');
    }

    // Relasi ke upload struk
    public function uploadStruk()
    {
        return $this->hasOne(UploadStruk::class, 'daftar_belanja_id');
    }
}
