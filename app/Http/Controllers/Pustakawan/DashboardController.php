<?php

namespace App\Http\Controllers\Pustakawan;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pustakawan.dashboard');
    }
}
