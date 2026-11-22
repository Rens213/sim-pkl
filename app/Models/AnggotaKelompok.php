<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnggotaKelompok extends Model
{
    protected $table = 'anggota_kelompok';

    protected $fillable = ['pengajuan_id', 'siswa_id'];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanPkl::class, 'pengajuan_id');
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }
}
