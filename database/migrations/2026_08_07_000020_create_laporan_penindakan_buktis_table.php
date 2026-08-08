<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bukti Digital (foto/PDF/log/ZIP forensik) untuk satu Laporan
     * Penanganan Insiden. Satu laporan bisa punya banyak bukti — dipakai
     * fitur "Upload Bukti Digital" di dashboard Satuan Pelaksanaan
     * Penindakan (Satlakdak).
     */
    public function up(): void
    {
        Schema::create('laporan_penindakan_buktis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_penindakan_id')->constrained('laporan_penindakans')->cascadeOnDelete();
            $table->string('nama_file');
            $table->string('path');
            $table->string('tipe'); // image | pdf | dokumen
            $table->foreignId('diunggah_oleh')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_penindakan_buktis');
    }
};
