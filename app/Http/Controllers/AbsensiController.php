<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AbsensiController extends Controller
{
    /**
     * VALIDASI TOKEN DAN WAKTU
     * Dipanggil setelah scan QR
     */
    public function validateToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        // Get config
        $jamMasukConfig = config('absensi.jam_masuk', '06:00');
        $toleransiTelatMenit = config('absensi.toleransi_telat_menit', 0);

        // Current time Asia/Jakarta
        $now = Carbon::now('Asia/Jakarta');

        // Parse waktu absen
        [$hour, $minute] = explode(':', $jamMasukConfig);
        $waktuAbsen = $now->copy()->startOfDay()->setTime($hour, $minute);

        // **VALIDASI WAKTU ABSEN**
        if ($now->lt($waktuAbsen)) {
            return response()->json([
                'ok' => false,
                'message' => 'Belum waktunya absen. Absensi dapat dilakukan mulai '.$waktuAbsen->format('H:i').' WIB.',
            ], 400);
        }

        // Validate token
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $expectedToken = hash_hmac('sha256', $today, config('app.key'));

        if ($request->token !== $expectedToken) {
            return response()->json([
                'ok' => false,
                'message' => 'Token QR tidak valid atau sudah kadaluarsa.',
            ], 403);
        }

        // Check if already absen today
        $karyawanId = auth()->user()?->karyawan?->id;
        if (! $karyawanId) {
            return response()->json([
                'ok' => false,
                'message' => 'Profil karyawan tidak ditemukan.',
            ], 400);
        }

        $existing = Absensi::where('karyawan_id', $karyawanId)
            ->whereDate('waktu_absen', $today)
            ->first();

        if ($existing) {
            return response()->json([
                'ok' => true,
                'created' => false,
                'requires_photo' => true,
                'message' => 'Silakan ambil foto bukti (memperbarui foto lama).',
            ]);
        }

        // Token valid, belum absen, waktu OK -> Redirect ke foto
        return response()->json([
            'ok' => true,
            'requires_photo' => true,
            'message' => 'Token valid. Silakan ambil foto bukti.',
        ]);
    }

    /**
     * CREATE ABSENSI SETELAH FOTO
     */
    public function storeWithPhoto(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'foto_bukti' => 'required|image|max:5120', // max 5MB
        ]);

        // Get config
        $jamMasukConfig = config('absensi.jam_masuk', '06:00');
        $toleransiTelatMenit = config('absensi.toleransi_telat_menit', 0);

        // Current time
        $now = Carbon::now('Asia/Jakarta');

        // Parse waktu
        [$hour, $minute] = explode(':', $jamMasukConfig);
        $waktuAbsen = $now->copy()->startOfDay()->setTime($hour, $minute);
        $waktuTelat = $waktuAbsen->copy()->addMinutes((int) $toleransiTelatMenit);

        // Validate token again
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $expectedToken = hash_hmac('sha256', $today, config('app.key'));

        if ($request->token !== $expectedToken) {
            return response()->json([
                'ok' => false,
                'message' => 'Token tidak valid.',
            ], 403);
        }

        // Get karyawan
        $karyawanId = auth()->user()?->karyawan?->id;
        if (! $karyawanId) {
            return response()->json([
                'ok' => false,
                'message' => 'Profil karyawan tidak ditemukan.',
            ], 400);
        }

        // Check if this specific employee already has attendance today
        $existing = Absensi::where('karyawan_id', $karyawanId)
            ->whereDate('waktu_absen', $today)
            ->first();

        if ($existing) {
            // Hapus foto lama jika ada
            if ($existing->foto_bukti) {
                $oldFotoPath = storage_path('app/private/'.$existing->foto_bukti);
                if (file_exists($oldFotoPath)) {
                    unlink($oldFotoPath);
                }
            }

            // Update only the foto_bukti field
            $fotoPath = $request->file('foto_bukti')->store('foto-bukti-absensi', 'local');
            $existing->update([
                'foto_bukti' => $fotoPath,
            ]);

            return response()->json([
                'ok' => true,
                'created' => false,
                'updated' => true,
                'message' => 'Foto bukti berhasil diperbarui.',
                'absensi_id' => $existing->id,
            ]);
        } else {
            // Store photo
            $fotoPath = $request->file('foto_bukti')->store('foto-bukti-absensi', 'local');

            // Create new absensi
            $absensi = Absensi::create([
                'karyawan_id' => $karyawanId,
                'waktu_absen' => $now,
                'is_telat' => $now->gt($waktuTelat),
                'foto_bukti' => $fotoPath,
                'token' => $request->token,
                'is_terkonfirmasi' => false,
                'dikonfirmasi_oleh_id' => null,
                'dikonfirmasi_pada' => null,
            ]);

            return response()->json([
                'ok' => true,
                'created' => true,
                'updated' => false,
                'message' => 'Absensi berhasil disimpan.',
                'absensi_id' => $absensi->id,
            ]);
        }
    }
}
