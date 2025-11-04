<x-filament-panels::page>
    @if ($this->sudahAbsenHariIni)
        <div class="alert alert-success">
            Anda sudah absen hari ini.
        </div>
    @else
        <div class="alert alert-error">
            Anda belum absen hari ini.
        </div>
    @endif

    {{ $this->table }}

    <style>
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        body.dark .alert-success {
            background-color: rgba(6, 95, 70, 0.2);
            color: #6ee7b7;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
        }

        body.dark .alert-error {
            background-color: rgba(153, 27, 27, 0.2);
            color: #fca5a5;
        }
    </style>
</x-filament-panels::page>