@extends('layouts.app')

@section('title', 'Riwayat Peminjaman - Panel Peminjam')
@section('header-title', 'Riwayat Peminjaman')

@section('content')

    {{-- NOTIFIKASI SUCCESS --}}
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200
                    text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif


    {{-- NOTIFIKASI ERROR --}}
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200
                    text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif


    {{-- CARD RIWAYAT --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden
                border border-gray-200">

        {{-- HEADER --}}
        <div class="p-5 border-b border-gray-200 bg-gray-50
                    flex flex-col md:flex-row
                    justify-between items-start md:items-center gap-3">

            <div>
                <h3 class="text-lg font-bold text-gray-800">
                    Riwayat Peminjaman Saya
                </h3>

                <p class="text-xs text-gray-500 mt-1">
                    Daftar seluruh peminjaman alat yang pernah Anda ajukan.
                </p>
            </div>

            <a href="{{ route('peminjam.katalog') }}"
               class="bg-blue-600 hover:bg-blue-700
                      text-white text-sm font-semibold
                      px-4 py-2 rounded-lg transition whitespace-nowrap">

                + Ajukan Peminjaman

            </a>

        </div>


        {{-- TABLE --}}
        <div class="overflow-x-auto">

            <table class="w-full text-left border-collapse">

                <thead>

                    <tr class="bg-gray-100 text-gray-600
                               text-sm uppercase tracking-wider">

                        <th class="py-3 px-4 border-b">
                            No
                        </th>

                        <th class="py-3 px-4 border-b">
                            Tanggal Pinjam
                        </th>

                        <th class="py-3 px-4 border-b">
                            Alat yang Dipinjam
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

                    @forelse($peminjamans as $index => $peminjaman)

                        <tr class="hover:bg-gray-50 transition align-top">

                            {{-- NO --}}
                            <td class="py-3 px-4 border-b">
                                {{ $index + 1 }}
                            </td>


                            {{-- TANGGAL PINJAM --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">

                                {{ $peminjaman->tgl_pinjam
                                    ? $peminjaman->tgl_pinjam->format('d-m-Y')
                                    : '-' }}

                            </td>


                            {{-- ALAT --}}
                            <td class="py-3 px-4 border-b">

                                <ul class="list-disc list-inside space-y-1">

                                    @forelse($peminjaman->detailPinjams as $detail)

                                        <li>

                                            <span class="font-semibold text-gray-800">
                                                {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
                                            </span>

                                            <span class="text-xs bg-gray-200
                                                         px-1.5 py-0.5 rounded">

                                                {{ $detail->jumlah }} pcs

                                            </span>

                                        </li>

                                    @empty

                                        <span class="text-gray-400">
                                            Tidak ada alat
                                        </span>

                                    @endforelse

                                </ul>

                            </td>


                            {{-- RENCANA KEMBALI --}}
                            <td class="py-3 px-4 border-b whitespace-nowrap">

                                {{ $peminjaman->tgl_kembali_plan
                                    ? $peminjaman->tgl_kembali_plan->format('d-m-Y')
                                    : '-' }}

                            </td>


                            {{-- STATUS --}}
                            <td class="py-3 px-4 border-b">

                                <span class="px-2.5 py-1 text-xs
                                             font-semibold rounded-full

                                    @if($peminjaman->status == 'diajukan')
                                        bg-yellow-100 text-yellow-800

                                    @elseif($peminjaman->status == 'dipinjam')
                                        bg-blue-100 text-blue-800

                                    @elseif($peminjaman->status == 'dikembalikan')
                                        bg-emerald-100 text-emerald-800

                                    @elseif($peminjaman->status == 'selesai')
                                        bg-emerald-100 text-emerald-800

                                    @elseif($peminjaman->status == 'telat')
                                        bg-red-100 text-red-800

                                    @else
                                        bg-gray-100 text-gray-700
                                    @endif
                                ">

                                    @if($peminjaman->status == 'diajukan')
                                        Diajukan

                                    @elseif($peminjaman->status == 'dipinjam')
                                        Dipinjam

                                    @elseif($peminjaman->status == 'dikembalikan')
                                        Dikembalikan

                                    @elseif($peminjaman->status == 'selesai')
                                        Selesai

                                    @elseif($peminjaman->status == 'telat')
                                        Telat

                                    @else
                                        {{ ucfirst($peminjaman->status) }}
                                    @endif

                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5"
                                class="py-8 px-4 text-center text-gray-500">

                                Belum ada riwayat peminjaman.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- FOOTER --}}
        <div class="p-4 border-t border-gray-200 bg-gray-50">

            <p class="text-xs text-gray-500">

                Total:
                <span class="font-semibold text-gray-700">
                    {{ $peminjamans->count() }}
                </span>
                peminjaman

            </p>

        </div>

    </div>

@endsection