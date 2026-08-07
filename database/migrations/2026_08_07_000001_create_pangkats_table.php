<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master data pangkat (mis. Prada, Praka, Kopda, Serda, Sertu, Serka,
     * Letda, Lettu, Kapten, dst) yang dikelola Binfung untuk fitur
     * Administrasi Personel.
     */
    public function up(): void
    {
        Schema::create('pangkats', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();   // mis. SERDA, SERTU
            $table->string('nama');              // nama lengkap pangkat
            $table->string('kategori');          // Tamtama | Bintara | Perwira
            $table->unsignedInteger('urutan')->default(0); // urutan tampil/kenaikan pangkat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pangkats');
    }
};
