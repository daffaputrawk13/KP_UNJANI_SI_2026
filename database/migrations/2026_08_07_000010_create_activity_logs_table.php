<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Log aktivitas sistem — dipakai fitur "Monitoring Aktivitas Sistem"
     * dan "Laporan Pengguna & Aktivitas" pada dashboard Admin.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nama_pengguna')->nullable(); // snapshot nama, tetap ada walau user dihapus
            $table->string('aksi'); // mis. "login", "logout", "user.create", "satuan.delete"
            $table->text('deskripsi')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
