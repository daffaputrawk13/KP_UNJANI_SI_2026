<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Laporan yang dikirim satu satuan (mis. Satlok Duktek/Bangtek) ke satuan
     * tujuan (mis. DANPUS). Statusnya sengaja sederhana (Menunggu / Disetujui
     * DANPUS / Ditolak DANPUS) karena pada fitur ini laporan dikirim langsung
     * ke DANPUS, bukan lewat alur verifikasi WADAN.
     */
    public function up(): void
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satuan_id')->constrained('satuans')->cascadeOnDelete(); // satuan asal pengirim
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();     // pengguna yang mengirim
            $table->foreignId('tujuan_satuan_id')->constrained('satuans')->cascadeOnDelete(); // satuan tujuan (DANPUS)
            $table->string('proyek')->nullable();   // proyek/kegiatan terkait, opsional
            $table->string('perihal');
            $table->text('deskripsi');
            $table->string('prioritas');            // Tinggi | Sedang | Rendah
            $table->string('lampiran_path')->nullable();
            $table->string('status')->default('Menunggu'); // Menunggu | Disetujui DANPUS | Ditolak DANPUS
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
