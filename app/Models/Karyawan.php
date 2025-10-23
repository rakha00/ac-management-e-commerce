<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
