<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pengajuan & riwayat mutasi (perpindahan satuan/jabatan) personel yang
     * ditangani Binfung. Saat status "Disetujui", satuan/jabatan aktif pada
     * tabel personels ikut diperbarui oleh PersonelMutasiController.
     */
    public function up(): void
    {
        Schema::create('personel_mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personel_id')->constrained('personels')->cascadeOnDelete();
            $table->foreignId('satuan_asal_id')->nullable()->constrained('satuans')->nullOnDelete();
            $table->foreignId('satuan_tujuan_id')->constrained('satuans')->cascadeOnDelete();
            $table->foreignId('jabatan_asal_id')->nullable()->constrained('jabatans')->nullOnDelete();
            $table->foreignId('jabatan_tujuan_id')->nullable()->constrained('jabatans')->nullOnDelete();
            $table->string('nomor_sk')->nullable();
            $table->date('tanggal_mutasi');
            $table->text('keterangan')->nullable();
            $table->string('status')->default('Menunggu SK'); // Menunggu SK | Disetujui | Ditolak
            $table->foreignId('diajukan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personel_mutasis');
    }
};
