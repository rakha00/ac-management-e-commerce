<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Karyawan extends Model
{
    use SoftDeletes;

    protected $table = 'karyawan';

    protected $fillable = [
        'user_id',
        'nama',
        'jabatan',
        'no_hp',
        'gaji_pokok',
        'alamat',
        'foto_ktp',
        'dokumen_tambahan',
        'kontak_darurat_serumah',
        'kontak_darurat_tidak_serumah',
        'status_aktif',
        'tanggal_terakhir_aktif',
    ];

    protected function casts(): array
    {
        return [
            'gaji_pokok' => 'integer',
            'status_aktif' => 'boolean',
            'tanggal_terakhir_aktif' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detailPenghasilanKaryawan(): HasMany
    {
        return $this->hasMany(DetailPenghasilanKaryawan::class);
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }
}
