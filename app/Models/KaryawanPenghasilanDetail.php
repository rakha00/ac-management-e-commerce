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
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'karyawan_id' => 'integer',
            'kasbon' => 'integer',
            'lembur' => 'integer',
            'bonus' => 'integer',
            'potongan' => 'integer',
            'tanggal' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
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

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    protected static function booted(): void
    {
        // Set created_by  when creating
        static::creating(function (self $penghasilanKaryawan): void {
            if (auth()->check()) {
                $penghasilanKaryawan->created_by = auth()->id();
                $penghasilanKaryawan->updated_by = auth()->id();
            }
        });

        // Set updated_by when updating
        static::updating(function (self $penghasilanKaryawan): void {
            if (auth()->check()) {
                $penghasilanKaryawan->updated_by = auth()->id();
            }
        });

        // Set deleted_by when soft deleting
        static::deleting(function (self $penghasilanKaryawan): void {
            if (auth()->check()) {
                $penghasilanKaryawan->deleted_by = auth()->id();
                $penghasilanKaryawan->save();
            }
        });
    }
}
