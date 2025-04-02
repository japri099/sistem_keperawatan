@extends('layouts.app')

@section('content')
<div class="flex">
    <div class="flex-1 p-8 bg-gray-100">

        {{-- Judul --}}
        <h1 class="text-2xl font-bold mb-4">Kelola Kelas</h1>

        {{-- Pesan Sukses --}}
        @if(session('success'))
            <div class="bg-green-200 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Search & Filter --}}
        <div class="mb-4 flex justify-between items-center">
            <form method="GET" action="{{ route('admin_fakultas.kelas.index') }}" class="flex space-x-2">
                {{-- Input Search --}}
                <input
                    type="text"
                    name="search"
                    placeholder="Cari kelas..."
                    value="{{ request('search') }}"
                    class="border border-gray-300 rounded px-4 py-2 w-64 focus:outline-none focus:ring"
                >

                {{-- Filter Semester --}}
                <select
                    name="semester"
                    class="border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring"
                    onchange="this.form.submit()"
                >
                    <option value="">Semua Semester</option>
                    @for ($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>
                            Semester {{ $i }}
                        </option>
                    @endfor
                </select>

                {{-- Tombol Search --}}
                <button
                    type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                >
                    Cari
                </button>
            </form>
        </div>

        {{-- Form Tambah / Edit --}}
        <div class="bg-white p-6 rounded shadow-md mb-6 w-full max-w-md">
            <h2 class="text-lg font-semibold mb-4">
                {{ isset($editKelas) ? 'Edit Kelas' : 'Tambah Kelas' }}
            </h2>

            <form
                method="POST"
                action="{{ isset($editKelas) ? route('admin_fakultas.kelas.update', $editKelas->kode_kelas) : route('admin_fakultas.kelas.store') }}"
            >
                @csrf
                @if(isset($editKelas))
                    @method('PUT')
                @endif

                <div class="mb-4">
                    <label class="block mb-1 font-medium">Nama Kelas</label>
                    <input
                        type="text"
                        name="nama_kelas"
                        value="{{ old('nama_kelas', $editKelas->nama_kelas ?? '') }}"
                        class="border border-gray-300 rounded px-4 py-2 w-full focus:outline-none focus:ring"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium">Semester</label>
                    <input
                        type="number"
                        min="1"
                        max="8"
                        name="semester"
                        value="{{ old('semester', $editKelas->semester ?? '') }}"
                        class="border border-gray-300 rounded px-4 py-2 w-full focus:outline-none focus:ring"
                        required
                    >
                </div>

                <div class="flex space-x-2">
                    <button
                        type="submit"
                        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
                    >
                        {{ isset($editKelas) ? 'Update' : 'Tambah' }}
                    </button>

                    @if(isset($editKelas))
                        <a
                            href="{{ route('admin_fakultas.kelas.index') }}"
                            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
                        >
                            Batal
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="bg-white p-6 rounded shadow-md overflow-x-auto" x-data="modalHandler()">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Kode Kelas</th>
                        <th class="py-3 px-6 text-left">Nama Kelas</th>
                        <th class="py-3 px-6 text-left">Semester</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($kelas as $item)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6">{{ $item->kode_kelas }}</td>
                            <td class="py-3 px-6">{{ $item->nama_kelas }}</td>
                            <td class="py-3 px-6">{{ $item->semester }}</td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex justify-center space-x-2">
                                    {{-- Edit --}}
                                    <a
                                        href="{{ route('admin_fakultas.kelas.index', ['edit' => $item->kode_kelas]) }}"
                                        class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 text-sm"
                                    >
                                        Edit
                                    </a>

                                    {{-- Hapus --}}
                                    <button
                                        @click="confirmDelete('{{ $item->kode_kelas }}')"
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-3 px-6 text-center text-gray-500">
                                Tidak ada data kelas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Modal Delete --}}
            <div
                x-show="showModal"
                x-cloak
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
            >
                <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                    <h2 class="text-xl font-semibold mb-4">Konfirmasi Hapus</h2>
                    <p class="mb-4">Yakin ingin menghapus kelas <strong x-text="kelasKode"></strong>?</p>

                    <div class="flex justify-end space-x-2">
                        <button
                            @click="showModal = false"
                            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
                        >
                            Batal
                        </button>

                        <form
                            :action="deleteUrl(kelasKode)"
                            method="POST"
                        >
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
                            >
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- AlpineJS --}}
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    function modalHandler() {
        return {
            showModal: false,
            kelasKode: '',

            confirmDelete(kode) {
                this.kelasKode = kode;
                this.showModal = true;
            },

            deleteUrl(kode) {
                return `/admin_fakultas/kelas/${kode}`;
                // return `{{ url('admin_fakultas/kelas') }}/${kode}`;
            }
        }
    }
</script>
@endsection
