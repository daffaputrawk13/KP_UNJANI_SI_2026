<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Baris tunggal (singleton) pengaturan sistem — nama instansi, logo,
     * dan alamat. Dipakai fitur "Pengaturan Sistem" pada dashboard Admin.
     */
    public function up(): void
    {
        Schema::create('pengaturans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_instansi')->default('Pussiberad');
            $table->string('singkatan')->nullable();
            $table->string('logo_path')->nullable();
            $table->text('alamat')->nullable();
            $table->string('email_kontak')->nullable();
            $table->string('telepon_kontak')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturans');
    }
};
