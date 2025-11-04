<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SparepartKeluarDetail extends Model
{
    use SoftDeletes;

    protected $table = 'sparepart_keluar_detail';

    protected $fillable = [
        'sparepart_keluar_id',
        'sparepart_id',
        'jumlah_keluar',
        'harga_modal',
        'harga_jual',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_keluar' => 'integer',
            'harga_modal' => 'integer',
            'harga_jual' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    public function sparepartKeluar(): BelongsTo
    {
        return $this->belongsTo(SparepartKeluar::class, 'sparepart_keluar_id');
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

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function (SparepartKeluarDetail $detail) {
            $detail->updateStokSparepart((int) $detail->jumlah_keluar, 'out');
        });

        static::updating(function (SparepartKeluarDetail $detail) {
            $oldQty = (int) $detail->getOriginal('jumlah_keluar');
            $newQty = (int) $detail->jumlah_keluar;
            $oldSpId = $detail->getOriginal('sparepart_id');
            $newSpId = $detail->sparepart_id;

            if ($oldSpId != $newSpId) {
                // Sparepart changed: revert old stock, apply new stock
                if ($oldSpId) {
                    $detail->updateStokSparepart($oldQty, 'revert', $oldSpId);
                }
                if ($newSpId) {
                    $detail->updateStokSparepart($newQty, 'out', $newSpId);
                }
            } else {
                // Sparepart unchanged: adjust stock by difference
                $diff = $newQty - $oldQty;
                if ($diff !== 0) {
                    $detail->updateStokSparepart(abs($diff), $diff > 0 ? 'out' : 'revert', $newSpId);
                }
            }

        });

        static::deleted(function (SparepartKeluarDetail $detail) {
            $detail->updateStokSparepart((int) $detail->jumlah_keluar, 'revert');
        });

        static::restored(function (SparepartKeluarDetail $detail) {
            $detail->updateStokSparepart((int) $detail->jumlah_keluar, 'out');
        });
    }

    /**
     * Update Sparepart stock based on action ('out' or 'revert').
     */
    private function updateStokSparepart(int $jumlah, string $action, ?int $sparepartId = null): void
    {
        $sp = Sparepart::find($sparepartId ?? $this->sparepart_id);

        if (! $sp || $jumlah <= 0) {
            return;
        }

        if ($action === 'out') {
            $sp->increment('stok_keluar', $jumlah);
            $sp->decrement('stok_akhir', $jumlah);
        } else { // action === 'revert'
            $sp->decrement('stok_keluar', $jumlah);
            $sp->increment('stok_akhir', $jumlah);
        }
    }
}
