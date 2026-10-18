@extends('layouts.app')

@section('title', 'Kelola Pengembalian - Panel Admin')
@section('header-title', 'Manajemen Data Pengembalian')

@section('content')
    @if (session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Riwayat Pengembalian</h3>
                <p class="text-sm text-gray-500 mt-1">Daftar alat yang telah dikembalikan oleh peminjam.</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
            <form action="{{ route('admin.pengembalian.index') }}" method="GET" class="flex w-full md:w-96">
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Cari peminjam, kondisi, petugas..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                    Cari
                </button>
                @if ($search)
                    <a href="{{ route('admin.pengembalian.index') }}"
                        class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                        Reset
                    </a>
                @endif
            </form>
            <a href="{{ route('admin.pengembalian.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-semibold rounded-lg transition whitespace-nowrap text-center">
                + Tambah Pengembalian
            </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Tanggal Kembali</th>
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Tanggal Peminjaman</th>
                        <th class="py-3 px-4 border-b">Kondisi</th>
                        <th class="py-3 px-4 border-b">Denda</th>
                        <th class="py-3 px-4 border-b">Petugas</th>
                        <th class="py-3 px-4 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse ($pengembalians as $pengembalian)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 border-b font-medium text-gray-900">
                                {{ $pengembalian->tgl_kembali?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="py-3 px-4 border-b">
                                {{ $pengembalian->peminjaman->user->name ?? 'User Dihapus' }}
                            </td>
                            <td class="py-3 px-4 border-b text-xs text-gray-600">
                                <span class="block">Pinjam: {{ $pengembalian->peminjaman->tgl_pinjam?->format('d/m/Y') ?? '-' }}</span>
                                <span class="block">Rencana: {{ $pengembalian->peminjaman->tgl_kembali_plan?->format('d/m/Y') ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full
                                    @if (str_contains(strtolower($pengembalian->kondisi_kembali), 'baik')) bg-emerald-100 text-emerald-800
                                    @elseif (str_contains(strtolower($pengembalian->kondisi_kembali), 'rusak')) bg-amber-100 text-amber-800
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ $pengembalian->kondisi_kembali }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-b font-semibold">
                                Rp {{ number_format($pengembalian->denda ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 border-b">
                                {{ $pengembalian->petugas->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4 border-b">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.pengembalian.edit', $pengembalian->id) }}"
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.pengembalian.destroy', $pengembalian->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus pengembalian ini dan kembalikan status peminjaman?')">
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
                            <td colspan="7" class="py-8 text-center text-gray-500">Belum ada data pengembalian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $pengembalians->links() }}
        </div>
    </div>
@endsection