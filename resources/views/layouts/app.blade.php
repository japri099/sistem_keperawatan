<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    @vite('resources/css/app.css')  {{-- Pastikan Tailwind terhubung --}}
</head>
<body class="bg-gray-100 text-gray-900">

    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a class="text-lg font-bold" href="#">Aplikasi</a>
            <a href="{{ route('logout') }}" class="bg-red-500 hover:bg-red-700 text-white py-2 px-4 rounded">Logout</a>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        @yield('content')
    </div>

</body>
</html>
