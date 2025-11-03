<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\UnitAC;

class FrontendController extends Controller
{
    public function home()
    {
        // Logika untuk halaman beranda
        $unitACs = UnitAC::limit(3)->get();
        $spareparts = Sparepart::limit(3)->get();

        return view('home', compact('unitACs', 'spareparts'));
    }

    public function category($type)
    {
        // Logika untuk halaman kategori
        if ($type === 'ac') {
            $products = UnitAC::all();
        } elseif ($type === 'sparepart') {
            $products = Sparepart::all();
        } else {
            $products = collect(); // Empty collection
        }

        return view('category', compact('products', 'type'));
    }

    public function productDetail($id)
    {
        // Logika untuk halaman detail produk
        $product = UnitAC::find($id) ?? Sparepart::find($id);
        if (!$product) {
            abort(404);
        }

        return view('product_detail', compact('product'));
    }

    public function aboutUs()
    {
        // Logika untuk halaman tentang kami
        return view('about_us');
    }
}
