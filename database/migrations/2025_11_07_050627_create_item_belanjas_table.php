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
        Schema::create('item_belanjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daftar_belanja_id')->constrained('daftar_belanjas')->onDelete('cascade');
            $table->string('nama_barang');
            $table->integer('qty');
            $table->double('harga_satuan');
            $table->double('total_harga');
            $table->enum('status', ['Sudah Dibeli', 'Belum Dibeli'])->default('Belum Dibeli');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_belanjas');
    }
};
