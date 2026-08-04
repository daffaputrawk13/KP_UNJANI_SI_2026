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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name'); // NIP / username login
            $table->foreignId('satuan_id')->nullable()->after('username')
                ->constrained('satuans')->nullOnDelete();
            $table->string('jabatan')->nullable()->after('satuan_id'); // opsional, tidak lagi dipakai untuk membedakan role tampilan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('satuan_id');
            $table->dropColumn(['username', 'jabatan']);
        });
    }
};
