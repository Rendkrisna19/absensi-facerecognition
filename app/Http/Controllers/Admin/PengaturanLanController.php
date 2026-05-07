<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IpLokal;
use Illuminate\Http\Request;

class PengaturanLanController extends Controller
{
    public function index()
    {
        $ips = IpLokal::latest()->get();
        return view('admin.pengaturan_lan.index', compact('ips'));
    }

    public function create()
    {
        return view('admin.pengaturan_lan.create');
    }

    public function store(Request $request)
    {
        // Validasi 'ip' otomatis mengecek format angka IP address yang benar
        $request->validate([
            'nama_jaringan' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:ip_lokals,ip_address',
            'is_active' => 'required|boolean',
        ], [
            'ip_address.ip' => 'Format IP Address tidak valid (Contoh: 192.168.1.1)',
            'ip_address.unique' => 'IP Address ini sudah terdaftar di sistem.'
        ]);

        IpLokal::create($request->all());

        return redirect()->route('admin.pengaturan-lan.index')->with('success', 'Jaringan LAN berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $ip = IpLokal::findOrFail($id);
        return view('admin.pengaturan_lan.edit', compact('ip'));
    }

    public function update(Request $request, $id)
    {
        $ip = IpLokal::findOrFail($id);

        $request->validate([
            'nama_jaringan' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:ip_lokals,ip_address,' . $id,
            'is_active' => 'required|boolean',
        ]);

        $ip->update($request->all());

        return redirect()->route('admin.pengaturan-lan.index')->with('success', 'Pengaturan LAN berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $ip = IpLokal::findOrFail($id);
        $ip->delete();

        return redirect()->route('admin.pengaturan-lan.index')->with('success', 'Data Jaringan LAN berhasil dihapus!');
    }
}