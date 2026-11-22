<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPkl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PelamarController extends Controller
{
    public function index(Request $request)
    {
        $id = $this->perusahaanId($request);

        $daftar = PengajuanPkl::with('ketua', 'anggota')
            ->where('perusahaan_id', $id)
            ->whereIn('status', ['disetujui_hubin', 'diterima_industri', 'ditolak_industri'])
            ->latest()
            ->get();

        return view('mitra.pelamar', ['daftar' => $daftar, 'perusahaan' => $request->user()->perusahaan]);
    }

    public function terima(Request $request, PengajuanPkl $pengajuan)
    {
        $this->pastikanMilik($request, $pengajuan);

        $hasil = DB::transaction(function () use ($pengajuan) {
            $p = PengajuanPkl::lockForUpdate()->findOrFail($pengajuan->id);

            if ($p->status !== 'disetujui_hubin') {
                return 'Pengajuan ini sudah dijawab atau belum disetujui Hubin.';
            }

            $perusahaan = $p->perusahaan()->lockForUpdate()->first();
            $ukuran = $p->ukuran();

            if ($perusahaan->kuota_tersedia < $ukuran) {
                return "Kuota tersisa {$perusahaan->kuota_tersedia} siswa, kelompok ini $ukuran siswa. Hubungi Hubin untuk menambah kuota.";
            }

            $perusahaan->decrement('kuota_tersedia', $ukuran);
            $p->update(['status' => 'diterima_industri', 'catatan' => null]);

            return null;
        });

        return $hasil ? back()->with('err', $hasil) : back()->with('ok', 'Kelompok diterima. Kuota Anda sudah dikurangi.');
    }

    public function tolak(Request $request, PengajuanPkl $pengajuan)
    {
        $this->pastikanMilik($request, $pengajuan);

        if ($pengajuan->status !== 'disetujui_hubin') {
            return back()->with('err', 'Pengajuan ini sudah dijawab atau belum disetujui Hubin.');
        }

        $data = $request->validate(['catatan' => ['nullable', 'string', 'max:500']]);
        $pengajuan->update(['status' => 'ditolak_industri', 'catatan' => $data['catatan'] ?? null]);

        return back()->with('ok', 'Kelompok ditolak.');
    }

    private function perusahaanId(Request $request): int
    {
        $id = $request->user()->perusahaan_id;
        abort_if(! $id, 403, 'Akun ini belum dihubungkan ke perusahaan. Hubungi Hubin.');

        return $id;
    }

    private function pastikanMilik(Request $request, PengajuanPkl $pengajuan): void
    {
        abort_unless($pengajuan->perusahaan_id == $this->perusahaanId($request), 403);
    }
}
