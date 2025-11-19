<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\TipeAC;
use App\Models\UnitAC;

class FrontendController extends Controller
{
    public function home()
    {
        // Popular products (top 8 by stok_keluar)
        $popularProducts = UnitAC::with('tipeAC')
            ->orderByDesc('stok_keluar')
            ->take(8)
            ->get();

        // Categories (first 4)
        $categories = TipeAC::take(4)->get();

        // Products by category (4 each)
        $sections = [];
        foreach ($categories as $category) {
            $sections[] = [
                'categoryId' => $category->id,
                'title' => $category->tipe_ac,
                'slug' => strtolower(str_replace(' ', '-', $category->tipe_ac)),
                'products' => UnitAC::with('tipeAC')
                    ->where('tipe_ac_id', $category->id)
                    ->take(4)
                    ->get(),
            ];
        }

        // Sparepart section
        $sections[] = [
            'categoryId' => 'Spare Part',
            'title' => 'Spare Part',
            'slug' => 'spare-part',
            'products' => Sparepart::orderByDesc('stok_keluar')
                ->take(4)
                ->get(),
        ];

        return view('pages.home', compact('popularProducts', 'sections'));
    }

    public function products()
    {
        return view('pages.products');
    }

    public function services()
    {
        return view('pages.services');
    }

    public function detailProducts($slug)
    {
        $product = UnitAC::with(['merk', 'tipeAC'])
            ->where('nama_unit', str_replace('-', ' ', $slug))
            ->firstOrFail();

        return view('pages.detail-products', compact('product'));
    }
}
