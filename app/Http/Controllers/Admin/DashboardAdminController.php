<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_guru' => 45,
            'hadir' => 38,
            'terlambat' => 5,
            'alpa' => 2,
        ];

        return view('admin.index', compact('stats'));
    }
}