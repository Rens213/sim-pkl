<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    protected $table = 'perusahaan';

    protected $fillable = ['nama_perusahaan', 'alamat_lengkap', 'kontak_hrd', 'kuota_tersedia', 'status_mitra'];

    public function pengajuan()
    {
        return $this->hasMany(PengajuanPkl::class, 'perusahaan_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status_mitra', 'aktif');
    }
}
