<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nis_nip', 'nama_lengkap', 'kelas', 'email', 'password', 'role', 'no_hp', 'perusahaan_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        // 'hashed' = password otomatis di-bcrypt saat disimpan.
        return ['password' => 'hashed'];
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }
}
