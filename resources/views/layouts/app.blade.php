<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Global Servis Int. - Pusat Dealer & Servis AC')</title>
    <link rel="icon" href="{{ asset('img/GSI.png') }}" type="image/png">

    @include('components.assets')
</head>

<body class="bg-gray-100 text-gray-800 antialiased">

    @include('components.header')

    <main>
        @yield('content')
    </main>

    @include('components.footer')

    @livewire('cart-handler')
    @livewireScripts
    @stack('scripts')
</body>

</html>
