@extends('layouts.app')

@section('title', 'Edit Pengembalian - Panel Admin')
@section('header-title', 'Edit Data Pengembalian')

@section('content')
    <div class="max-w-2xl bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-1">Edit Pengembalian</h3>
        <p class="text-sm text-gray-500 mb-6">
            Peminjam: {{ $pengembalian->peminjaman->user->name ?? 'User Dihapus' }}
            | Tanggal: {{ $pengembalian->tgl_kembali?->format('d/m/Y') ?? '-' }}
        </p>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.pengembalian.update', $pengembalian->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="kondisi_kembali" class="block text-sm font-semibold text-gray-700 mb-1">Kondisi Barang</label>
                <input id="kondisi_kembali" type="text" name="kondisi_kembali"
                    value="{{ old('kondisi_kembali', $pengembalian->kondisi_kembali) }}" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="denda" class="block text-sm font-semibold text-gray-700 mb-1">Denda</label>
                <input id="denda" type="number" name="denda" min="0"
                    value="{{ old('denda', $pengembalian->denda) }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.pengembalian.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection