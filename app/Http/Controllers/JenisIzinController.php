<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisIzin;
use App\Helpers\LogHelper;
use Illuminate\Validation\Rule;

class JenisIzinController extends Controller
{
    public function index()
    {
        $jenisIzin = JenisIzin::where('organization_id', request()->user()->organization_id)->get();
        return view('admin.jenis_izin.index', compact('jenisIzin'));
    }

    public function store(Request $request)
    {
        $orgId = $request->user()->organization_id;
        $request->validate(['nama' => ['required', 'string', 'max:255', Rule::unique('jenis_izin')->where('organization_id', $orgId)]]);
        
        $jenisIzin = JenisIzin::create(['organization_id' => $orgId, 'nama' => $request->nama]);
        $logMessage = 'Jenis izin "' . $jenisIzin->nama . '" berhasil ditambahkan.';
        LogHelper::log($request, 'Tambah Jenis Izin', $logMessage);

        return back()->with('success', 'Jenis izin ditambahkan.');
    }

    public function destroy(Request $request, $id)
    {
        $jenisIzin = JenisIzin::where('organization_id', $request->user()->organization_id)->withCount('absensis')->findOrFail($id);

        if ($jenisIzin->absensis_count > 0) {
            $logMessage = 'Gagal menghapus jenis izin "' . $jenisIzin->nama . '" karena sudah digunakan.';
            LogHelper::log($request, 'Gagal Hapus Jenis Izin', $logMessage);
            
            return back()->with('error', 'Jenis izin "' . $jenisIzin->nama . '" tidak dapat dihapus karena sudah digunakan.');
        }

        $namaIzinDihapus = $jenisIzin->nama;
        $jenisIzin->delete();

        $logMessage = 'Jenis izin "' . $namaIzinDihapus . '" berhasil dihapus.';
        LogHelper::log($request, 'Hapus Jenis Izin', $logMessage);

        return back()->with('success', 'Jenis izin berhasil dihapus.');
    }
}
