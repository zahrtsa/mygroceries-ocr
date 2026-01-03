<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'daftar_belanja_id',
        'filename',
        'file_path',
        'transaction_date',
        'extracted_text',
        'ocr_total',
        'ocr_subtotal',
        'total_amount',
        'subtotal_amount',
        'status_ocr',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'ocr_total'        => 'decimal:2',
        'ocr_subtotal'     => 'decimal:2',
        'total_amount'     => 'decimal:2',
        'subtotal_amount'  => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function daftarBelanja()
    {
        return $this->belongsTo(DaftarBelanja::class);
    }
}
