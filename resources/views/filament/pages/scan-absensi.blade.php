<x-filament-panels::page>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <div class="max-w-3xl mx-auto px-3 sm:px-0">
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <!-- Card Header -->
            <div class="p-4 border-b border-gray-100 sm:p-5 sm:px-6">
                <div class="flex items-center justify-between gap-4 flex-col sm:flex-row sm:gap-14">
                    <div class="w-full sm:w-auto">
                        <h2 class="text-lg font-semibold tracking-tight text-gray-800">
                            Scan Absensi
                        </h2>
                        <p class="mt-1 text-sm text-gray-600" id="instruction-text">
                            <span id="camera-instruction">Arahkan kamera ke QR pada halaman "QR Absensi Harian".</span>
                            <span id="file-instruction" class="hidden">Upload file gambar QR Code atau drag & drop
                                untuk memindai QR Code.</span>
                        </p>
                    </div>

                    <span id="scan-status"
                        class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium border bg-yellow-50 text-yellow-800 border-yellow-200">
                        Menunggu izin kamera
                    </span>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="px-6 pt-4 border-b border-gray-100">
                <nav class="flex space-x-8" aria-label="Tabs">
                    <button id="tab-camera"
                        class="tab-button py-2 px-1 border-b-2 border-blue-500 text-sm font-medium text-blue-600">
                        Scan dengan Kamera
                    </button>
                    <button id="tab-files"
                        class="tab-button py-2 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Scan dari File/Folder
                    </button>
                </nav>
            </div>

            <!-- Card Body -->
            <div class="p-6 px-4 sm:p-8 sm:px-6">
                <div class="flex flex-col items-center gap-4">
                    <!-- Camera Scanner -->
                    <div id="camera-scanner" class="w-full max-w-[560px]">
                        <div id="reader"
                            class="w-full aspect-square rounded-lg border border-gray-200 shadow-sm bg-gray-50 overflow-hidden">
                        </div>
                    </div>

                    <!-- File Upload Scanner -->
                    <div id="file-scanner" class="w-full max-w-[560px] hidden">
                        <div id="drop-zone"
                            class="w-full aspect-square rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 flex flex-col items-center justify-center p-8 text-center hover:bg-gray-100 transition-colors cursor-pointer">
                            <div class="text-gray-400 mb-4">
                                <svg class="mx-auto h-12 w-12" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="text-sm text-gray-600 mb-2">
                                <span class="font-medium text-blue-600 hover:text-blue-500">Klik untuk upload
                                    file</span> atau drag & drop
                            </div>
                            <p class="text-xs text-gray-500 mb-4">
                                QR Code PNG, JPG, atau GIF
                            </p>
                            <input type="file" id="file-input" accept="image/*" class="hidden">
                            <button id="select-files-btn" type="button"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Pilih File
                            </button>
                        </div>

                        <!-- Processing Status -->
                        <div id="file-status" class="mt-4 hidden">
                            <div class="flex items-center justify-between text-sm">
                                <span id="processing-info" class="text-gray-600">Memproses 0/0 file...</span>
                                <span id="progress-percent" class="text-gray-500">0%</span>
                            </div>
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                <div id="progress-bar" class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Results Table -->
                        <div id="results-section" class="mt-6 hidden">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Hasil Scan</h3>
                            <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                File</th>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="results-table-body" class="bg-white divide-y divide-gray-200">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Camera Controls -->
                    <div class="w-full max-w-[560px] grid grid-cols-1 gap-3 sm:grid-cols-[2fr_1fr]">
                        <div class="w-full">
                            <label for="camera-select" class="block text-xs font-medium text-gray-700 mb-1">
                                Pilih Kamera
                            </label>
                            <select id="camera-select"
                                class="w-full rounded-md border border-gray-300 bg-white text-sm text-gray-700 shadow-sm p-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                hidden></select>
                        </div>

                        <div class="flex items-end">
                            <button id="restart-btn" type="button"
                                class="w-full inline-flex items-center justify-center gap-1 rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 cursor-pointer transition-colors duration-150">
                                Restart
                            </button>
                        </div>
                    </div>

                    <!-- Scanner Notes -->
                    <div class="text-center flex flex-col gap-1">
                        <p class="text-xs text-gray-600 m-0">
                            Jika kamera tidak muncul, pastikan Anda memberi izin akses kamera pada browser.
                        </p>
                        <p class="text-xs text-gray-500 m-0">
                            Disarankan menggunakan kamera belakang (environment) di perangkat mobile.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="p-3 px-4 bg-gray-50 rounded-b-lg sm:p-4 sm:px-6">
                <div class="flex items-center justify-between text-xs text-gray-600">
                    <span>Privasi: video tidak disimpan, hanya diproses di perangkat untuk membaca QR.</span>
                    <button type="button" id="reload-page"
                        class="hidden sm:inline-flex items-center gap-1 rounded-md bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 cursor-pointer transition-colors duration-150">
                        Muat Ulang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Triggers -->
    <button id="open-too-early-modal" type="button" x-data="{}"
        x-on:click="$dispatch('open-modal', { id: 'scan-too-early' })" class="hidden">Open</button>
    <button id="open-success-modal" type="button" x-data="{}"
        x-on:click="$dispatch('open-modal', { id: 'scan-success' })" class="hidden">Open</button>
    <button id="open-duplicate-modal" type="button" x-data="{}"
        x-on:click="$dispatch('open-modal', { id: 'scan-duplicate' })" class="hidden">Open</button>
    <button id="open-failed-modal" type="button" x-data="{}"
        x-on:click="$dispatch('open-modal', { id: 'scan-failed' })" class="hidden">Open</button>

    <!-- Too Early Modal -->
    <x-filament::modal id="scan-too-early" icon="heroicon-o-clock" icon-color="warning" :close-by-clicking-away="true">
        <x-slot name="heading">
            Belum Waktunya Absen
        </x-slot>

        <p class="text-sm text-gray-600" id="too-early-message">
            Absensi dapat dilakukan mulai jam yang ditentukan.
        </p>

        <x-slot name="footer">
            <x-filament::button x-on:click="$dispatch('close-modal', { id: 'scan-too-early' })">
                OK
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    <!-- Success Modal -->
    <x-filament::modal id="scan-success" icon="heroicon-o-check-circle" icon-color="success" :close-by-clicking-away="false">
        <x-slot name="heading">
            Absen Berhasil
        </x-slot>

        <p class="text-sm text-gray-600">
            Absensi berhasil disimpan. Tekan "OK" untuk menuju halaman Riwayat Absensi.
        </p>

        <x-slot name="footer">
            <x-filament::button x-on:click="window.location.href='{{ \App\Filament\Pages\RiwayatAbsensi::getUrl() }}'">
                OK
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    <!-- Duplicate Modal -->
    <x-filament::modal id="scan-duplicate" icon="heroicon-o-exclamation-triangle" icon-color="warning"
        :close-by-clicking-away="false">
        <x-slot name="heading">
            Sudah Absen
        </x-slot>

        <p class="text-sm text-gray-600">
            Anda sudah melakukan absensi hari ini. Tekan "OK" untuk menuju halaman Riwayat Absensi.
        </p>

        <x-slot name="footer">
            <x-filament::button x-on:click="window.location.href='{{ \App\Filament\Pages\RiwayatAbsensi::getUrl() }}'">
                OK
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    <!-- Failed Modal -->
    <x-filament::modal id="scan-failed" icon="heroicon-o-x-circle" icon-color="danger" :close-by-clicking-away="false">
        <x-slot name="heading">
            Gagal Absen
        </x-slot>

        <p class="text-sm text-gray-600">
            QR Code tidak valid atau tidak sesuai dengan token hari ini. Silakan coba kembali dengan QR Code yang benar.
        </p>

        <x-slot name="footer">
            <x-filament::button x-on:click="$dispatch('close-modal', { id: 'scan-failed' })">
                OK
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    <style>
        #reader {
            aspect-ratio: 1 / 1;
            display: block;
        }

        #reader video,
        #reader canvas {
            width: 100% !important;
            height: 100% !important;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            display: block;
        }
    </style>

    <script src="https://unpkg.com/html5-qrcode" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const statusEl = document.getElementById('scan-status');
            const cameraSelect = document.getElementById('camera-select');
            const restartBtn = document.getElementById('restart-btn');
            const reloadBtn = document.getElementById('reload-page');

            // Tab elements
            const tabCamera = document.getElementById('tab-camera');
            const tabFiles = document.getElementById('tab-files');
            const cameraScanner = document.getElementById('camera-scanner');
            const fileScanner = document.getElementById('file-scanner');
            const cameraInstruction = document.getElementById('camera-instruction');
            const fileInstruction = document.getElementById('file-instruction');

            // File upload elements
            const fileInput = document.getElementById('file-input');
            const selectFilesBtn = document.getElementById('select-files-btn');
            const dropZone = document.getElementById('drop-zone');
            const fileStatus = document.getElementById('file-status');
            const processingInfo = document.getElementById('processing-info');
            const progressPercent = document.getElementById('progress-percent');
            const progressBar = document.getElementById('progress-bar');
            const resultsSection = document.getElementById('results-section');
            const resultsTableBody = document.getElementById('results-table-body');

            let html5QrCode = null;
            let currentCameraId = null;
            let isRunning = false;
            let availableCameras = [];

            const setStatus = (text, tone = 'info') => {
                statusEl.textContent = text;
                const toneClasses = {
                    'info': 'bg-blue-50 text-blue-800 border-blue-200',
                    'warn': 'bg-yellow-50 text-yellow-800 border-yellow-200',
                    'ok': 'bg-green-50 text-green-800 border-green-200',
                    'err': 'bg-red-50 text-red-800 border-red-200'
                };
                const baseClasses = 'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium border';
                statusEl.className = `${baseClasses} ${toneClasses[tone] || toneClasses['info']}`;
            };

            // Tab switching functionality
            const switchTab = (tab) => {
                if (tab === 'camera') {
                    tabCamera.classList.add('border-blue-500', 'text-blue-600');
                    tabCamera.classList.remove('border-transparent', 'text-gray-500');
                    tabFiles.classList.add('border-transparent', 'text-gray-500');
                    tabFiles.classList.remove('border-blue-500', 'text-blue-600');

                    cameraScanner.classList.remove('hidden');
                    fileScanner.classList.add('hidden');
                    cameraInstruction.classList.remove('hidden');
                    fileInstruction.classList.add('hidden');

                    // Hide file processing UI if visible
                    hideFileProcessingUI();
                } else {
                    tabFiles.classList.add('border-blue-500', 'text-blue-600');
                    tabFiles.classList.remove('border-transparent', 'text-gray-500');
                    tabCamera.classList.add('border-transparent', 'text-gray-500');
                    tabCamera.classList.remove('border-blue-500', 'text-blue-600');

                    fileScanner.classList.remove('hidden');
                    cameraScanner.classList.add('hidden');
                    fileInstruction.classList.remove('hidden');
                    cameraInstruction.classList.add('hidden');

                    // Stop camera if running
                    if (html5QrCode && isRunning) {
                        stopScanner();
                    }
                }
            };

            // File upload functionality
            const handleFiles = (files) => {
                if (files.length === 0) return;

                const file = files[0];
                if (!file.type.startsWith('image/')) {
                    alert('File harus berupa gambar (PNG, JPG, GIF).');
                    return;
                }

                resultsTableBody.innerHTML = '';
                resultsSection.classList.add('hidden');

                // Show processing UI
                showFileProcessingUI();

                // Process single file
                processSingleFile(file);
            };

            const showFileProcessingUI = () => {
                fileStatus.classList.remove('hidden');
                setFileProgress(1, 0, 'Memproses file...');
            };

            const hideFileProcessingUI = () => {
                fileStatus.classList.add('hidden');
            };

            const setFileProgress = (total, current, status = 'Memproses...') => {
                processingInfo.textContent = status;
                const percent = Math.round((current / total) * 100);
                progressPercent.textContent = `${percent}%`;
                progressBar.style.width = `${percent}%`;
            };

            const addResultRow = (fileName, status, token, hasError = false, errorMessage = '') => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-3 py-3 text-sm text-gray-900">${fileName}</td>
                    <td class="px-3 py-3">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${hasError ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}">
                            ${status}
                        </span>
                    </td>
                    <td class="px-3 py-3 text-sm text-gray-500">
                        ${hasError && errorMessage ? `<span class="text-red-600 text-xs">${errorMessage}</span>` :
                          token ? `<button onclick="processToken('${token}')" class="text-blue-600 hover:text-blue-900 text-xs underline">Proses Token</button>` :
                          '-'}
                    </td>
                `;
                resultsTableBody.appendChild(row);
            };

            const processSingleFile = async (file) => {
                setFileProgress(1, 1, `Memindai ${file.name}...`);

                try {
                    const qrText = await scanQRFromImage(file);
                    addResultRow(file.name, 'QR Terdeteksi', qrText);

                    if (qrText) {
                        // Auto-process the valid QR code
                        processToken(qrText);
                    }
                } catch (error) {
                    addResultRow(file.name, 'QR Tidak Ditemukan', '', true, error.message);
                }

                hideFileProcessingUI();
                resultsSection.classList.remove('hidden');
            };

            const scanQRFromImage = (file) => {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            try {
                                // Create canvas for QR code detection
                                const canvas = document.createElement('canvas');
                                const ctx = canvas.getContext('2d');
                                canvas.width = img.width;
                                canvas.height = img.height;
                                ctx.drawImage(img, 0, 0);

                                // Get image data from canvas
                                const imageData = ctx.getImageData(0, 0, canvas.width, canvas
                                    .height);

                                // Use jsQR to detect QR code
                                if (typeof jsQR === 'undefined') {
                                    reject(new Error('jsQR library tidak tersedia'));
                                    return;
                                }

                                const code = jsQR(imageData.data, imageData.width, imageData
                                    .height, {
                                        inversionAttempts: "dontInvert",
                                    });

                                if (code) {
                                    resolve(code.data);
                                } else {
                                    reject(new Error(
                                        'QR Code tidak ditemukan dalam gambar ini'));
                                }
                            } catch (error) {
                                reject(new Error('Error memproses gambar: ' + error.message));
                            }
                        };
                        img.onerror = () => reject(new Error('Gagal memuat gambar'));
                        img.src = e.target.result;
                    };
                    reader.onerror = () => reject(new Error('Gagal membaca file'));
                    reader.readAsDataURL(file);
                });
            };

            // Global function to process token from results
            window.processToken = (token) => {
                // Switch to camera tab and show success
                switchTab('camera');
                submitToken(token);
            };

            // Event Listeners
            tabCamera.addEventListener('click', () => switchTab('camera'));
            tabFiles.addEventListener('click', () => switchTab('files'));

            // File input events
            selectFilesBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                fileInput.click();
            });
            fileInput.addEventListener('change', (e) => handleFiles(e.target.files));

            // Drag and drop events
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-blue-500', 'bg-blue-50');
            });

            dropZone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-blue-500', 'bg-blue-50');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-blue-500', 'bg-blue-50');
                const files = e.dataTransfer.files;
                handleFiles(files);
            });

            // Only trigger file input when clicking on drop zone, not on the button
            dropZone.addEventListener('click', (e) => {
                // Don't trigger if clicking on the button itself
                if (e.target === selectFilesBtn || selectFilesBtn.contains(e.target)) {
                    return;
                }
                fileInput.click();
            });

            const stopScanner = async () => {
                if (html5QrCode) {
                    try {
                        await html5QrCode.stop();
                    } catch (_) {}
                    try {
                        await html5QrCode.clear();
                    } catch (_) {}
                }
                isRunning = false;
            };

            const submitToken = (token) => {
                setStatus('Memvalidasi token...', 'info');

                fetch('{{ route('absensi.validate.token') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                                '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            token: token
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.message || 'Server error');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.ok) {
                            if (data.requires_photo) {
                                setStatus('Token valid, mengarahkan ke foto bukti...', 'ok');
                                window.location.href =
                                    `{{ route('absensi.foto-bukti') }}?token=${encodeURIComponent(token)}`;
                            } else if (data.created) {
                                document.getElementById('open-success-modal')?.click();
                            } else {
                                document.getElementById('open-duplicate-modal')?.click();
                            }
                        } else {
                            throw new Error(data.message || 'Token tidak valid');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        if (error.message.includes('Belum waktunya absen')) {
                            document.getElementById('too-early-message').textContent = error.message;
                            document.getElementById('open-too-early-modal')?.click();
                            setStatus('Belum waktunya absen', 'warn');
                        } else if (error.message.includes('sudah absen') || error.message.includes(
                                'Sudah Absen')) {
                            document.getElementById('open-duplicate-modal')?.click();
                        } else {
                            document.getElementById('open-failed-modal')?.click();
                            setStatus('Gagal: ' + error.message, 'err');
                        }

                        // Restart scanner after error
                        setTimeout(() => {
                            if (currentCameraId && !isRunning) {
                                startScanner(currentCameraId);
                            }
                        }, 2000);
                    });
            };

            const startScanner = async (cameraId) => {
                try {
                    await stopScanner();
                    html5QrCode = new Html5Qrcode('reader');

                    const config = {
                        fps: 10,
                        qrbox: (viewfinderWidth, viewfinderHeight) => {
                            const edge = Math.min(viewfinderWidth, viewfinderHeight);
                            const size = Math.min(340, Math.floor(edge * 0.8));
                            return {
                                width: size,
                                height: size
                            };
                        },
                    };

                    currentCameraId = cameraId;
                    setStatus('Memulai kamera…', 'info');

                    await html5QrCode.start({
                            deviceId: {
                                exact: cameraId
                            }
                        },
                        config,
                        (decodedText) => {
                            setStatus('QR terdeteksi, memproses...', 'ok');

                            // Stop scanner dulu
                            if (html5QrCode && isRunning) {
                                html5QrCode.stop().then(() => {
                                    isRunning = false;
                                    submitToken(decodedText);
                                }).catch(() => {
                                    isRunning = false;
                                    submitToken(decodedText);
                                });
                            } else {
                                submitToken(decodedText);
                            }
                        },
                        (_errorMessage) => {
                            /* Silent for UX */
                        }
                    );

                    isRunning = true;
                    setStatus('Kamera aktif — arahkan ke QR', 'ok');
                } catch (err) {
                    console.error('Error starting scanner:', err);
                    setStatus('Gagal memulai kamera: ' + err.message, 'err');
                }
            };

            const populateCameras = (cameras) => {
                cameraSelect.innerHTML = '';
                cameras.forEach((cam, idx) => {
                    const opt = document.createElement('option');
                    opt.value = cam.id;
                    opt.textContent = cam.label || `Kamera ${idx + 1}`;
                    cameraSelect.appendChild(opt);
                });
                cameraSelect.hidden = cameras.length <= 1;
            };

            const findDefaultCameraId = (cameras) => {
                const env = cameras.find(c => /back|rear|environment/i.test(c.label || ''));
                return (env || cameras[0])?.id ?? null;
            };

            const init = async () => {
                setStatus('Memuat daftar kamera…', 'warn');
                try {
                    availableCameras = await Html5Qrcode.getCameras();
                    if (!availableCameras || availableCameras.length === 0) {
                        setStatus('Tidak ada kamera terdeteksi', 'err');
                        return;
                    }

                    populateCameras(availableCameras);

                    const defId = findDefaultCameraId(availableCameras);
                    if (defId) {
                        cameraSelect.value = defId;
                        await startScanner(defId);
                    } else {
                        setStatus('Tidak menemukan kamera yang cocok', 'err');
                    }
                } catch (err) {
                    console.error('Error initializing:', err);
                    setStatus('Izin kamera ditolak atau tidak tersedia', 'err');
                }
            };

            cameraSelect.addEventListener('change', async (e) => {
                const newId = e.target.value;
                if (newId && newId !== currentCameraId) {
                    setStatus('Beralih kamera…', 'warn');
                    await startScanner(newId);
                }
            });

            restartBtn.addEventListener('click', async () => {
                if (currentCameraId) {
                    setStatus('Restart scanner…', 'warn');
                    await startScanner(currentCameraId);
                } else {
                    init();
                }
            });

            reloadBtn?.addEventListener('click', () => window.location.reload());

            init();
        });
    </script>
</x-filament-panels::page>
