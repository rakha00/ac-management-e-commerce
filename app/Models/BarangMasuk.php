<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class BarangMasuk extends Model
{
    use SoftDeletes;

    protected $table = 'barang_masuk';

    protected $fillable = [
        'principal_id',
        'tanggal',
        'nomor_barang_masuk',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function barangMasukDetail(): HasMany
    {
        return $this->hasMany(BarangMasukDetail::class);
    }

    public function principal(): BelongsTo
    {
        return $this->belongsTo(Principal::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function generateNomorBarangMasuk(string $tanggalInput): string
    {
        $tanggal = Carbon::parse($tanggalInput);
        $dateFormat = $tanggal->format('dmY');

        $lastNumber = self::whereDate('tanggal', $tanggal)
            ->get()
            ->map(function ($item) {
                if (preg_match('/-(\d+)$/', $item->nomor_barang_masuk, $matches)) {
                    return (int) $matches[1];
                }

                return 0;
            })
            ->max();

        $newNumber = $lastNumber + 1;

        return "BM/{$dateFormat}-{$newNumber}";
    }
}
