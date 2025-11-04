<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PiutangJasa extends Model
{
    use SoftDeletes;

    protected $table = 'piutang_jasa';

    protected $fillable = [
        'transaksi_jasa_id',
        'total_piutang',
        'sisa_piutang',
        'status_pembayaran',
        'jatuh_tempo',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'total_piutang' => 'integer',
            'sisa_piutang' => 'integer',
            'jatuh_tempo' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function piutangJasaCicilanDetail(): HasMany
    {
        return $this->hasMany(PiutangJasaCicilanDetail::class);
    }

    public function transaksiJasa(): BelongsTo
    {
        return $this->belongsTo(TransaksiJasa::class);
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

    public static function calculateTotalPiutang(int $transaksiJasaId)
    {
        $total = TransaksiJasaDetail::where('transaksi_jasa_id', $transaksiJasaId)
            ->sum('subtotal_pendapatan');

        return $total;
    }

    public function recalculatePaymentStatus(): void
    {
        $totalCicilan = $this->piutangJasaCicilanDetail()->sum('nominal_cicilan');
        $totalPiutang = (int) ($this->total_piutang ?? 0);
        $sisa = max($totalPiutang - (int) $totalCicilan, 0);

        $status = 'belum lunas';
        if ($sisa <= 0 && $totalPiutang > 0) {
            $status = 'sudah lunas';
        } elseif ($sisa < $totalPiutang && $sisa > 0) {
            $status = 'tercicil';
        }

        $this->forceFill([
            'status_pembayaran' => $status,
            'sisa_piutang' => $sisa,
        ])->save();
    }
}
