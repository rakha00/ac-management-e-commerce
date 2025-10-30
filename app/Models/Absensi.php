<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'karyawan_id',
        'waktu_absen',
        'is_telat',
        'keterangan',
        'is_terkonfirmasi',
        'dikonfirmasi_oleh_id',
        'dikonfirmasi_pada',
    ];

    protected function casts(): array
    {
        return [
            'waktu_absen' => 'datetime',
            'is_telat' => 'boolean',
            'is_terkonfirmasi' => 'boolean',
            'dikonfirmasi_pada' => 'datetime',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function dikonfirmasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh_id');
    }
}
