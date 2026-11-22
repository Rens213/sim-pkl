<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_pkl', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan')->unique();
            $table->foreignId('ketua_siswa_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('perusahaan_id')->constrained('perusahaan')->cascadeOnDelete();
            $table->date('tgl_pengajuan');
            $table->date('tgl_mulai_pkl');
            $table->date('tgl_selesai_pkl');
            $table->enum('status', ['dikirim', 'disetujui_hubin', 'ditolak_hubin', 'diterima_industri', 'ditolak_industri'])
                ->default('dikirim');
            $table->string('file_surat_balasan')->nullable();
            // Tambahan untuk fitur surat pengantar, plotting guru, dan alasan penolakan.
            $table->string('nomor_surat')->nullable()->unique();
            $table->date('tgl_surat')->nullable();
            $table->string('guru_pembimbing')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_pkl');
    }
};
