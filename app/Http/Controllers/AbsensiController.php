<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        // Validasi token harian (Asia/Jakarta) berbasis APP_KEY
        $expected = hash_hmac('sha256', now('Asia/Jakarta')->format('Y-m-d'), config('app.key'));
        if (!hash_equals($expected, (string) $request->input('token'))) {
            abort(403, 'Token tidak valid / tidak sesuai hari ini.');
        }

        $user = $request->user();
        $karyawan = $user?->karyawan;
        if (!$karyawan) {
            abort(403, 'Profil karyawan tidak ditemukan.');
        }

        $tanggal = now('Asia/Jakarta')->toDateString();
        $waktu = now('Asia/Jakarta');

        // Logika telat: jam masuk 08:00 dengan toleransi 15 menit
        $jamMasuk = now('Asia/Jakarta')->startOfDay()->setTime(8, 0, 0);
        $telat = $waktu->greaterThan($jamMasuk->clone()->addMinutes(15));

        // Simpan absen (hindari duplikasi per karyawan per tanggal)
        Absensi::firstOrCreate(
            ['karyawan_id' => $karyawan->id, 'tanggal' => $tanggal],
            [
                'waktu_absen' => $waktu,
                'telat' => $telat,
                'keterangan' => $telat ? 'Terlambat' : 'Tepat waktu',
                'terkonfirmasi' => false,
            ]
        );

        return redirect()->back()->with('status', 'Absen tercatat.');
    }
}
