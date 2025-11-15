@props(['categoryId' => '', 'title' => '', 'category' => '', 'categorySlug' => '', 'products' => collect()])

<section id="{{ $categorySlug }}" class="bg-white p-6 rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-900">{{ $title }}</h2>
        <a href="/produk?tipe={{ $categoryId }}" class="text-sm font-semibold text-gsi-red hover:underline">Lihat
            Semua</a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($products as $product)
            @php
                $productName = isset($product->nama_unit) ? $product->nama_unit : $product->nama_sparepart;
                $productPrice = $product->harga_ecommerce;
                $productImage = isset($product->path_foto_produk)
                    ? (!empty($product->path_foto_produk) &&
                    is_array($product->path_foto_produk) &&
                    count($product->path_foto_produk) > 0
                        ? $product->path_foto_produk[0]
                        : 'https://placehold.co/300x300/e0e0e0/969696?text=' . urlencode($category))
                    : (!empty($product->path_foto_sparepart) &&
                    is_array($product->path_foto_sparepart) &&
                    count($product->path_foto_sparepart) > 0
                        ? $product->path_foto_sparepart[0]
                        : 'https://placehold.co/300x300/e0e0e0/969696?text=' . urlencode($category));
            @endphp
            <x-product-card category="{{ $category }}" title="{{ $productName }}"
                price="Rp {{ number_format($productPrice, 0, ',', '.') }}" image="{{ $productImage }}"
                href="/produk/{{ Str::slug($product->nama_unit) }}" />
        @endforeach
    </div>
</section>
