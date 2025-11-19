<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\TipeAC;
use App\Models\UnitAC;
use Illuminate\Support\Str;

class FrontendController extends Controller
{
    public function home()
    {
        // Popular products (top 8 by stok_keluar)
        $popularProducts = UnitAC::with('tipeAC')
            ->orderByDesc('stok_keluar')
            ->take(8)
            ->get();

        // Categories (first 4) that have units
        $categories = TipeAC::has('unitAC')->take(4)->get();

        // Products by category (4 each)
        $sections = [];
        foreach ($categories as $category) {
            $products = UnitAC::with('tipeAC')
                ->where('tipe_ac_id', $category->id)
                ->take(4)
                ->get();

            if ($products->isNotEmpty()) {
                $sections[] = [
                    'categoryId' => $category->id,
                    'title' => $category->tipe_ac,
                    'slug' => Str::slug($category->tipe_ac),
                    'products' => $products,
                ];
            }
        }

        // Sparepart section
        $spareparts = Sparepart::orderByDesc('stok_keluar')->take(4)->get();
        
        if ($spareparts->isNotEmpty()) {
            $sections[] = [
                'categoryId' => 'sparepart',
                'title' => 'Spare Part',
                'slug' => 'spare-part',
                'products' => $spareparts,
            ];
        }

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

    public function detailProducts($id)
    {
        $product = UnitAC::with(['merk', 'tipeAC'])
            ->where('id', $id)
            ->firstOrFail();

        return view('pages.detail-products', compact('product'));
    }

    public function detailSparepart($id)
    {
        $product = Sparepart::with(['merkSparepart'])
            ->where('id', $id)
            ->firstOrFail();

        return view('pages.detail-sparepart', compact('product'));
    }
}
