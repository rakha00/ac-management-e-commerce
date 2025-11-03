@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-4xl font-bold text-gray-800 mb-6 text-center">Tentang Kami</h1>

        <p class="text-gray-700 leading-relaxed mb-6">
            Selamat datang di toko AC dan Sparepart kami! Kami berkomitmen untuk menyediakan solusi pendingin ruangan terbaik dan suku cadang berkualitas tinggi untuk memenuhi kebutuhan rumah tangga dan bisnis Anda. Dengan pengalaman bertahun-tahun di industri ini, kami bangga menawarkan produk-produk terkemuka dengan harga yang kompetitif dan layanan pelanggan yang luar biasa.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Visi Kami</h2>
        <p class="text-gray-700 leading-relaxed mb-6">
            Menjadi penyedia AC dan sparepart terdepan yang dipercaya oleh pelanggan atas kualitas produk, inovasi, dan pelayanan purna jual yang responsif.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Misi Kami</h2>
        <ul class="list-disc list-inside text-gray-700 leading-relaxed mb-6">
            <li>Menyediakan beragam pilihan produk AC hemat energi dan ramah lingkungan.</li>
            <li>Menjamin ketersediaan suku cadang asli dan berkualitas untuk semua jenis AC.</li>
            <li>Memberikan pelayanan konsultasi dan purna jual yang profesional dan memuaskan.</li>
            <li>Membangun hubungan jangka panjang dengan pelanggan berdasarkan kepercayaan dan kepuasan.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Hubungi Kami</h2>
        <p class="text-gray-700 leading-relaxed mb-6">
            Jika Anda memiliki pertanyaan, membutuhkan bantuan, atau ingin melakukan pemesanan, jangan ragu untuk menghubungi kami melalui WhatsApp. Tim kami siap membantu Anda!
        </p>

        <div class="text-center">
            <a href="https://wa.me/6281234567890?text=Halo,%20saya%20ingin%20bertanya%20tentang%20produk%20AC%20atau%20sparepart." class="inline-block bg-green-500 text-white px-8 py-4 rounded-lg text-xl font-semibold hover:bg-green-600 transition-colors duration-300">
                <x-icon-whatsapp class="w-5 h-5 mr-2"></x-icon-whatsapp> Chat via WhatsApp
            </a>
        </div>
    </div>
</div>
@endsection