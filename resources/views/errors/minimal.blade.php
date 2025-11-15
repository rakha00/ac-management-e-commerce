<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') | {{ config('app.name', 'Laravel') }}</title>

    <style>
        /* === Reset & Base === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            font-family: "Inter", "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f6f8;
            /* lembut, tidak silau */
            color: #2f3640;
            /* abu kehitaman yang lembut */
            -webkit-font-smoothing: antialiased;
        }

        /* === Layout === */
        .error-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
            padding: 2rem;
        }

        .error-card {
            background-color: #ffffff;
            border: 1px solid #e3e6ea;
            border-radius: 1rem;
            padding: 3rem 2.5rem;
            max-width: 460px;
            width: 100%;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .error-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.06);
        }

        /* === Logo === */
        .logo-container {
            margin-bottom: 2rem;
        }

        .logo-container img {
            max-width: 130px;
            opacity: 0.9;
            height: auto;
        }

        /* === Error Info === */
        .error-code {
            font-size: 3.5rem;
            font-weight: 700;
            color: #d9534f;
            /* merah lembut, bukan merah tajam */
            margin-bottom: 0.5rem;
        }

        .error-message {
            font-size: 1.25rem;
            font-weight: 600;
            color: #495057;
            /* abu gelap nyaman di mata */
            margin-bottom: 2rem;
        }

        /* === Action Button === */
        .btn {
            display: inline-block;
            background-color: #1f2937;
            /* biru pastel lembut */
            color: #ffffff;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.95rem;
            transition: background-color 0.25s ease, box-shadow 0.25s ease;
        }

        .btn:hover {
            background-color: #405370;
            box-shadow: 0 2px 10px rgba(92, 124, 250, 0.25);
        }

        /* === Footer === */
        .error-footer {
            margin-top: 1.75rem;
            font-size: 0.85rem;
            color: #868e96;
        }

        /* === Responsive === */
        @media (max-width: 640px) {
            .error-card {
                padding: 2rem 1.5rem;
            }

            .error-code {
                font-size: 2.8rem;
            }

            .error-message {
                font-size: 1.1rem;
            }

            .logo-container img {
                max-width: 100px;
            }
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-card">
            <div class="logo-container">
                <img src="{{ asset('img/GSI-landscape.png') }}" alt="GSI Logo">
            </div>

            <div class="error-code">@yield('code')</div>
            <div class="error-message">@yield('message')</div>

            <a href="{{ url('/') }}" class="btn">Kembali ke Beranda</a>

            <div class="error-footer">
                © {{ date('Y') }} {{ config('app.name', 'GSI') }}. Semua hak dilindungi.
            </div>
        </div>
    </div>
</body>

</html>
