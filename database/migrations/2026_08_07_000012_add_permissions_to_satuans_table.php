<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Setiap satuan (kode) berperan sebagai "role" login di SIBERAD.
     * Kolom `permissions` menyimpan daftar modul yang boleh diakses satuan
     * tsb — dipakai fitur "Manajemen Role & Hak Akses" pada dashboard Admin.
     */
    public function up(): void
    {
        Schema::table('satuans', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('satuans', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};
