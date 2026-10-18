@extends('layouts.app')

@section('title', 'Dashboard Peminjam - Sistem Peminjaman')
@section('header-title', 'Dashboard Peminjam')

@section('content')

    {{-- ALERT SELAMAT DATANG --}}
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm">
        Selamat datang,
        <strong class="font-semibold">{{ auth()->user()->name }}</strong>!

        Anda login sebagai hak akses
        <span class="uppercase font-bold text-emerald-900">
            {{ auth()->user()->role }}
        </span>.
    </div>


    {{-- RINGKASAN PEMINJAMAN --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        {{-- Total --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-sm text-gray-500">
                Total Peminjaman
            </p>

            <p class="text-2xl font-bold text-gray-800 mt-2">
                {{ $totalPeminjaman }}
            </p>
        </div>


        {{-- Menunggu --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-sm text-gray-500">
                Menunggu Persetujuan
            </p>

            <p class="text-2xl font-bold text-gray-800 mt-2">
                {{ $menunggu }}
            </p>
        </div>


        {{-- Sedang Dipinjam --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-sm text-gray-500">
                Sedang Dipinjam
            </p>

            <p class="text-2xl font-bold text-gray-800 mt-2">
                {{ $sedangDipinjam }}
            </p>
        </div>


        {{-- Selesai --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-sm text-gray-500">
                Peminjaman Selesai
            </p>

            <p class="text-2xl font-bold text-gray-800 mt-2">
                {{ $selesai }}
            </p>
        </div>

    </div>


    {{-- GRID UTAMA --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">


        {{-- ============================= --}}
        {{-- PEMINJAMAN TERBARU --}}
        {{-- ============================= --}}
        <div class="xl:col-span-2 bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">

                <div>
                    <h3 class="text-lg font-bold text-gray-800">
                        Peminjaman Terbaru
                    </h3>

                    <p class="text-xs text-gray-500 mt-1">
                        Daftar peminjaman terakhir Anda
                    </p>
                </div>

                <a href="{{ route('peminjam.riwayat') }}"
                   class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                    Lihat Riwayat
                </a>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-left border-collapse">

                    <thead>
                        <tr class="bg-gray-100 text-gray-600 text-xs font-medium uppercase tracking-wider">

                            <th class="py-3 px-4 border-b">
                                Tanggal
                            </th>

                            <th class="py-3 px-4 border-b">
                                Alat
                            </th>

                            <th class="py-3 px-4 border-b">
                                Rencana Kembali
                            </th>

                            <th class="py-3 px-4 border-b">
                                Status
                            </th>

                        </tr>
                    </thead>


                    <tbody class="text-gray-700 text-sm">

                        @forelse($peminjamansTerbaru as $peminjaman)

                            @php

                                $status = strtolower($peminjaman->status);

                                $statusLabel = [
                                    'diajukan' => 'Diajukan',
                                    'dipinjam' => 'Dipinjam',
                                    'telat' => 'Terlambat',
                                    'dikembalikan' => 'Dikembalikan',
                                    'selesai' => 'Selesai',
                                ][$status] ?? ucfirst($status);

                                $statusClass = [
                                    'diajukan' => 'bg-yellow-100 text-yellow-800',
                                    'dipinjam' => 'bg-blue-100 text-blue-800',
                                    'telat' => 'bg-red-100 text-red-800',
                                    'dikembalikan' => 'bg-green-100 text-green-800',
                                    'selesai' => 'bg-green-100 text-green-800',
                                ][$status] ?? 'bg-gray-100 text-gray-700';

                            @endphp


                            <tr class="hover:bg-gray-50 transition">

                                {{-- Tanggal --}}
                                <td class="py-3 px-4 border-b whitespace-nowrap">

                                    {{ $peminjaman->tgl_pinjam
                                        ? \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d-m-Y')
                                        : '-' }}

                                </td>


                                {{-- Alat --}}
                                <td class="py-3 px-4 border-b">

                                    @forelse($peminjaman->detailPinjams as $detail)

                                        <div class="font-medium text-gray-900">
                                            {{ $detail->alat->nama_alat ?? '-' }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            Jumlah: {{ $detail->jumlah }}
                                        </div>

                                    @empty

                                        <span class="text-gray-400">
                                            Tidak ada alat
                                        </span>

                                    @endforelse

                                </td>


                                {{-- Kembali --}}
                                <td class="py-3 px-4 border-b whitespace-nowrap">

                                    {{ $peminjaman->tgl_kembali_plan
                                        ? \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d-m-Y')
                                        : '-' }}

                                </td>


                                {{-- Status --}}
                                <td class="py-3 px-4 border-b">

                                    <span class="inline-flex px-2 py-1 rounded text-xs font-medium {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4"
                                    class="py-6 px-4 text-center text-gray-500">

                                    Belum ada data peminjaman.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>



        {{-- ============================= --}}
        {{-- ALAT TERSEDIA --}}
        {{-- ============================= --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">

                <h3 class="text-lg font-bold text-gray-800">
                    Alat Tersedia
                </h3>

                <p class="text-xs text-gray-500 mt-1">
                    Alat yang dapat dipinjam
                </p>

            </div>


            <div class="p-4">

                @forelse($alatsTersedia as $alat)

                    <div class="py-3 border-b border-gray-100 last:border-b-0">

                        <div class="flex justify-between items-start gap-3">

                            <div>

                                <p class="font-medium text-gray-900">
                                    {{ $alat->nama_alat }}
                                </p>

                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $alat->kategori->nama_kategori ?? '-' }}
                                </p>

                            </div>


                            <span class="text-xs font-medium
                                         bg-green-100 text-green-800
                                         px-2 py-1 rounded whitespace-nowrap">

                                Stok {{ $alat->stok }}

                            </span>

                        </div>

                    </div>

                @empty

                    <div class="py-6 text-center text-gray-500 text-sm">
                        Belum ada alat tersedia.
                    </div>

                @endforelse


                <a href="{{ route('peminjam.katalog') }}"
                   class="block mt-4 text-center
                          bg-gray-800 hover:bg-gray-700
                          text-white text-sm font-medium
                          px-4 py-2 rounded-lg transition">

                    Lihat Katalog Alat

                </a>

            </div>

        </div>

    </div>


    {{-- ============================= --}}
    {{-- INFORMASI PEMINJAM --}}
    {{-- ============================= --}}
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">

        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">

            <h3 class="text-lg font-bold text-gray-800">
                Informasi Peminjaman
            </h3>

        </div>

        <div class="p-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">
                        Nama Peminjam
                    </p>

                    <p class="font-medium text-gray-800 mt-1">
                        {{ auth()->user()->name }}
                    </p>
                </div>


                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">
                        Email
                    </p>

                    <p class="font-medium text-gray-800 mt-1">
                        {{ auth()->user()->email }}
                    </p>
                </div>


                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">
                        Hak Akses
                    </p>

                    <p class="font-medium text-gray-800 mt-1 uppercase">
                        {{ auth()->user()->role }}
                    </p>
                </div>

            </div>

        </div>

    </div>

@endsection