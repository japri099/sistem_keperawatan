<div class="w-1/4 bg-gray-800 text-white h-screen p-4">
    <h2 class="text-lg font-bold">Menu</h2>
    <ul class="mt-4">
        <li class="py-2">
            <a href="{{ route('dashboard.admin_fakultas') }}" class="hover:text-gray-300">Dashboard</a>
        </li>
        <li class="py-2">
            <a href="{{ route('admin_fakultas.user.index') }}" class="hover:text-gray-300">Kelola User</a>
        </li>
        <li class="py-2">
            <a href="{{ route('admin_fakultas.kelas.index') }}" class="hover:text-gray-300">Kelola Kelas</a>
        </li>
        <li class="py-2">
            <a href="{{ route('admin_fakultas.mata_kuliah.index') }}" class="hover:text-gray-300">Kelola Mata Kuliah</a>
        </li>
        <li class="py-2">
            <form action="{{ route('logout') }}" method="POST">
            @csrf
                <button type="submit" class="text-red-400 hover:text-red-600">
                    Logout
                </button>
            </form>
        </li>
    </ul>
</div>
