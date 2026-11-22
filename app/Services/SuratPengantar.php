<?php

namespace App\Services;

use App\Models\PengajuanPkl;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class SuratPengantar
{
    public static function pdf(PengajuanPkl $pengajuan)
    {
        $pengajuan->loadMissing('ketua', 'anggota', 'perusahaan');

        // QR berisi URL verifikasi publik; PNG base64 (butuh ekstensi GD, aktif di Laragon).
        $qr = (new QRCode(new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'outputBase64' => true,
            'scale' => 6,
            'quietzoneSize' => 1,
        ])))->render(route('verifikasi', $pengajuan->kode_pengajuan));

        return Pdf::loadView('surat.pengantar', [
            'p' => $pengajuan,
            'qr' => $qr,
            's' => config('sim'),
        ])->setPaper('a4', 'portrait');
    }

    public static function namaFile(PengajuanPkl $pengajuan): string
    {
        return 'Surat-Pengantar-'.$pengajuan->kode_pengajuan.'.pdf';
    }
}