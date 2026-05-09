<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GuruExport implements FromView, ShouldAutoSize
{
    public function view(): View
    {
        return view('admin.guru.excel', [
            'gurus' => User::with('guru')->where('role', 'guru')->orderBy('name', 'asc')->get()
        ]);
    }
}