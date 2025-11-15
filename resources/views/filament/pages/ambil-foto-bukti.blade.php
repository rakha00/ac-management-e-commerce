<x-filament-panels::page>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <div class="max-w-3xl mx-auto px-3 sm:px-0">
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <!-- Card Header -->
            <div class="p-4 border-b border-gray-100 sm:p-5 sm:px-6">
                <div class="flex items-center justify-between gap-4 flex-col sm:flex-row sm:gap-14">
                    <div class="w-full sm:w-auto">
                        <h2 class="text-lg font-semibold tracking-tight text-gray-800">
                            Ambil Foto Bukti Kehadiran
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Posisikan wajah Anda di dalam bingkai dan ambil foto
                        </p>
                    </div>

                    <span id="camera-status"
                        class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium border bg-yellow-50 text-yellow-800 border-yellow-200">
                        Menunggu izin kamera
                    </span>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-6 px-4 sm:p-8 sm:px-6">
                <div class="flex flex-col items-center gap-4">
                    <!-- Selfie Video/Canvas -->
                    <div class="w-full max-w-lg">
                        <video id="selfie-video"
                            class="w-full max-w-lg aspect-square rounded-lg block mx-auto bg-gray-50 object-cover"
                            autoplay playsinline></video>
                        <canvas id="selfie-canvas" width="800" height="800"
                            class="w-full max-w-lg aspect-square rounded-lg block mx-auto bg-gray-50 hidden object-cover"></canvas>
                    </div>

                    <!-- Camera Controls -->
                    <div class="w-full max-w-xl grid grid-cols-1 gap-3 sm:grid-cols-[2fr_1fr]">
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

                    <!-- Selfie Controls -->
                    <div class="flex justify-center gap-3 mt-4 flex-wrap">
                        <button id="capture-btn"
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-150">
                            Ambil Foto
                        </button>
                        <button id="retake-btn"
                            class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-medium py-2 px-4 rounded-md transition-colors duration-150 hidden">
                            Ambil Ulang
                        </button>
                        <button id="confirm-btn"
                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-150 hidden">
                            Konfirmasi & Kirim
                        </button>
                    </div>

                    <!-- Selfie Preview -->
                    <div class="mt-4 text-center">
                        <img id="selfie-preview" class="max-w-full max-h-[300px] rounded-lg shadow-md hidden"
                            alt="Preview foto bukti kehadiran">
                    </div>

                    <!-- Camera Notes -->
                    <div class="text-center flex flex-col gap-1">
                        <p class="text-xs text-gray-600 m-0">
                            Jika kamera tidak muncul, pastikan Anda memberi izin akses kamera pada browser.
                        </p>
                        <p class="text-xs text-gray-500 m-0">
                            Disarankan menggunakan kamera depan (user) untuk foto selfie.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="p-3 px-4 bg-gray-50 rounded-b-lg sm:p-4 sm:px-6">
                <div class="flex items-center justify-between text-xs text-gray-600">
                    <span>Privasi: video tidak disimpan, hanya diproses di perangkat untuk pengambilan foto.</span>
                    <button type="button" id="reload-page"
                        class="hidden sm:inline-flex items-center gap-1 rounded-md bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 cursor-pointer transition-colors duration-150">
                        Muat Ulang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Form -->
    <form id="absen-form" method="POST" action="{{ route('absensi.submit.with.photo') }}" class="hidden"
        enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="token" id="token" value="{{ request()->query('token', $token ?? '') }}">
    </form>

    <!-- Modal Triggers -->
    <button id="open-success-modal" type="button" onclick="openModal('scan-success')" class="hidden">Open</button>
    <button id="open-duplicate-modal" type="button" onclick="openModal('scan-duplicate')" class="hidden">Open</button>

    <!-- Success Modal -->
    <x-filament::modal id="scan-success" icon="heroicon-o-check-circle" icon-color="success" :close-by-clicking-away="false">
        <x-slot name="heading">
            Absen Berhasil
        </x-slot>

        <p class="text-sm text-gray-600">
            Absensi berhasil disimpan. Tekan "OK" untuk menuju halaman Riwayat Absensi.
        </p>

        <x-slot name="footer">
            <x-filament::button x-on:click="window.location.href='/admin/riwayat-absensi'">
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
            <x-filament::button x-on:click="window.location.href='/admin/riwayat-absensi'">
                OK
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const token = document.getElementById('token').value;

            if (!token) {
                alert('Token tidak ditemukan. Silakan kembali ke halaman scan absensi.');
                window.location.href = '{{ route('absensi.scan.page') }}';
                return;
            }

            const video = document.getElementById('selfie-video');
            const canvas = document.getElementById('selfie-canvas');
            const captureBtn = document.getElementById('capture-btn');
            const retakeBtn = document.getElementById('retake-btn');
            const confirmBtn = document.getElementById('confirm-btn');
            const preview = document.getElementById('selfie-preview');
            const form = document.getElementById('absen-form');
            const cameraSelect = document.getElementById('camera-select');
            const restartBtn = document.getElementById('restart-btn');
            const reloadBtn = document.getElementById('reload-page');
            const statusEl = document.getElementById('camera-status');

            let stream = null;
            let currentCameraId = null;
            let availableCameras = [];

            const setStatus = (text, tone = 'info') => {
                statusEl.textContent = text;
                const toneClasses = {
                    'info': 'bg-blue-50 text-blue-800 border-blue-200',
                    'warn': 'bg-yellow-50 text-yellow-800 border-yellow-200',
                    'ok': 'bg-green-50 text-green-800 border-green-20',
                    'err': 'bg-red-50 text-red-800 border-red-200'
                };
                const baseClasses = 'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium border';
                statusEl.className = `${baseClasses} ${toneClasses[tone] || toneClasses['info']}`;
            };

            const stopCamera = async () => {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
            };

            const initCamera = async (cameraId = null) => {
                try {
                    await stopCamera();

                    const constraints = {
                        video: {
                            width: {
                                ideal: 800,
                                max: 800
                            },
                            height: {
                                ideal: 800,
                                max: 800
                            }
                        },
                        audio: false
                    };

                    if (cameraId) {
                        constraints.video.deviceId = {
                            exact: cameraId
                        };
                    } else {
                        constraints.video.facingMode = 'user';
                    }

                    stream = await navigator.mediaDevices.getUserMedia(constraints);
                    video.srcObject = stream;

                    return new Promise((resolve) => {
                        video.onloadedmetadata = () => resolve();
                    });
                } catch (err) {
                    console.error('Gagal mengakses kamera: ', err);
                    setStatus('Gagal mengakses kamera: ' + err.message, 'err');
                    alert('Gagal mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
                }
            };

            const populateCameras = (cameras) => {
                cameraSelect.innerHTML = '';
                cameras.forEach((cam, idx) => {
                    const opt = document.createElement('option');
                    opt.value = cam.deviceId;
                    opt.textContent = cam.label || `Kamera ${idx + 1}`;
                    cameraSelect.appendChild(opt);
                });
                cameraSelect.hidden = cameras.length <= 1;

                // Set default camera based on preference
                const userCam = cameras.find(c => /front|user/i.test(c.label || ''));
                const defaultCam = userCam || cameras[0];

                if (defaultCam) {
                    cameraSelect.value = defaultCam.deviceId;
                    currentCameraId = defaultCam.deviceId;
                }
            };

            const initCameraSelection = async () => {
                setStatus('Memuat daftar kamera...', 'warn');

                try {
                    availableCameras = await navigator.mediaDevices.enumerateDevices();
                    const videoCameras = availableCameras.filter(device => device.kind === 'videoinput');

                    if (!videoCameras || videoCameras.length === 0) {
                        setStatus('Tidak ada kamera terdeteksi', 'err');
                        return;
                    }

                    populateCameras(videoCameras);

                    // Initialize with default camera
                    await initCamera(currentCameraId);
                    setStatus('Kamera aktif', 'ok');

                } catch (err) {
                    console.error('Error initializing camera selection:', err);
                    setStatus('Gagal memuat daftar kamera: ' + err.message, 'err');
                    // Fallback to default initialization
                    await initCamera();
                    setStatus('Kamera aktif', 'ok');
                }
            };

            // Capture selfie
            const captureSelfie = () => {
                const context = canvas.getContext('2d');

                // Set square canvas size
                const squareSize = 800;
                canvas.width = squareSize;
                canvas.height = squareSize;

                // Calculate cropping to make it square
                const videoWidth = video.videoWidth;
                const videoHeight = video.videoHeight;
                const videoAspect = videoWidth / videoHeight;

                let sourceX = 0;
                let sourceY = 0;
                let sourceWidth = videoWidth;
                let sourceHeight = videoHeight;

                // Crop to square based on the smaller dimension
                if (videoAspect > 1) {
                    // Video is wider than tall - crop width
                    sourceWidth = videoHeight;
                    sourceX = (videoWidth - sourceWidth) / 2;
                } else {
                    // Video is taller than wide - crop height
                    sourceHeight = videoWidth;
                    sourceY = (videoHeight - sourceHeight) / 2;
                }

                // Draw the cropped square image
                context.drawImage(video, sourceX, sourceY, sourceWidth, sourceHeight, 0, 0, squareSize,
                    squareSize);

                const imageData = canvas.toDataURL('image/jpeg');
                preview.src = imageData;
                preview.classList.remove('hidden');

                // Stop video stream
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }

                // Hide video and show retake/confirm buttons
                video.classList.add('hidden');
                captureBtn.classList.add('hidden');
                retakeBtn.classList.remove('hidden');
                confirmBtn.classList.remove('hidden');
            };

            // Retake selfie
            const retakeSelfie = () => {
                video.classList.remove('hidden');
                preview.classList.add('hidden');
                captureBtn.classList.remove('hidden');
                retakeBtn.classList.add('hidden');
                confirmBtn.classList.add('hidden');

                // Restart camera
                initCamera(currentCameraId);
            };

            // Confirm and submit
            const confirmAndSubmit = async () => {
                setTimeout(() => {
                    canvas.toBlob(async (blob) => {
                        const formData = new FormData(form);
                        const file = new File([blob], 'selfie_' + new Date().getTime() +
                            '.jpg', {
                                type: 'image/jpeg'
                            });
                        formData.append('foto_bukti', file);

                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                            });

                            if (response.ok) {
                                const data = await response.json();
                                if (data.ok) {
                                    if (data.created) {
                                        document.getElementById('open-success-modal')
                                            ?.click();
                                    } else if (data.updated) {
                                        // Jika foto diperbarui, tampilkan pesan sukses
                                        document.getElementById('open-success-modal')
                                            ?.click();
                                    } else {
                                        document.getElementById('open-duplicate-modal')
                                            ?.click();
                                    }
                                } else {
                                    alert('Gagal menyimpan absensi: ' + (data.message ||
                                        'Data tidak valid'));
                                }
                            } else {
                                const errorData = await response.json();
                                alert('Gagal menyimpan absensi: ' + (errorData
                                    .message || 'Server error'));
                            }
                        } catch (err) {
                            console.error('Error submitting form:', err);
                            alert('Gagal menyimpan absensi: ' + err.message);
                        }
                    }, 'image/jpeg', 0.8);
                }, 100);
            };

            // Event listeners
            captureBtn.addEventListener('click', captureSelfie);
            retakeBtn.addEventListener('click', retakeSelfie);
            confirmBtn.addEventListener('click', confirmAndSubmit);

            cameraSelect.addEventListener('change', async (e) => {
                const newId = e.target.value;
                if (newId && newId !== currentCameraId) {
                    setStatus('Beralih kamera...', 'warn');
                    currentCameraId = newId;
                    await initCamera(newId);
                    setStatus('Kamera aktif', 'ok');
                }
            });

            restartBtn.addEventListener('click', async () => {
                setStatus('Restart kamera...', 'warn');
                await initCamera(currentCameraId);
                setStatus('Kamera aktif', 'ok');
            });

            reloadBtn?.addEventListener('click', () => window.location.reload());

            // Initialize camera selection when page loads
            initCameraSelection();

            // Request camera permissions to enumerate devices
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({
                        video: true,
                        audio: false
                    })
                    .then(stream => {
                        stream.getTracks().forEach(track => track.stop());
                        // After getting permission, initialize camera selection
                        initCameraSelection();
                    })
                    .catch(err => {
                        console.error('Permission error:', err);
                        setStatus('Izin kamera ditolak', 'err');
                        alert(
                            'Izin kamera ditolak. Silakan berikan izin akses kamera untuk menggunakan fitur ini.'
                        );
                    });
            } else {
                setStatus('API MediaDevices tidak didukung', 'err');
                alert('Browser Anda tidak mendukung fitur kamera. Silakan gunakan browser modern.');
            }
        });

        // Modal opener helper
        function openModal(id) {
            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: {
                    id
                }
            }));
        }
    </script>
</x-filament-panels::page>
