<div class="bg-white p-4 rounded-lg shadow-sm mt-6" x-data="{
    /* Ganti '628123456789' dengan nomor WA admin servis Anda */
    nomorWa: '628123456789',
    nama: '',
    alamat: '',
    pesan: '',

    /* Fungsi untuk generate & buka link WA */
    orderServis() {
        if (!this.nama || !this.alamat) {
            alert('Mohon isi Nama dan Alamat Lengkap Anda.');
            return;
        }

        let templatePesan = `--- ORDER SERVIS AC ---

                        Nama Lengkap: ${this.nama}
                        Alamat Lengkap: ${this.alamat}

                        Keluhan / Pesan:
                        ${this.pesan || '(Tidak ada keluhan spesifik, mohon dijadwalkan inspeksi)'}

                        Saya ingin memesan servis. Mohon konfirmasi jadwal. Terima kasih.
                        `;
        /* Encode pesan untuk URL */
        let urlWa = `https://wa.me/${this.nomorWa}?text=${encodeURIComponent(templatePesan)}`;

        /* Buka di tab baru */
        window.open(urlWa, '_blank');
    }
}">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">
        Pesan Servis Cepat
    </h3>

    <form @submit.prevent="orderServis" class="space-y-4">

        <div>
            <label for="nama_servis_sidebar" class="block text-sm font-medium text-gray-700 mb-1">Nama Anda</label>
            <input type="text" id="nama_servis_sidebar" x-model="nama" placeholder="Tulis nama lengkap..."
                class="w-full px-3 py-2 text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gsi-red/50 focus:border-gsi-red"
                required>
        </div>

        <div>
            <label for="alamat_servis_sidebar" class="block text-sm font-medium text-gray-700 mb-1">Alamat
                Lengkap</label>
            <textarea id="alamat_servis_sidebar" x-model="alamat" rows="2" placeholder="Tulis alamat pemasangan/servis..."
                class="w-full px-3 py-2 text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gsi-red/50 focus:border-gsi-red"
                required></textarea>
        </div>

        <div>
            <label for="pesan_servis_sidebar" class="block text-sm font-medium text-gray-700 mb-1">Pesan /
                Keluhan</label>
            <textarea id="pesan_servis_sidebar" x-model="pesan" rows="3"
                placeholder="Contoh: AC tidak dingin, bocor air, dll."
                class="w-full px-3 py-2 text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gsi-red/50 focus:border-gsi-red"></textarea>
        </div>

        <div>
            <button type="submit"
                class="w-full px-5 py-3 bg-gsi-red text-white rounded-lg shadow-md hover:bg-red-700 transition-colors font-semibold text-sm flex items-center justify-center">
                <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 mr-2" />
                <span>Order Servis via WA</span>
            </button>
        </div>

    </form>
</div>
