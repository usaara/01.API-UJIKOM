@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')

<div class="container">

    {{-- Header --}}
    <div class="p-5 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800">
            Daftar Pengguna Sistem
        </h3>

        <a href="{{ route('admin.user.create') }}"
           class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
            + Tambah User
        </a>
    </div>

    {{-- Form Search --}}
    <div class="p-5">

        <form action="{{ route('admin.user.index') }}" method="GET" class="flex w-full md:w-80">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama, email, role..."
                class="border border-gray-300 rounded-l-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-500"
            >

            <button
                type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-r-lg">
                Cari
            </button>

        </form>

        @if(request('search'))
            <a href="{{ route('admin.user.index') }}"
               class="inline-block mt-2 bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg text-sm">
                Reset Pencarian
            </a>
        @endif

    </div>

    {{-- Pesan berhasil --}}
    @if(session('success'))
        <div class="mx-5 mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabel --}}
    <div class="overflow-x-auto">

        <table class="w-full">

            <thead>
                <tr class="bg-gray-50">

                    <th class="py-3 px-4 border-b text-left">
                        No
                    </th>

                    <th class="py-3 px-4 border-b text-left">
                        Nama
                    </th>

                    <th class="py-3 px-4 border-b text-left">
                        Email
                    </th>

                    <th class="py-3 px-4 border-b text-left">
                        Role
                    </th>

                    <th class="py-3 px-4 border-b text-left">
                        Aksi
                    </th>

                </tr>
            </thead>

            <tbody>

                @forelse($users as $user)

                    <tr>

                        <td class="py-3 px-4 border-b">
                            {{ $users->firstItem() + $loop->index }}
                        </td>

                        <td class="py-3 px-4 border-b">
                            {{ $user->name }}
                        </td>

                        <td class="py-3 px-4 border-b">
                            {{ $user->email }}
                        </td>

                        <td class="py-3 px-4 border-b">
                            {{ $user->role }}
                        </td>

                        <td class="py-3 px-4 border-b">

                            <div class="flex space-x-2">

                                {{-- Tombol Edit --}}
                                <a href="{{ route('admin.user.edit', $user->id) }}"
                                   class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                    Edit
                                </a>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('admin.user.destroy', $user->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus user ini?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                        Hapus
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="py-5 text-center text-gray-500">
                            Tidak ada data user.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- Pagination --}}
    <div class="p-4 border-t border-gray-200 bg-gray-50">
        {{ $users->links() }}
    </div>

</div>

@endsection