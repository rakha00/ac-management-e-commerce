<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class KaryawanPenghasilanDetail extends Model
{
    use SoftDeletes;

    protected $table = 'karyawan_penghasilan_detail';

    protected $fillable = [
        'karyawan_id',
        'kasbon',
        'lembur',
        'bonus',
        'potongan',
        'keterangan',
        'tanggal',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'kasbon' => 'integer',
            'lembur' => 'integer',
            'bonus' => 'integer',
            'potongan' => 'integer',
            'tanggal' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
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
