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
        'path_foto_pengerjaan',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function konsumen(): BelongsTo
    {
        return $this->belongsTo(Konsumen::class, 'nama_konsumen', 'nama');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
