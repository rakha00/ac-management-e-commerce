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
        'jumlah_masuk',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'sparepart_masuk_id' => 'integer',
            'sparepart_id' => 'integer',
            'jumlah_masuk' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
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

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    protected static function boot()
    {
        parent::boot();

        // Set created_by and updated_by when creating
        static::creating(function (self $sparepartMasukDetail): void {
            if (auth()->check()) {
                $sparepartMasukDetail->created_by = auth()->id();
                $sparepartMasukDetail->updated_by = auth()->id();
            }
        });

        // Set updated_by when updating
        static::updating(function (self $sparepartMasukDetail): void {
            if (auth()->check()) {
                $sparepartMasukDetail->updated_by = auth()->id();
            }
        });

        // Set deleted_by when soft deleting
        static::deleting(function (self $sparepartMasukDetail): void {
            if (! $sparepartMasukDetail->isForceDeleting()) {
                if (auth()->check()) {
                    $sparepartMasukDetail->deleted_by = auth()->id();
                    $sparepartMasukDetail->save();
                }
            }
        });

        static::saving(function ($detail) {
            $originalQuantity = $detail->getOriginal('jumlah_masuk');
            $originalSparepartId = $detail->getOriginal('sparepart_id');

            $newQuantity = $detail->jumlah_masuk;
            $newSparepartId = $detail->sparepart_id;

            if ($detail->exists) {
                if ($originalSparepartId !== $newSparepartId) {
                    $detail->updateSparepartStock($originalQuantity, 'decrement', $originalSparepartId);
                    $detail->updateSparepartStock($newQuantity, 'increment', $newSparepartId);
                } else {
                    $difference = $newQuantity - $originalQuantity;
                    if ($difference > 0) {
                        $detail->updateSparepartStock($difference, 'increment');
                    } elseif ($difference < 0) {
                        $detail->updateSparepartStock(abs($difference), 'decrement');
                    }
                }
            } else {
                $detail->updateSparepartStock($newQuantity, 'increment');
            }
        });

        static::softDeleted(function ($detail) {
            $detail->updateSparepartStock($detail->getOriginal('jumlah_masuk'), 'decrement', $detail->getOriginal('sparepart_id'));
        });

        static::restored(function ($detail) {
            $detail->updateSparepartStock($detail->jumlah_masuk, 'increment', $detail->sparepart_id);
        });
    }

    private function updateSparepartStock(int $quantity, string $action, ?int $sparepartId = null)
    {
        $sparepart = Sparepart::find($sparepartId ?? $this->sparepart_id);

        if (! $sparepart) {
            return;
        }

        if ($action === 'increment') {
            $sparepart->increment('stok_masuk', $quantity);
        } else {
            $sparepart->decrement('stok_masuk', $quantity);
        }
    }
}
