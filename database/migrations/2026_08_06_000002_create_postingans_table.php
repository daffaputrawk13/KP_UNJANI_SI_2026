<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Postingan konten media sosial: menyatukan fitur "Membuat Posting",
     * "Menjadwalkan Posting", "Kalender Konten", "Upload Foto/Video",
     * "Monitoring Engagement", "Statistik Performa", dan "Arsip Posting"
     * dalam satu tabel. Statusnya menentukan di tab mana postingan tampil:
     * Draft/Terjadwal -> Kalender & Buat Posting, Terbit -> Arsip & Engagement.
     */
    public function up(): void
    {
        Schema::create('postingans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('akun_medsos_id')->constrained('akun_medsos')->cascadeOnDelete();
            $table->foreignId('satuan_id')->constrained('satuans')->cascadeOnDelete(); // scoping per satuan
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();     // pembuat konten
            $table->string('judul');                 // judul/perihal singkat internal, bukan caption
            $table->text('caption');
            $table->string('media_path')->nullable();
            $table->string('media_type')->nullable(); // foto | video
            $table->string('jenis_konten')->default('Feed'); // Feed | Reels/Video | Story | Carousel
            $table->string('status')->default('Draft'); // Draft | Terjadwal | Terbit | Gagal
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('komentar')->default(0);
            $table->unsignedInteger('share')->default(0);
            $table->unsignedInteger('dilihat')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postingans');
    }
};
