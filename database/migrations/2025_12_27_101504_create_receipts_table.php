<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();

            // Relasi ke user & daftar belanja
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('daftar_belanja_id')
                  ->nullable()
                  ->constrained('daftar_belanjas')
                  ->onDelete('cascade');

            // File & info struk
            $table->string('filename');      // nama file
            $table->string('file_path');     // path di storage
            $table->date('transaction_date')->nullable();

            // Hasil OCR mentah
            $table->text('extracted_text')->nullable();

            // Nilai asli hasil OCR
            $table->decimal('ocr_total', 15, 2)->nullable();
            $table->decimal('ocr_subtotal', 15, 2)->nullable();

            // Nilai yang dipakai sistem (bisa diedit)
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->decimal('subtotal_amount', 15, 2)->nullable();

            // Status proses OCR
            $table->enum('status_ocr', ['Belum Diproses', 'Diproses', 'Selesai'])
                  ->default('Belum Diproses');

            $table->timestamps();
        });

        // OPSIONAL: kalau mau hapus tabel upload_struks lama
        // Schema::dropIfExists('upload_struks');
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');

        // OPSIONAL rollback upload_struks kalau tadi di-drop
        /*
        Schema::create('upload_struks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daftar_belanja_id')->constrained('daftar_belanjas')->onDelete('cascade')->unique();
            $table->string('file_path');
            $table->text('hasil_ocr')->nullable();
            $table->enum('status_ocr', ['Belum Diproses', 'Diproses', 'Selesai'])->default('Belum Diproses');
            $table->datetime('uploaded_at')->nullable();
            $table->timestamps();
        });
        */
    }
};
