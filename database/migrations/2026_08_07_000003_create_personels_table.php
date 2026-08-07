<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Data induk personel yang dikelola Binfung lewat fitur "Administrasi
     * Personel" (Data Personel, Tambah/Edit Personel, dst).
     */
    public function up(): void
    {
        Schema::create('personels', function (Blueprint $table) {
            $table->id();
            $table->string('nrp')->unique(); // Nomor Registrasi Pokok
            $table->string('nama');
            $table->string('jenis_kelamin')->nullable(); // L | P
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->foreignId('pangkat_id')->nullable()->constrained('pangkats')->nullOnDelete();
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatans')->nullOnDelete();
            $table->foreignId('satuan_id')->nullable()->constrained('satuans')->nullOnDelete();
            $table->string('status')->default('Aktif'); // Aktif | Mutasi | Purna
            $table->date('tanggal_masuk')->nullable();
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto_path')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personels');
    }
};
