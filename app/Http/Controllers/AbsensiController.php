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

        // Daily token validation (Asia/Jakarta) based on APP_KEY
        $tz = 'Asia/Jakarta';
        $now = now($tz);
        $expected = hash_hmac('sha256', $now->format('Y-m-d'), config('app.key'));
        if (! hash_equals($expected, (string) $request->input('token'))) {
            abort(403, 'Token tidak valid / tidak sesuai hari ini.');
        }

        $user = $request->user();
        $karyawan = $user?->karyawan;
        if (! $karyawan) {
            abort(403, 'Profil karyawan tidak ditemukan.');
        }

        $waktu = $now;

        // Late logic: check-in time 08:00 with 15-minute tolerance
        $jamMasuk = $now->clone()->startOfDay()->setTime(...explode(':', config('absensi.jam_masuk')));
        $telat = $waktu->greaterThan($jamMasuk->clone()->addMinutes((int) config('absensi.toleransi_telat_menit')));

        // Save attendance (prevent duplicates per employee per date)
        $absensi = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('waktu_absen', $now->toDateString())
            ->first();

        if (! $absensi) {
            $absensi = Absensi::create([
                'karyawan_id' => $karyawan->id,
                'waktu_absen' => $waktu,
                'is_telat' => $telat,
                'keterangan' => $telat ? 'Terlambat' : 'Tepat waktu',
                'is_terkonfirmasi' => false,
            ]);
            $absensi->wasRecentlyCreated = true;
        } else {
            $absensi->wasRecentlyCreated = false;
        }

        // If the request is AJAX (fetch from the scan page), return JSON so the UI can display a modal based on the result
        if ($request->ajax()) {
            return response()->json([
                'ok' => true,
                'created' => $absensi->wasRecentlyCreated, // true = berhasil baru, false = sudah pernah absen hari ini
            ]);
        }

        // Non-AJAX fallback: redirect back with a message based on status
        $message = $absensi->wasRecentlyCreated ? 'Absen tercatat.' : 'Anda sudah melakukan absensi hari ini.';

        return redirect()->back()->with('status', $message);
    }
}
