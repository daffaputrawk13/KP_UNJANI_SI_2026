<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lampiran (foto/PDF/dokumen) untuk satu Laporan Monitoring & Recovery.
     * Satu laporan bisa punya banyak lampiran — dipakai fitur "Upload
     * Lampiran" di dashboard Satuan Pelaksanaan Penangkalan.
     */
    public function up(): void
    {
        Schema::create('laporan_monitoring_lampirans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_monitoring_id')->constrained('laporan_monitorings')->cascadeOnDelete();
            $table->string('nama_file');
            $table->string('path');
            $table->string('tipe'); // image | pdf | dokumen
            $table->foreignId('diunggah_oleh')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_monitoring_lampirans');
    }
};
