<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('upload_struks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daftar_belanja_id')->constrained('daftar_belanjas')->onDelete('cascade')->unique();
            $table->string('file_path');
            $table->text('hasil_ocr')->nullable();
            $table->enum('status_ocr', ['Belum Diproses', 'Diproses', 'Selesai'])->default('Belum Diproses');
            $table->datetime('uploaded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_struks');
    }
};
