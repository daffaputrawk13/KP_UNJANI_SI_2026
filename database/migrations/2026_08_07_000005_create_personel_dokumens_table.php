<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dokumen administrasi personel (mis. SK, KTP, Ijazah) yang diunggah
     * lewat fitur "Upload Dokumen" pada Administrasi Personel Binfung.
     */
    public function up(): void
    {
        Schema::create('personel_dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personel_id')->constrained('personels')->cascadeOnDelete();
            $table->string('jenis_dokumen'); // mis. SK, KTP, Ijazah, KTA
            $table->string('nama_file');
            $table->string('path');
            $table->foreignId('diunggah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personel_dokumens');
    }
};
