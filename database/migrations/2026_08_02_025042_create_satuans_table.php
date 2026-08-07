<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('satuans', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();      // mis. SATLAKKAL, BINFUNG, DANPUS
            $table->string('nama');                // nama lengkap satuan
            $table->string('kategori');            // satlak | direktorat | pimpinan
            $table->string('deskripsi')->nullable();
            $table->unsignedInteger('urutan')->default(0); // urutan tampil di dropdown
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('satuans');
    }
};
