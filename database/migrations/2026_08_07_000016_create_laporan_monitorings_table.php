<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Laporan Monitoring & Recovery — dipakai khusus fitur Satuan Pelaksanaan Penangkalan
     * (Penangkalan) untuk melaporkan insiden/pemulihan aset digital ke
     * DANPUS. Alurnya: Draft -> Dikirim -> Direvisi/Disetujui/Ditolak.
     * "Direvisi" memungkinkan DANPUS meminta perbaikan tanpa langsung
     * menolak laporan; laporan yang direvisi bisa diedit & dikirim ulang
     * oleh Satuan Pelaksanaan Penangkalan (statusnya kembali ke "Dikirim").
     */
    public function up(): void
    {
        Schema::create('laporan_monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satuan_id')->constrained('satuans')->cascadeOnDelete(); // satuan asal (Satuan Pelaksanaan Penangkalan)
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();     // pembuat laporan
            $table->foreignId('tujuan_satuan_id')->constrained('satuans')->cascadeOnDelete(); // satuan tujuan (DANPUS)
            $table->string('aset')->nullable();       // aset/sistem terdampak, mis. "Portal Utama Pussiberad"
            $table->string('jenis_insiden')->nullable(); // DDoS, Defacement, SQL Injection, Malware, dll
            $table->string('perihal');
            $table->text('deskripsi');                // kronologi/detail insiden
            $table->text('tindakan')->nullable();      // tindakan monitoring/recovery yang sudah dilakukan
            $table->string('prioritas')->default('Sedang'); // Tinggi | Sedang | Rendah
            $table->string('status')->default('Draft');     // Draft | Dikirim | Direvisi | Disetujui | Ditolak
            $table->text('catatan_danpus')->nullable();      // alasan/catatan saat Direvisi atau Ditolak
            $table->timestamp('tanggal_kirim')->nullable();  // diisi tiap kali laporan dikirim/dikirim ulang
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_monitorings');
    }
};
