@extends('layouts.app')

@section('title', 'Katalog Alat - Peminjam')
@section('header-title', 'Katalog Alat')

@section('content')

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200
                    text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200
                    text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif


    {{-- JUDUL HALAMAN --}}
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">
            Katalog Alat
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            Pilih alat yang ingin kamu pinjam dan tentukan jumlahnya.
        </p>
    </div>


    <form action="{{ route('peminjam.peminjaman.ajukan') }}"
          method="POST">

        @csrf


        {{-- INFORMASI PEMINJAMAN --}}
        <div class="bg-white rounded-lg shadow-sm
                    border border-gray-200 mb-6">

            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">

                <h3 class="text-lg font-bold text-gray-800">
                    Informasi Peminjaman
                </h3>

            </div>


            <div class="p-6">

                <div class="max-w-sm">

                    <label class="block text-sm font-medium
                                  text-gray-700 mb-2">

                        Rencana Tanggal Kembali

                    </label>

                    <input
                        type="date"
                        name="tgl_kembali_plan"
                        min="{{ now()->addDay()->format('Y-m-d') }}"
                        required
                        class="w-full px-3 py-2 text-sm
                               border border-gray-300 rounded-lg
                               focus:outline-none
                               focus:ring-2 focus:ring-gray-400
                               focus:border-gray-400">

                </div>

            </div>

        </div>


        {{-- DAFTAR ALAT --}}
        <div class="bg-white rounded-lg shadow-sm
                    border border-gray-200 overflow-hidden">


            {{-- HEADER --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50
                        flex items-center justify-between gap-4">

                <div>

                    <h3 class="text-lg font-bold text-gray-800">
                        Daftar Alat Tersedia
                    </h3>

                    <p class="text-xs text-gray-500 mt-1">
                        Centang alat yang ingin dipinjam.
                    </p>

                </div>


                {{-- SEARCH --}}
                <div class="relative w-72">

                    <span class="absolute inset-y-0 left-0
                                 flex items-center pl-3
                                 text-gray-400">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-4 h-4"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.04 6.04a7.5 7.5 0 0 0 10.61 10.61Z"/>

                        </svg>

                    </span>


                    <input
                        type="text"
                        id="searchAlat"
                        placeholder="Cari nama alat..."
                        class="w-full pl-9 pr-3 py-2 text-sm
                               border border-gray-300 rounded-lg
                               bg-white
                               focus:outline-none
                               focus:ring-2 focus:ring-gray-400
                               focus:border-gray-400">

                </div>

            </div>


            {{-- TABLE --}}
            <div class="overflow-x-auto">

                <table class="w-full text-left border-collapse">

                    <thead>

                        <tr class="bg-gray-100 text-gray-600
                                   text-xs font-medium uppercase
                                   tracking-wider">

                            <th class="py-3 px-4 border-b text-center">
                                Pilih
                            </th>

                            <th class="py-3 px-4 border-b">
                                Gambar
                            </th>

                            <th class="py-3 px-4 border-b">
                                Nama Alat
                            </th>

                            <th class="py-3 px-4 border-b">
                                Kategori
                            </th>

                            <th class="py-3 px-4 border-b text-center">
                                Stok
                            </th>

                            <th class="py-3 px-4 border-b">
                                Jumlah
                            </th>

                        </tr>

                    </thead>


                    <tbody id="daftarAlat"
                           class="text-gray-700 text-sm">

                        @forelse($alats as $alat)

                            <tr class="alat-row hover:bg-gray-50 transition"
                                data-nama="{{ strtolower($alat->nama_alat) }}">


                                {{-- CHECKBOX --}}
                                <td class="py-4 px-4 border-b text-center">

                                    <input
                                        type="checkbox"
                                        name="alat_id[]"
                                        value="{{ $alat->id }}"
                                        class="w-4 h-4 text-gray-700
                                               border-gray-300 rounded
                                               focus:ring-gray-500">

                                </td>


                                {{-- GAMBAR --}}
                                <td class="py-4 px-4 border-b">

                                    <div class="w-14 h-14 rounded-lg
                                                border border-gray-200
                                                bg-gray-100 overflow-hidden
                                                flex items-center justify-center">

                                        @if(
                                            $alat->gambar &&
                                            file_exists(public_path($alat->gambar))
                                        )

                                            <img
                                                src="{{ asset($alat->gambar) }}"
                                                alt="{{ $alat->nama_alat }}"
                                                class="w-full h-full object-cover">

                                        @else

                                            <span class="text-xs text-gray-400">
                                                Tidak ada gambar
                                            </span>

                                        @endif

                                    </div>

                                </td>


                                {{-- NAMA --}}
                                <td class="py-4 px-4 border-b">

                                    <p class="font-medium text-gray-900">
                                        {{ $alat->nama_alat }}
                                    </p>

                                    @if($alat->deskripsi)

                                        <p class="text-xs text-gray-500 mt-1
                                                  max-w-md">

                                            {{ $alat->deskripsi }}

                                        </p>

                                    @endif

                                </td>


                                {{-- KATEGORI --}}
                                <td class="py-4 px-4 border-b">

                                    {{ $alat->kategori->nama_kategori ?? '-' }}

                                </td>


                                {{-- STOK --}}
                                <td class="py-4 px-4 border-b text-center">

                                    <span class="font-medium text-gray-800">

                                        {{ $alat->stok }}

                                    </span>

                                </td>


                                {{-- JUMLAH --}}
                                <td class="py-4 px-4 border-b">

                                    <input
                                        type="number"
                                        name="jumlah[{{ $alat->id }}]"
                                        value="1"
                                        min="1"
                                        max="{{ $alat->stok }}"
                                        class="w-full px-3 py-2 text-sm
                                               border border-gray-300 rounded-lg
                                               focus:outline-none
                                               focus:ring-2 focus:ring-blue-500">

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td colspan="6"
                                    class="py-8 px-4 text-center
                                           text-gray-500">

                                    Tidak ada alat yang tersedia saat ini.

                                </td>

                            </tr>

                        @endforelse


                        {{-- HASIL PENCARIAN KOSONG --}}
                        <tr id="tidakDitemukan"
                            style="display: none;">

                            <td colspan="6"
                                class="py-8 px-4 text-center
                                       text-gray-500">

                                Alat yang dicari tidak ditemukan.

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            {{-- FOOTER --}}
            <div class="px-6 py-4 border-t border-gray-200
                        bg-gray-50 flex justify-end gap-3">

                <a href="{{ route('peminjam.dashboard') }}"
                   class="bg-white hover:bg-gray-100
                          border border-gray-300
                          text-gray-700 px-5 py-2
                          rounded-lg text-sm font-medium
                          transition">

                    Kembali

                </a>


                <button
                    type="submit"
                    class="bg-gray-900 hover:bg-gray-800
                           text-white px-5 py-2
                           rounded-lg text-sm font-medium
                           transition">

                    Ajukan Peminjaman

                </button>

            </div>

        </div>

    </form>


    {{-- SEARCH SCRIPT --}}
    <script>

        const searchAlat = document.getElementById('searchAlat');
        const alatRows = document.querySelectorAll('.alat-row');
        const tidakDitemukan = document.getElementById('tidakDitemukan');

        searchAlat.addEventListener('input', function () {

            const keyword = this.value.toLowerCase().trim();

            let ditemukan = false;

            alatRows.forEach(function (row) {

                const namaAlat = row.getAttribute('data-nama');

                if (namaAlat.includes(keyword)) {

                    row.style.display = '';
                    ditemukan = true;

                } else {

                    row.style.display = 'none';

                }

            });

            if (ditemukan || keyword === '') {

                tidakDitemukan.style.display = 'none';

            } else {

                tidakDitemukan.style.display = '';

            }

        });

    </script>

@endsection