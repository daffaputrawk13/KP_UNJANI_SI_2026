<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Laporan Publikasi — dipakai khusus fitur Satlak Sibersos untuk
     * melaporkan kegiatan publikasi/konten media sosial ke DANPUS.
     * Tabel terpisah dari 'laporans' (Duktek/Bangtek) supaya alur Sibersos
     * (yang punya status Draft) tidak bercampur dengan alur laporan lain.
     */
    public function up(): void
    {
        Schema::create('laporan_publikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satuan_id')->constrained('satuans')->cascadeOnDelete(); // satuan asal (Satlak Sibersos)
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();     // pembuat laporan
            $table->foreignId('tujuan_satuan_id')->constrained('satuans')->cascadeOnDelete(); // satuan tujuan (DANPUS)
            $table->string('judul');                 // judul/perihal publikasi
            $table->string('platform')->nullable();  // Instagram | Facebook | X (Twitter) | TikTok | dll
            $table->string('link_publikasi')->nullable();
            $table->text('deskripsi');
            $table->string('status')->default('Draft'); // Draft | Menunggu | Disetujui DANPUS | Ditolak DANPUS
            $table->timestamp('tanggal_kirim')->nullable(); // diisi saat draft dikirim ke DANPUS
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_publikasis');
    }
};
