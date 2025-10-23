<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailPenghasilanKaryawan extends Model
{
    use SoftDeletes;

    protected $table = 'detail_penghasilan_karyawan';

    protected $fillable = [
        'karyawan_id',
        'kasbon',
        'lembur',
        'bonus',
        'potongan',
        'keterangan',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'kasbon' => 'integer',
            'lembur' => 'integer',
            'bonus' => 'integer',
            'potongan' => 'integer',
            'tanggal' => 'date',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }
}
