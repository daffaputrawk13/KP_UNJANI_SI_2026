<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dukungan_teknis_logs', function (Blueprint $table) {
            $table->id();
            // satuan_id = Satuan Pelaksanaan Dukungan Teknologi yang memberi dukungan (selalu sama,
            // tapi tetap disimpan supaya query & relasi konsisten dengan
            // tabel log lain di aplikasi ini).
            $table->foreignId('satuan_id')->constrained('satuans')->cascadeOnDelete();
            $table->foreignId('satuan_tujuan_id')->constrained('satuans')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('jenis_bantuan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dukungan_teknis_logs');
    }
};
