<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengeluaranKantor extends Model
{
    use SoftDeletes;

    protected $table = 'pengeluaran_kantor';

    protected $fillable = [
        'tanggal',
        'pengeluaran',
        'keterangan_pengeluaran',
        'path_bukti_pembayaran',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'pengeluaran' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
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
