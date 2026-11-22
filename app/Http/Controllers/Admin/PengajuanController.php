<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPkl;
use App\Services\SuratPengantar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $daftar = PengajuanPkl::with('perusahaan', 'ketua')->withCount('anggota')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->q, fn ($q, $s) => $q->where(
                fn ($w) => $w->where('kode_pengajuan', 'like', "%$s%")
                    ->orWhereHas('perusahaan', fn ($p) => $p->where('nama_perusahaan', 'like', "%$s%"))
                    ->orWhereHas('ketua', fn ($k) => $k->where('nama_lengkap', 'like', "%$s%"))
            ))
            ->latest()
            ->get();

        $hitung = PengajuanPkl::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.pengajuan.index', compact('daftar', 'hitung'));
    }

    public function show(PengajuanPkl $pengajuan)
    {
        $pengajuan->load('perusahaan', 'ketua', 'anggota');

        $saranGuru = collect(config('sim.guru_pembimbing'))
            ->merge(PengajuanPkl::whereNotNull('guru_pembimbing')->distinct()->pluck('guru_pembimbing'))
            ->unique()->values();

        return view('admin.pengajuan.show', ['p' => $pengajuan, 'saranGuru' => $saranGuru]);
    }

    public function setujui(PengajuanPkl $pengajuan)
    {
        if ($pengajuan->status !== 'dikirim') {
            return back()->with('err', 'Pengajuan ini sudah diproses.');
        }

        if ($pengajuan->perusahaan->kuota_tersedia < $pengajuan->ukuran()) {
            return back()->with('err', 'Kuota perusahaan tidak cukup untuk kelompok ini. Tambah kuota dulu atau tolak pengajuan.');
        }

        DB::transaction(function () use ($pengajuan) {
            $tahun = now()->year;
            $romawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][now()->month];
            $urut = PengajuanPkl::where('nomor_surat', 'like', "%/$tahun")->lockForUpdate()->count() + 1;

            $pengajuan->update([
                'status' => 'disetujui_hubin',
                'nomor_surat' => sprintf('%03d/HUBIN/SMK-ICBCN/%s/%d', $urut, $romawi, $tahun),
                'tgl_surat' => now()->toDateString(),
                'catatan' => null,
            ]);
        });

        return back()->with('ok', 'Pengajuan disetujui. Surat pengantar sudah bisa diunduh.');
    }

    public function tolak(Request $request, PengajuanPkl $pengajuan)
    {
        if ($pengajuan->status !== 'dikirim') {
            return back()->with('err', 'Pengajuan ini sudah diproses.');
        }

        $data = $request->validate(['catatan' => ['required', 'string', 'max:500']], [
            'catatan.required' => 'Tulis alasan penolakan agar siswa tahu apa yang perlu diperbaiki.',
        ]);

        $pengajuan->update(['status' => 'ditolak_hubin', 'catatan' => $data['catatan']]);

        return back()->with('ok', 'Pengajuan ditolak.');
    }

    public function guru(Request $request, PengajuanPkl $pengajuan)
    {
        abort_unless($pengajuan->nomor_surat, 422, 'Guru pembimbing diplot setelah pengajuan disetujui.');

        $data = $request->validate(['guru_pembimbing' => ['required', 'string', 'max:100']]);
        $pengajuan->update($data);

        return back()->with('ok', 'Guru pembimbing disimpan.');
    }

    public function surat(PengajuanPkl $pengajuan)
    {
        abort_unless($pengajuan->nomor_surat, 404, 'Surat pengantar belum diterbitkan.');

        return SuratPengantar::pdf($pengajuan)->stream(SuratPengantar::namaFile($pengajuan));
    }
}
