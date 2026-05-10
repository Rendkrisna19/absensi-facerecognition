<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IpLokal;
use Illuminate\Http\Request;

class PengaturanLanController extends Controller
{
    public function index(Request $request)
    {
        $query = IpLokal::latest();

        // Filter Pencarian (Nama Jaringan / IP)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_jaringan', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Filter Status Aktif/Non-Aktif
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $ips = $query->paginate($perPage)->withQueryString();

        return view('admin.pengaturan_lan.index', compact('ips'));
    }

    // Fungsi Baru untuk AJAX Toggle Switch
    public function toggleStatus(Request $request, $id)
    {
        try {
            $ip = IpLokal::findOrFail($id);
            $ip->is_active = !$ip->is_active; // Balikkan statusnya (0 jadi 1, 1 jadi 0)
            $ip->save();

            return response()->json([
                'success' => true,
                'message' => 'Status jaringan berhasil diperbarui!',
                'is_active' => $ip->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status.'
            ], 500);
        }
    }

    public function create()
    {
        return view('admin.pengaturan_lan.create');
    }

    public function store(Request $request)
    {
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