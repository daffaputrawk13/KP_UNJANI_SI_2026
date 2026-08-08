<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Laporan Penanganan Insiden — dipakai khusus fitur Satuan Pelaksanaan
     * Penindakan (Satlakdak) untuk melaporkan penanganan aksi cyber
     * (malware, ransomware, phishing, dll) ke DANPUS. Alurnya sama seperti
     * Laporan Monitoring & Recovery milik Satuan Pelaksanaan Penangkalan:
     * Draft -> Dikirim -> Direvisi/Disetujui/Ditolak. "Direvisi"
     * memungkinkan DANPUS meminta perbaikan tanpa langsung menolak laporan;
     * laporan yang direvisi bisa diedit & dikirim ulang oleh Satlakdak
     * (statusnya kembali ke "Dikirim").
     */
    public function up(): void
    {
        Schema::create('laporan_penindakans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satuan_id')->constrained('satuans')->cascadeOnDelete(); // satuan asal (Satlakdak)
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();     // pembuat laporan
            $table->foreignId('tujuan_satuan_id')->constrained('satuans')->cascadeOnDelete(); // satuan tujuan (DANPUS)
            $table->string('aset')->nullable();          // aset/sistem terdampak, mis. "Server File Sharing Ditjen"
            $table->string('jenis_ancaman')->nullable(); // Ransomware, Malware/Trojan, Phishing, DDoS, dll
            $table->string('perihal');
            $table->text('deskripsi');                   // kronologi/detail insiden
            $table->text('tindakan')->nullable();        // tindakan penindakan/penanganan yang sudah dilakukan
            $table->string('prioritas')->default('Sedang'); // Tinggi | Sedang | Rendah
            $table->string('status')->default('Draft');      // Draft | Dikirim | Direvisi | Disetujui | Ditolak
            $table->text('catatan_danpus')->nullable();       // alasan/catatan saat Direvisi atau Ditolak
            $table->timestamp('tanggal_kirim')->nullable();   // diisi tiap kali laporan dikirim/dikirim ulang
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_penindakans');
    }
};
