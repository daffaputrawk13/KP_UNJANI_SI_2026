<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Akun media sosial RESMI yang dikelola langsung oleh satuan (mis. akun
     * Instagram/TikTok resmi Satlak Sibersos) — berbeda dari "akun yang
     * dipantau" (target monitoring isu/hoaks) yang sudah ada sebelumnya.
     * Tabel ini jadi induk untuk fitur pembuatan & penjadwalan posting.
     */
    public function up(): void
    {
        Schema::create('akun_medsos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satuan_id')->constrained('satuans')->cascadeOnDelete();
            $table->string('nama_akun');                 // mis. "Instagram Resmi Satlak Sibersos"
            $table->string('platform');                   // Instagram | Facebook | X (Twitter) | TikTok | YouTube
            $table->string('username_platform');          // mis. @satlaksibersos
            $table->string('url_profil')->nullable();
            $table->string('foto_profil_path')->nullable();
            $table->string('status')->default('Aktif');   // Aktif | Nonaktif
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akun_medsos');
    }
};
