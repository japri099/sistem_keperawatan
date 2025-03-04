<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="w-96 p-6 bg-white shadow-md rounded-md">
        <h2 class="text-2xl font-bold text-center mb-4">Login</h2>
        @if ($errors->any())
            <div class="text-red-500">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <input type="number" name="username" placeholder="Username" required class="w-full p-2 border rounded mb-2">
            <input type="password" name="password" placeholder="Password" required class="w-full p-2 border rounded mb-2">
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Login</button>
        </form>
        <p class="mt-4 text-center">Belum punya akun? <a href="{{ route('register') }}" class="text-blue-500">Daftar</a></p>
    </div>
</body>
</html>
