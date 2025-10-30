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
        'nomor_hp',
        'gaji_pokok',
        'alamat',
        'path_foto_ktp',
        'path_dokumen_tambahan',
        'kontak_darurat_serumah',
        'kontak_darurat_tidak_serumah',
        'status_aktif',
        'tanggal_terakhir_aktif',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'gaji_pokok' => 'integer',
            'status_aktif' => 'boolean',
            'tanggal_terakhir_aktif' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function karyawanPenghasilanDetail(): HasMany
    {
        return $this->hasMany(KaryawanPenghasilanDetail::class);
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        // On soft delete: delete related User first; FK is SET NULL so user_id becomes null automatically.
        static::deleting(function (self $karyawan): void {
            if (! $karyawan->isForceDeleting()) {
                // Snapshot related user before any FK side effects
                $user = $karyawan->user()->first();
                if ($user) {
                    $user->delete(); // hard delete user so email can be reused and account can't login
                }
            }
        });

        // On permanent delete: ensure user is removed if still present (defensive)
        static::forceDeleted(function (self $karyawan): void {
            $karyawan->user?->delete();
        });
    }
}
