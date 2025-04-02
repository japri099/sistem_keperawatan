@extends('layouts.app')

@section('title', 'Kelola Mata Kuliah')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-xl font-bold mb-4">Kelola Mata Kuliah</h2>

    <!-- Form Tambah/Edit Mata Kuliah -->
    <form id="mataKuliahForm" action="{{ route('admin_fakultas.mata_kuliah.store') }}" method="POST" class="mb-6">
        @csrf
        <input type="hidden" id="method" name="_method" value="POST">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="kode_mk" class="block text-sm font-medium">Kode Mata Kuliah</label>
                <input type="text" id="kode_mk" name="kode_mk" class="mt-1 p-2 border rounded w-full" readonly>
            </div>
            <div>
                <label for="nama_mk" class="block text-sm font-medium">Nama Mata Kuliah</label>
                <input type="text" id="nama_mk" name="nama_mk" class="mt-1 p-2 border rounded w-full" required>
            </div>
            <div>
                <label for="kode_kelas" class="block text-sm font-medium">Kelas</label>
                <select id="kode_kelas" name="kode_kelas" class="mt-1 p-2 border rounded w-full">
                    @foreach($kelas as $k)
                        <option value="{{ $k->kode_kelas }}">{{ $k->kode_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="username" class="block text-sm font-medium">Dosen</label>
                <select id="username" name="username" class="mt-1 p-2 border rounded w-full">
                    @foreach($dosen as $d)
                        <option value="{{ $d->username }}">{{ $d->name }} ({{ $d->username }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-4 flex space-x-2">
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">
                Simpan
            </button>
            <button type="button" id="batalButton" onclick="resetForm()" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-700 hidden">
                Batal
            </button>
        </div>
    </form>

    <!-- Tabel Data Mata Kuliah -->
    <table class="w-full border-collapse border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-2">Kode MK</th>
                <th class="border p-2">Nama Mata Kuliah</th>
                <th class="border p-2">Kelas</th>
                <th class="border p-2">Dosen</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mataKuliah as $mk)
            <tr>
                <td class="border p-2">{{ $mk->kode_mk }}</td>
                <td class="border p-2">{{ $mk->nama_mk }}</td>
                <td class="border p-2">{{ $mk->kode_kelas }}</td>
                <td class="border p-2">
                    {{ $mk->dosen->name ?? 'Dosen tidak ditemukan' }} ({{ $mk->username }})
                </td>
                <td class="border p-2 flex space-x-2">
                    <!-- Tombol Edit -->
                    <button onclick="editMataKuliah('{{ $mk->kode_mk }}', '{{ $mk->nama_mk }}', '{{ $mk->kode_kelas }}', '{{ $mk->username }}')" class="bg-yellow-500 text-white py-1 px-3 rounded hover:bg-yellow-700">
                        Edit
                    </button>
                    <!-- Form Delete -->
                    <form action="{{ route('admin_fakultas.mata_kuliah.destroy', $mk->kode_mk) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white py-1 px-3 rounded hover:bg-red-700">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
function editMataKuliah(kode, nama, kelas, dosen) {
    let form = document.getElementById('mataKuliahForm');

    // Ubah form menjadi mode edit
    form.action = "/admin_fakultas/mata_kuliah/" + kode;
    document.getElementById('method').value = "PUT";

    document.getElementById('kode_mk').value = kode;
    document.getElementById('nama_mk').value = nama;
    document.getElementById('kode_kelas').value = kelas;
    document.getElementById('username').value = dosen;

    // Tampilkan tombol batal
    document.getElementById('batalButton').classList.remove('hidden');
}

function resetForm() {
    let form = document.getElementById('mataKuliahForm');

    // Kembalikan ke mode tambah
    form.action = "{{ route('admin_fakultas.mata_kuliah.store') }}";
    document.getElementById('method').value = "POST";

    document.getElementById('kode_mk').value = "";
    document.getElementById('nama_mk').value = "";
    document.getElementById('kode_kelas').selectedIndex = 0;
    document.getElementById('username').selectedIndex = 0;

    // Sembunyikan tombol batal
    document.getElementById('batalButton').classList.add('hidden');
}
</script>
@endsection
