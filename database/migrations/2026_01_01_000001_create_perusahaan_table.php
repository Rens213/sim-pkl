<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perusahaan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->text('alamat_lengkap');
            $table->string('kontak_hrd');
            $table->unsignedInteger('kuota_tersedia')->default(0);
            $table->enum('status_mitra', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        // Tambahan: menghubungkan akun role "perusahaan" ke data perusahaannya.
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('perusahaan_id')->nullable()->after('no_hp')
                ->constrained('perusahaan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('perusahaan_id');
        });
        Schema::dropIfExists('perusahaan');
    }
};
