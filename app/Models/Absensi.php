<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'waktu_absen',
        'telat',
        'keterangan',
        'terkonfirmasi',
        'dikonfirmasi_oleh',
        'waktu_konfirmasi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'waktu_absen' => 'datetime',
            'telat' => 'boolean',
            'terkonfirmasi' => 'boolean',
            'waktu_konfirmasi' => 'datetime',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function dikonfirmasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }
}
