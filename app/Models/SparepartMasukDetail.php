<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SparepartMasukDetail extends Model
{
    use SoftDeletes;

    protected $table = 'sparepart_masuk_detail';

    protected $fillable = [
        'sparepart_masuk_id',
        'sparepart_id',
        'kode_sparepart',
        'nama_sparepart',
        'jumlah_masuk',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_masuk' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function sparepartMasuk(): BelongsTo
    {
        return $this->belongsTo(SparepartMasuk::class, 'sparepart_masuk_id');
    }

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class, 'sparepart_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Boot the model to handle events.
     */
    protected static function boot()
    {
        parent::boot();

        // Before creating, ensure denormalized fields are populated from Sparepart.
        static::creating(function (SparepartMasukDetail $detail) {
            if ($detail->sparepart_id && (empty($detail->kode_sparepart) || empty($detail->nama_sparepart))) {
                $sp = Sparepart::find($detail->sparepart_id);
                if ($sp) {
                    if (empty($detail->kode_sparepart)) {
                        $detail->kode_sparepart = $sp->kode_sparepart;
                    }
                    if (empty($detail->nama_sparepart)) {
                        $detail->nama_sparepart = $sp->nama_sparepart;
                    }
                }
            }
        });

        // When a new detail is created
        static::created(function (SparepartMasukDetail $detail) {
            $detail->updateStokSparepart((int) $detail->jumlah_masuk, 'in');
            $detail->recalculateParent();
        });

        // Before a detail is updated (handle change in qty or sparepart)
        static::updating(function (SparepartMasukDetail $detail) {
            $oldJumlah = (int) $detail->getOriginal('jumlah_masuk');
            $newJumlah = (int) $detail->jumlah_masuk;
            $oldSparepartId = $detail->getOriginal('sparepart_id');
            $newSparepartId = $detail->sparepart_id;

            // If the Sparepart has changed, revert from old and apply to new
            if ($oldSparepartId != $newSparepartId) {
                if ($oldSparepartId) {
                    $detail->updateStokSparepart($oldJumlah, 'revert', $oldSparepartId);
                }
                if ($newSparepartId) {
                    $detail->updateStokSparepart($newJumlah, 'in', $newSparepartId);
                }
            } else {
                // Sparepart unchanged, adjust by difference
                $diff = $newJumlah - $oldJumlah;
                if ($diff !== 0) {
                    $detail->updateStokSparepart(abs($diff), $diff > 0 ? 'in' : 'revert', $newSparepartId);
                }
            }

            // Keep denormalized fields in sync if sparepart_id changed
            if ($oldSparepartId != $newSparepartId && $newSparepartId) {
                $sp = Sparepart::find($newSparepartId);
                if ($sp) {
                    $detail->kode_sparepart = $sp->kode_sparepart;
                    $detail->nama_sparepart = $sp->nama_sparepart;
                }
            }
        });

        // After update, keep parent totals in sync
        static::updated(function (SparepartMasukDetail $detail) {
            $detail->recalculateParent();
        });

        // On soft delete, revert stok (return stok back)
        static::deleted(function (SparepartMasukDetail $detail) {
            $detail->updateStokSparepart((int) $detail->jumlah_masuk, 'revert');
            $detail->recalculateParent();
        });

        // On restore, re-apply stok in
        static::restored(function (SparepartMasukDetail $detail) {
            $detail->updateStokSparepart((int) $detail->jumlah_masuk, 'in');
            $detail->recalculateParent();
        });
    }

    /**
     * Update Sparepart stoks for 'in' (incoming) or 'revert' (undo incoming).
     */
    private function updateStokSparepart(int $jumlah, string $action, ?int $sparepartId = null): void
    {
        $sp = Sparepart::find($sparepartId ?? $this->sparepart_id);

        if (! $sp || $jumlah <= 0) {
            return;
        }

        if ($action === 'in') {
            $sp->increment('stok_masuk', $jumlah);
            $sp->increment('stok_akhir', $jumlah);
        } else {
            $sp->decrement('stok_masuk', $jumlah);
            $sp->decrement('stok_akhir', $jumlah);
        }
    }

    /**
     * Recalculate parent totals based on current non-trashed details.
     */
    private function recalculateParent(): void
    {
        $parent = $this->sparepartMasuk;
        if ($parent instanceof SparepartMasuk) {
            $parent->recalcFromDetails();
        }
    }
}
