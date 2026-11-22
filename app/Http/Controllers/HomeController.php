<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;

class HomeController extends Controller
{
    public function index()
    {
        $mitra = Perusahaan::aktif();

        return view('welcome', [
            'jumlahMitra' => (clone $mitra)->count(),
            'totalKuota' => (clone $mitra)->sum('kuota_tersedia'),
        ]);
    }
}
