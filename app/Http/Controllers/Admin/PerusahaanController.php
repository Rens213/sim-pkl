<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerusahaanController extends Controller
{
    public function index(Request $request)
    {
        $daftar = Perusahaan::withCount('pengajuan')
            ->when($request->q, fn ($q, $s) => $q->where('nama_perusahaan', 'like', "%$s%"))
            ->orderBy('nama_perusahaan')
            ->get();

        return view('admin.perusahaan.index', compact('daftar'));
    }

    public function create()
    {
        return view('admin.perusahaan.form', [
            'perusahaan' => new Perusahaan(['kuota_tersedia' => 0, 'status_mitra' => 'aktif']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        // Akun login mitra bersifat opsional; isi ketiga kolom agar akun dibuat.
        $akun = $request->validate([
            'akun_nip' => ['nullable', 'string', 'max:30', 'unique:users,nis_nip'],
            'akun_email' => ['required_with:akun_nip', 'nullable', 'email', 'max:150', 'unique:users,email'],
            'akun_password' => ['required_with:akun_nip', 'nullable', 'string', 'min:8'],
        ]);

        DB::transaction(function () use ($data, $akun) {
            $p = Perusahaan::create($data);

            if (! empty($akun['akun_nip'])) {
                User::create([
                    'nis_nip' => $akun['akun_nip'],
                    'nama_lengkap' => $p->nama_perusahaan,
                    'email' => $akun['akun_email'],
                    'password' => $akun['akun_password'],
                    'role' => 'perusahaan',
                    'perusahaan_id' => $p->id,
                ]);
            }
        });

        return redirect()->route('admin.perusahaan.index')->with('ok', 'Perusahaan ditambahkan.');
    }

    public function edit(Perusahaan $perusahaan)
    {
        return view('admin.perusahaan.form', compact('perusahaan'));
    }

    public function update(Request $request, Perusahaan $perusahaan)
    {
        $perusahaan->update($this->validasi($request));

        return redirect()->route('admin.perusahaan.index')->with('ok', 'Data perusahaan diperbarui.');
    }

    public function kuota(Request $request, Perusahaan $perusahaan)
    {
        $data = $request->validate(['kuota_tersedia' => ['required', 'integer', 'min:0', 'max:500']]);
        $perusahaan->update($data);

        return back()->with('ok', "Kuota {$perusahaan->nama_perusahaan} menjadi {$data['kuota_tersedia']} siswa.");
    }

    public function destroy(Perusahaan $perusahaan)
    {
        if ($perusahaan->pengajuan()->exists()) {
            return back()->with('err', 'Perusahaan ini sudah punya pengajuan. Ubah statusnya menjadi nonaktif, jangan dihapus.');
        }

        User::where('perusahaan_id', $perusahaan->id)->delete();
        $perusahaan->delete();

        return back()->with('ok', 'Perusahaan dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama_perusahaan' => ['required', 'string', 'max:150'],
            'alamat_lengkap' => ['required', 'string', 'max:500'],
            'kontak_hrd' => ['required', 'string', 'max:150'],
            'kuota_tersedia' => ['required', 'integer', 'min:0', 'max:500'],
            'status_mitra' => ['required', 'in:aktif,nonaktif'],
        ]);
    }
}
