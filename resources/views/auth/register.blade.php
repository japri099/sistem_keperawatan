<!DOCTYPE html>
<html lang="id">
<head>
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="w-96 p-6 bg-white shadow-md rounded-md">
        <h2 class="text-2xl font-bold text-center mb-4">Register</h2>
        
        @if ($errors->any())
            <div class="text-red-500">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <label class="block mb-2">Pilih metode pendaftaran:</label>
            <div class="flex gap-4 mb-2">
                <input type="radio" name="method" value="manual" id="manual" checked onclick="toggleInputs()">
                <label for="manual">Input Manual</label>
                <input type="radio" name="method" value="excel" id="excel" onclick="toggleInputs()">
                <label for="excel">Import Excel</label>
            </div>

            <div id="manualInputs">
                <input type="number" name="username" placeholder="Username" class="w-full p-2 border rounded mb-2">
                <input type="text" name="name" placeholder="Nama Lengkap" class="w-full p-2 border rounded mb-2">
                <input type="password" name="password" placeholder="Password" class="w-full p-2 border rounded mb-2">
                <select name="role" class="w-full p-2 border rounded mb-2">
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                    <option value="instruktur">Instruktur</option>
                    <option value="admin_fakultas">Admin Fakultas</option>
                    <option value="pimpinan_fakultas">Pimpinan Fakultas</option>
                    <option value="mitra">Mitra</option>
                </select>
            </div>

            <div id="fileInput" class="hidden">
                <label class="block mb-2">Import dari Excel (Opsional):</label>
                <input type="file" name="file" class="w-full p-2 border rounded mb-2" accept=".xlsx,.xls,.csv">
            </div>

            <button type="submit" class="w-full bg-green-500 text-white p-2 rounded">Register</button>
        </form>

        <p class="mt-4 text-center">Sudah punya akun? 
            <a href="{{ route('login') }}" class="text-blue-500">Login</a>
        </p>
    </div>

    <script>
        function toggleInputs() {
    let method = document.querySelector('input[name="method"]:checked').value;
    let manualInputs = document.getElementById('manualInputs');
    let fileInput = document.getElementById('fileInput');
    let passwordInput = document.getElementById('password');

    manualInputs.classList.toggle('hidden', method === 'excel');
    fileInput.classList.toggle('hidden', method === 'manual');

    if (method === 'excel') {
        passwordInput.removeAttribute('required'); // Tidak wajib jika upload Excel
    } else {
        passwordInput.setAttribute('required', 'required'); // Wajib jika input manual
    }
}

    </script>
</body>
</html>
