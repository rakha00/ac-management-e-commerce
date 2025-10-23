<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laporan extends Model
{
    use SoftDeletes;

    protected $table = 'laporan';

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'nama_konsumen',
        'jenis_pengerjaan',
        'foto_pengerjaan',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }
}
