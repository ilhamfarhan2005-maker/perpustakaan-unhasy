<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MemberCardController extends Controller
{
    public function show()
    {
        $user = request()->user();
        abort_if(blank($user->nim_nip), 404, 'NIM belum tersedia.');
        return view('mahasiswa.kartu', [
            'anggota' => $user,
            'qrImage' => base64_encode(QrCode::format('svg')->size(220)->margin(1)->generate($user->nim_nip)),
        ]);
    }
}
