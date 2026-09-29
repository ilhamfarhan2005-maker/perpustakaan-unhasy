<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;

class RiwayatController extends Controller
{
    public function index()
    {
        return view('mahasiswa.riwayat');
    }
}
