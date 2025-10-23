<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCash extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'pengeluaran',
        'keterangan_pengeluaran',
        'pemasukan',
        'keterangan_pemasukan',
        'bukti_pembayaran',
    ];
}
