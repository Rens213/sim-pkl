<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nis_nip', 30)->unique();
            $table->string('nama_lengkap');
            $table->string('kelas', 30)->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['siswa', 'admin_hubin', 'perusahaan'])->default('siswa');
            $table->string('no_hp', 20)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Dipakai driver session=database bawaan Laravel.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
