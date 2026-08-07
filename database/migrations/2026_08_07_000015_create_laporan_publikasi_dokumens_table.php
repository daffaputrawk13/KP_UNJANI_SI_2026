<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dokumentasi (foto/video/PDF bukti publikasi) yang diunggah untuk satu
     * Laporan Publikasi — dipisah ke tabel sendiri karena satu laporan bisa
     * punya lebih dari satu file dokumentasi, mirip pola personel_dokumens.
     */
    public function up(): void
    {
        Schema::create('laporan_publikasi_dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_publikasi_id')->constrained('laporan_publikasis')->cascadeOnDelete();
            $table->string('nama_file');
            $table->string('path');
            $table->string('tipe')->nullable(); // image | video | pdf
            $table->foreignId('diunggah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_publikasi_dokumens');
    }
};
