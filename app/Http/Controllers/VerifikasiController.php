<?php

namespace App\Http\Controllers;

use App\Models\PengajuanPkl;

class VerifikasiController extends Controller
{
    /** Halaman publik yang dibuka lewat QR Code di Surat Pengantar. */
    public function show(string $kode)
    {
        $p = PengajuanPkl::with('ketua', 'anggota', 'perusahaan')
            ->where('kode_pengajuan', $kode)
            ->whereNotNull('nomor_surat')
            ->first();

        return view('verifikasi', compact('p', 'kode'));
    }
}
