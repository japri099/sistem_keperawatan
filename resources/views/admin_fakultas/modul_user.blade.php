@if(session('success'))
    <script>alert("{{ session('success') }}");</script>
@endif

@if(session('error'))
    <script>alert("{{ session('error') }}");</script>
@endif

@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Manajemen Pengguna</h2>

    <!-- Alert -->
    <div id="alert" class="hidden bg-green-500 text-white p-3 mb-4 rounded"></div>

    <!-- Search & Filter -->
    <div class="flex gap-4 mb-4">
        <input type="text" id="search" placeholder="Cari pengguna..." class="border p-2 w-1/2">
        <select id="filterRole" class="border p-2 w-1/4">
            <option value="">Semua Role</option>
            <option value="mahasiswa">Mahasiswa</option>
            <option value="dosen">Dosen</option>
            <option value="instruktur">Instruktur</option>
            <option value="pimpinan_fakultas">Pimpinan Fakultas</option>
            <option value="mitra">Mitra</option>
        </select>
    </div>

    <!-- Button Tambah User & Import Excel -->
    <div class="flex gap-4 mb-4">
        <button onclick="toggleModal('addUserModal')" class="bg-blue-500 text-white px-4 py-2 rounded">Tambah User</button>
        <button onclick="toggleModal('importModal')" class="bg-green-500 text-white px-4 py-2 rounded">Import Excel</button>
    </div>

    <!-- Tabel User -->
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">Username</th>
                <th class="border p-2">Nama</th>
                <th class="border p-2">Role</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody id="userTable">
            @foreach($users as $user)
            <tr class="userRow">
                <td class="border p-2">{{ $user->username }}</td>
                <td class="border p-2">{{ $user->name }}</td>
                <td class="border p-2 role">{{ $user->role }}</td>
                <td class="border p-2">
                    <button onclick='editUser(@json($user))' class="bg-blue-500 text-white px-3 py-1 rounded">Edit</button>
                    <button onclick='confirmDelete("{{ $user->username }}")' class="bg-red-500 text-white px-3 py-1 rounded">Hapus</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah User -->
<div id="addUserModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-[1000]">
    <div class="bg-white p-5 rounded w-[90%] md:w-[400px]">
        <h3 class="text-xl font-bold mb-3">Tambah User</h3>
        <input type="text" id="addUsername" class="border p-2 w-full mb-3" placeholder="Username" required>
        <input type="text" id="addName" class="border p-2 w-full mb-3" placeholder="Nama" required>
        <input type="password" id="addPassword" class="border p-2 w-full mb-3" placeholder="Password (Min. 6 Karakter)" required>
        <select id="addRole" class="border p-2 w-full mb-3">
            <option value="">Pilih Role</option>
            @foreach(['mahasiswa', 'dosen', 'instruktur', 'pimpinan_fakultas', 'mitra'] as $role)
                <option value="{{ $role }}">{{ ucfirst($role) }}</option>
            @endforeach
        </select>
        <div class='flex justify-between'>
            <button onclick="addUser()" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
            <button onclick="toggleModal('addUserModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Tutup</button>
        </div>
    </div>
</div>


<!-- Modal Edit User -->
<div id="editUserModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-[1000]">
    <div class="bg-white p-5 rounded w-[90%] md:w-[400px]">
        <h3 class="text-xl font-bold mb-3">Edit User</h3>
        <input type="hidden" id="editUsername">
        <input type="text" id="editName" class="border p-2 w-full mb-3" placeholder="Nama Lengkap">
        <select id="editRole" class="border p-2 w-full mb-3">
            <option value="">Pilih Role</option>
            @foreach(['mahasiswa', 'dosen', 'instruktur', 'pimpinan_fakultas', 'mitra'] as $role)
                <option value="{{ $role }}">{{ ucfirst($role) }}</option>
            @endforeach
        </select>
        <div class='flex justify-between'>
            <button onclick="updateUser()" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
            <button onclick="toggleModal('editUserModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Delet User -->
<div id="deleteUserModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-[1000]">
    <div class="bg-white p-5 rounded w-[90%] md:w-[400px] text-center">
        <h3 class="text-xl font-bold mb-3">Konfirmasi Hapus</h3>
        <p class="mb-4">Apakah Anda yakin ingin menghapus user <span id="deleteUsername"></span>?</p>
        <div class="flex justify-between">
            <button id="confirmDeleteButton" class="bg-red-500 text-white px-4 py-2 rounded">Hapus</button>
            <button onclick="toggleModal('deleteUserModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<div id="importModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-[1000]">
    <div class="bg-white p-5 rounded w-[90%] md:w-[400px]">
        <h3 class="text-xl font-bold mb-3">Import User dari Excel</h3>
        <form action="{{ route('admin_fakultas.user.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file" class="border p-2 w-full mb-3" required accept=".xlsx,.xls,.csv">
            <div class='flex justify-between'>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Import</button>
                <button type="button" onclick="toggleModal('importModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Tutup</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search Feature
    document.getElementById('search').addEventListener('input', function() {
        let searchText = this.value.toLowerCase();
        document.querySelectorAll('.userRow').forEach(row => {
            let username = row.children[0].textContent.toLowerCase();
            let name = row.children[1].textContent.toLowerCase();
            row.style.display = (username.includes(searchText) || name.includes(searchText)) ? '' : 'none';
        });
    });

    // Filter Role Feature
    document.getElementById('filterRole').addEventListener('change', function() {
        let selectedRole = this.value;
        document.querySelectorAll('.userRow').forEach(row => {
            let role = row.querySelector('.role').textContent;
            row.style.display = (selectedRole === '' || role === selectedRole) ? '' : 'none';
        });
    });
});

// Function to toggle modal visibility
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.toggle('hidden');
    }
}

// Tambah User
function addUser() {
    let username = document.getElementById('addUsername').value;
    let name = document.getElementById('addName').value;
    let password = document.getElementById('addPassword').value;
    let role = document.getElementById('addRole').value;

    if (!username || !name || !password || !role) {
        alert('Semua field harus diisi.');
        return;
    }

    fetch('/admin_fakultas/user', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ username, name, password, role })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success || 'User berhasil ditambahkan!');
        location.reload();
    })
    .catch(error => {
        alert('Terjadi kesalahan saat menambahkan user.');
    });

    toggleModal('addUserModal');
}

// Edit User
function editUser(user) {
    document.getElementById('editUsername').value = user.username;
    document.getElementById('editName').value = user.name;
    document.getElementById('editRole').value = user.role;
    toggleModal('editUserModal');
}

// Update User
function updateUser() {
    let username = document.getElementById('editUsername').value;
    let name = document.getElementById('editName').value;
    let role = document.getElementById('editRole').value;

    fetch(`/admin_fakultas/user/${username}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ name, role })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success || 'User berhasil diperbarui!');
        location.reload();
    })
    .catch(error => {
        alert('Terjadi kesalahan saat memperbarui user.');
    });

    toggleModal('editUserModal');
}

// Konfirmasi Hapus
function confirmDelete(username) {
    document.getElementById('deleteUsername').textContent = username;
    document.getElementById('confirmDeleteButton').setAttribute('onclick', `deleteUser('${username}')`);
    toggleModal('deleteUserModal');
}

// Hapus User
function deleteUser(username) {
    fetch(`/admin_fakultas/user/${username}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success || 'User berhasil dihapus!');
        location.reload();
    })
    .catch(error => {
        alert('Terjadi kesalahan saat menghapus user.');
    });

    toggleModal('deleteUserModal');
}
</script>
@endsection
