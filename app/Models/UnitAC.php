<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitAC extends Model
{
    protected $fillable = [
        'sku',
        'nama_merk',
        'foto_produk',
        'harga_dealer',
        'harga_ecommerce',
        'harga_retail',
        'stock_awal',
        'stock_akhir',
        'stock_masuk',
        'stock_keluar',
        'remarks',
    ];
}
