@props(['categoryId' => '', 'title' => '', 'category' => '', 'categorySlug' => '', 'products' => collect()])

<section id="{{ $categorySlug }}" class="bg-white p-6 rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-900">{{ $title }}</h2>
        @if($categoryId === 'sparepart')
            <a href="/produk?category=sparepart" class="text-sm font-semibold text-gsi-red hover:underline">Lihat Semua</a>
        @else
            <a href="/produk?tipe={{ $categoryId }}" class="text-sm font-semibold text-gsi-red hover:underline">Lihat Semua</a>
        @endif
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 md:gap-4">
        @foreach ($products as $product)
            @php
                $isSparepart = isset($product->nama_sparepart);
                $productName = $isSparepart ? $product->nama_sparepart : $product->nama_unit;
                $productType = $isSparepart ? 'sparepart' : 'unit';
                $productPrice = $product->harga_ecommerce;
                
                $productImage = null;
                if ($isSparepart) {
                    if (!empty($product->path_foto_sparepart) && is_array($product->path_foto_sparepart) && count($product->path_foto_sparepart) > 0) {
                        $productImage = asset('storage/' . $product->path_foto_sparepart[0]);
                    }
                } else {
                    if (!empty($product->path_foto_produk) && is_array($product->path_foto_produk) && count($product->path_foto_produk) > 0) {
                        $productImage = asset('storage/' . $product->path_foto_produk[0]);
                    }
                }
                
                $productImage = $productImage ?? asset('img/produk/placeholder.png');
                
                $href = $isSparepart ? route('detail-sparepart', $product->id) : route('detail-products', $product->id);
            @endphp
            <x-product-card :id="$product->id" :type="$productType" category="{{ $category }}" title="{{ $productName }}"
                price="Rp {{ number_format($productPrice, 0, ',', '.') }}" image="{{ $productImage }}"
                href="{{ $href }}" />
        @endforeach
    </div>
</section>
