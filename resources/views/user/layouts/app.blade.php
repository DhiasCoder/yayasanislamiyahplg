<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'PPDB - Sistem Informasi Sekolah' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>


    @livewireStyles
</head>

<body class="min-h-screen bg-gray-50 text-gray-800 antialiased">

    @include('user.layouts.header')

    <main class="min-h-[calc(100vh-140px)]">
        {{ $slot }}
    </main>

    @include('user.layouts.footer')

    @livewireScripts
</body>

</html>