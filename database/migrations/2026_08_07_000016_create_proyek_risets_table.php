<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyek_risets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satuan_id')->constrained('satuans')->cascadeOnDelete();
            $table->string('nama');
            $table->string('kategori');
            $table->unsignedTinyInteger('progres')->default(0);
            $table->string('status')->default('Riset Awal'); // Riset Awal | Berjalan | Selesai
            $table->string('target_selesai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyek_risets');
    }
};
