<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a class="text-lg font-bold" href="#">Aplikasi</a>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white py-2 px-4 rounded"> Logout
                </button>
            </form>
        </div>
    </nav>

    <div class="flex">
        @include('layouts.sidebar')
        <div class="flex-1 p-6">
            @yield('content')
        </div>
    </div>

</body>
</html>
