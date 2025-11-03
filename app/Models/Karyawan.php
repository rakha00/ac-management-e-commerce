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
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'gaji_pokok' => 'integer',
            'status_aktif' => 'boolean',
            'tanggal_terakhir_aktif' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function karyawanPenghasilanDetail(): HasMany
    {
        return $this->hasMany(KaryawanPenghasilanDetail::class);
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
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

    public function getTotalGaji(?string $dari = null, ?string $sampai = null): int
    {
        $totalPenghasilan = $this->gaji_pokok;

        $query = $this->karyawanPenghasilanDetail();

        if ($dari) {
            $query->whereDate('tanggal', '>=', $dari);
        }

        if ($sampai) {
            $query->whereDate('tanggal', '<=', $sampai);
        }

        $details = $query->get();

        foreach ($details as $detail) {
            $totalPenghasilan += -$detail->kasbon + $detail->lembur + $detail->bonus - $detail->potongan;
        }

        return $totalPenghasilan;
    }

    protected static function booted(): void
    {
        // Set created_by  when creating
        static::creating(function (self $karyawan): void {
            if (auth()->check()) {
                $karyawan->created_by = auth()->id();
                $karyawan->updated_by = auth()->id();
            }
        });

        // Set updated_by when updating
        static::updating(function (self $karyawan): void {
            if (auth()->check()) {
                $karyawan->updated_by = auth()->id();
            }
        });

        // On soft delete: delete related User first; FK is SET NULL so user_id becomes null automatically.
        static::deleting(function (self $karyawan): void {
            if (! $karyawan->isForceDeleting()) {
                if (auth()->check()) {
                    $karyawan->deleted_by = auth()->id();
                    $karyawan->save(); // Save the model to persist the deleted_by value
                }
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
