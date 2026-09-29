<?php

namespace App\Http\Controllers\Eksekutif;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('eksekutif.dashboard');
    }
}
