<?php

namespace Database\Seeders;

use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $pw = 'password123'; // di-hash otomatis oleh cast 'hashed' pada model User

        User::create([
            'nis_nip' => 'HUBIN001', 'nama_lengkap' => 'Admin Hubin', 'email' => 'hubin@smkicb.example',
            'password' => $pw, 'role' => 'admin_hubin', 'no_hp' => '081200000001',
        ]);

        $siswa = [
            ['2526001', 'Ralin Rahmawati', 'XII RPL'],
            ['2526002', 'Bagas Pratama', 'XII AKL 1'],
            ['2526003', 'Citra Lestari', 'XII BR 1'],
            ['2526004', 'Dimas Saputra', 'XII BR 1'],
            ['2526005', 'Eka Nuraini', 'XII MPLB 1'],
            ['2526006', 'Fajar Ramadhan', 'XII MPLB 1'],
            ['2526007', 'Gita Permata', 'XII RPL'],
            ['2526008', 'Roy Kiyo', 'XII MPLB 2'],
        ];
        foreach ($siswa as $i => [$nis, $nama, $kelas]) {
            User::create([
                'nis_nip' => $nis, 'nama_lengkap' => $nama, 'kelas' => $kelas,
                'email' => "siswa{$nis}@smkicb.example", 'password' => $pw,
                'role' => 'siswa', 'no_hp' => '0812000010'.($i + 1),
            ]);
        }

        $mitra = [
            ['STEI ITB', 'Jl. Ganesa 10 Coblong, Kota Bandung, Jawa Barat, Indonesia 40132', 'itb@ganesha.example', 2, 'aktif'],
            ['CV Cahaya Digital Print', 'Jl. Cihampelas, Coblong, Bandung', 'hrd@cahayaprint.example', 3, 'aktif'],
            ['Koperasi Karya Sejahtera', 'Jl. Buah Batu, Lengkong, Bandung', 'hrd@karyasejahtera.example', 0, 'aktif'],
            ['PT Mitra Logistik Priangan', 'Jl. Gedebage Selatan, Gedebage, Bandung', 'hrd@mitralogistik.example', 6, 'aktif'],
            ['KAP Arga dan Rekan', 'Jl. Riau, Sumur Bandung, Bandung', 'hrd@argarekan.example', 2, 'aktif'],
            ['CV Lama Sentosa', 'Jl. Asia Afrika, Sumur Bandung, Bandung', 'hrd@lamasentosa.example', 5, 'nonaktif'],
        ];
        foreach ($mitra as $n => [$nama, $alamat, $kontak, $kuota, $status]) {
            $p = Perusahaan::create([
                'nama_perusahaan' => $nama, 'alamat_lengkap' => $alamat, 'kontak_hrd' => $kontak,
                'kuota_tersedia' => $kuota, 'status_mitra' => $status,
            ]);

            // Tiga mitra pertama diberi akun login: MITRA001, MITRA002, MITRA003
            if ($n < 3) {
                $kode = sprintf('MITRA%03d', $n + 1);
                User::create([
                    'nis_nip' => $kode, 'nama_lengkap' => $nama, 'email' => strtolower($kode).'@mitra.example',
                    'password' => $pw, 'role' => 'perusahaan', 'perusahaan_id' => $p->id,
                ]);
            }
        }
    }
}
