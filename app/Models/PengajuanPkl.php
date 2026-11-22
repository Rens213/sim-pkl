<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PengajuanPkl extends Model
{
    protected $table = 'pengajuan_pkl';

    protected $fillable = [
        'kode_pengajuan', 'ketua_siswa_id', 'perusahaan_id', 'tgl_pengajuan', 'tgl_mulai_pkl',
        'tgl_selesai_pkl', 'status', 'file_surat_balasan', 'nomor_surat', 'tgl_surat', 'guru_pembimbing', 'catatan',
    ];

    protected $casts = [
        'tgl_pengajuan' => 'date',
        'tgl_mulai_pkl' => 'date',
        'tgl_selesai_pkl' => 'date',
        'tgl_surat' => 'date',
    ];

    /** Status yang masih "memakai" siswa (siswa tidak boleh mengajukan lagi). */
    public const AKTIF = ['dikirim', 'disetujui_hubin', 'diterima_industri'];

    public const LABEL = [
        'dikirim' => 'Menunggu Hubin',
        'disetujui_hubin' => 'Disetujui Hubin',
        'ditolak_hubin' => 'Ditolak Hubin',
        'diterima_industri' => 'Diterima industri',
        'ditolak_industri' => 'Ditolak industri',
    ];

    public function ketua()
    {
        return $this->belongsTo(User::class, 'ketua_siswa_id');
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    /** Anggota kelompok (tanpa ketua). */
    public function anggota()
    {
        return $this->belongsToMany(User::class, 'anggota_kelompok', 'pengajuan_id', 'siswa_id')->withTimestamps();
    }

    /** Ketua + anggota, ketua selalu di urutan pertama. */
    public function semuaSiswa(): Collection
    {
        return collect([$this->ketua])->merge($this->anggota->all());
    }

    public function ukuran(): int
    {
        return 1 + ($this->relationLoaded('anggota') ? $this->anggota->count() : $this->anggota()->count());
    }

    public static function tgl($tanggal): string
    {
        return $tanggal ? Carbon::parse($tanggal)->locale('id')->translatedFormat('j F Y') : '-';
    }
}
