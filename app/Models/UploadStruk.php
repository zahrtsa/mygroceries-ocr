<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadStruk extends Model
{
    use HasFactory;

    protected $table = 'upload_struks';

    protected $fillable = [
        'daftar_belanja_id',
        'file_path',
        'hasil_ocr',
        'status_ocr',
        'uploaded_at',
    ];

    protected static function booted()
    {
        static::creating(function ($uploadStruk) {
            $uploadStruk->uploaded_at = now();
            $uploadStruk->status_ocr = 'Diproses'; // otomatis langsung jadi Diproses
        });

        // Event otomatis saat update model
        static::updating(function ($uploadStruk) {
            if (!empty($uploadStruk->hasil_ocr)) {
                $uploadStruk->status_ocr = 'Selesai'; // kalau hasil OCR sudah ada, set Selesai
            }
        });
    }


    public function daftarBelanja()
    {
        return $this->belongsTo(DaftarBelanja::class, 'daftar_belanja_id');
    }
}
