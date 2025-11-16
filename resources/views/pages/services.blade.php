@extends('layouts.app')

@section('title', 'Global Servis Int. - Pusat Dealer & Servis AC')

@section('content')
    <div class="container mx-auto px-4 py-6">

        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-4 md:mb-6">
                <h1 class="text-3xl lg:text-4xl font-semibold text-gray-900">
                    Pesan Jasa Servis AC
                </h1>
                <p class="mt-3 text-lg text-gray-600">
                    Isi form di bawah ini untuk menjadwalkan kunjungan teknisi kami.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-10">

                <div class="md:col-span-2 bg-white p-6 md:p-8 rounded-lg shadow-lg" x-data="{
                    /* Ganti '628123456789' dengan nomor WA admin servis Anda */
                    nomorWa: '6285156167795',
                    nama: '',
                    alamat: '',
                    pesan: '',
                
                    /* Fungsi untuk generate & buka link WA */
                    orderServis() {
                        if (!this.nama || !this.alamat || !this.pesan) {
                            alert('Mohon isi semua field: Nama, Alamat, dan Keluhan Anda.');
                            return;
                        }
                
let templatePesan = `--- ORDER SERVIS AC ---

Nama Lengkap: ${this.nama}
Alamat Lengkap: ${this.alamat}

Keluhan / Pesan:
${this.pesan}

Saya ingin memesan servis. Mohon konfirmasi jadwal. Terima kasih.`;
                        /* Encode pesan untuk URL */
                        let urlWa = `https://wa.me/${this.nomorWa}?text=${encodeURIComponent(templatePesan)}`;
                
                        /* Buka di tab baru */
                        window.open(urlWa, '_blank');
                    }
                }">
                    <form @submit.prevent="orderServis" class="space-y-5">

                        <div>
                            <label for="nama_servis" class="block text-sm font-semibold text-gray-700 mb-2">Nama
                                Lengkap</label>
                            <input type="text" id="nama_servis" x-model="nama" placeholder="Tulis nama lengkap Anda"
                                class="w-full px-4 py-3 text-base text-gray-700 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gsi-red/50 focus:border-gsi-red"
                                required>
                        </div>

                        <div>
                            <label for="alamat_servis" class="block text-sm font-semibold text-gray-700 mb-2">Alamat
                                Lengkap</label>
                            <textarea id="alamat_servis" x-model="alamat" rows="3"
                                placeholder="Tulis alamat lengkap untuk kunjungan teknisi (termasuk lokasi detail jika perlu)"
                                class="w-full px-4 py-3 text-base text-gray-700 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gsi-red/50 focus:border-gsi-red"
                                required></textarea>
                        </div>

                        <div>
                            <label for="pesan_servis" class="block text-sm font-semibold text-gray-700 mb-2">Keluhan /
                                Detail Servis</label>
                            <textarea id="pesan_servis" x-model="pesan" rows="5"
                                placeholder="Contoh: AC Daikin 1 PK tidak dingin. AC LG 2 PK bocor air."
                                class="w-full px-4 py-3 text-base text-gray-700 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gsi-red/50 focus:border-gsi-red"
                                required></textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="w-full px-8 py-4 bg-gsi-red text-white rounded-lg shadow-md hover:bg-red-700 transition-colors font-semibold text-base flex items-center justify-center">
                                <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 mr-2.5" />
                                <span>Kirim Pesanan Servis via WA</span>
                            </button>
                        </div>

                    </form>
                </div>
                <div class="md:col-span-1">
                    <div class="bg-white p-6 rounded-lg shadow-lg space-y-6">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                Jam Operasional
                            </h4>
                            <ul class="space-y-1.5 text-gray-600 text-sm">
                                <li class="flex justify-between">
                                    <span>Senin - Jumat:</span>
                                    <span class="font-medium text-gray-800">08:00 - 17:00 WIB</span>
                                </li>
                                <li class="flex justify-between">
                                    <span>Sabtu:</span>
                                    <span class="font-medium text-gray-800">08:00 - 14:00 WIB</span>
                                </li>
                                <li class="flex justify-between">
                                    <span>Minggu / Libur:</span>
                                    <span class="font-medium text-gsi-red">Tutup</span>
                                </li>
                            </ul>
                            <p class="text-xs text-gray-500 mt-3">
                                Pesanan servis di luar jam operasional akan kami proses di hari kerja berikutnya.
                            </p>
                        </div>

                        <hr class="border-gray-100">

                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                Kontak Admin
                            </h4>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-center">
                                    <x-heroicon-o-phone class="w-4 h-4 mr-2" />
                                    <span>(021) 1234 5678</span>
                                </li>
                                <li class="flex items-center">
                                    <x-heroicon-o-envelope class="w-4 h-4 mr-2" />
                                    <span>sales@gsi.co.id</span>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
