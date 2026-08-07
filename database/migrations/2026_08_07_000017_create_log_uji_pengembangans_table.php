<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_uji_pengembangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satuan_id')->constrained('satuans')->cascadeOnDelete();
            $table->foreignId('proyek_riset_id')->nullable()->constrained('proyek_risets')->nullOnDelete();
            $table->string('kegiatan');
            $table->text('hasil')->nullable();
            $table->string('status')->default('Selesai'); // Selesai | Perlu Tindak Lanjut
            $table->timestamp('waktu_uji')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_uji_pengembangans');
    }
};
