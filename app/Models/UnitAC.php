<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'stock_masuk',
        'stock_keluar',
        'remarks',
    ];

    // Hapus stock_akhir dari fillable karena akan dihitung otomatis

    protected $appends = ['stock_akhir']; // Agar stock_akhir muncul di JSON

    /**
     * Accessor untuk stock_akhir
     * Otomatis menghitung: stock_awal + stock_masuk - stock_keluar
     */
    protected function stockAkhir(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->stock_awal ?? 0) + ($this->stock_masuk ?? 0) - ($this->stock_keluar ?? 0),
        );
    }

    /**
     * Boot method untuk auto-update stock_akhir sebelum save
     */
    protected static function booted(): void
    {
        static::saving(function ($unitAc) {
            // Hitung stock_akhir sebelum disimpan ke database
            $unitAc->stock_akhir = ($unitAc->stock_awal ?? 0) 
                                 + ($unitAc->stock_masuk ?? 0) 
                                 - ($unitAc->stock_keluar ?? 0);
        });
    }
}