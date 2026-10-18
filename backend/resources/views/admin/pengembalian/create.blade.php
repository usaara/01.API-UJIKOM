@extends('layouts.app')

@section('title', 'Tambah Pengembalian - Panel Admin')
@section('header-title', 'Tambah Data Pengembalian')

@section('content')
    <div class="max-w-2xl bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-1">Catat Pengembalian</h3>
        <p class="text-sm text-gray-500 mb-6">Pilih transaksi yang sedang dipinjam untuk mencatat pengembaliannya.</p>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg text-sm">
                {{ $errors->first() }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.pengembalian.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="peminjaman_id" class="block text-sm font-semibold text-gray-700 mb-1">Peminjaman</label>
                <select id="peminjaman_id" name="peminjaman_id" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Pilih peminjaman</option>
                    @forelse ($peminjamans as $peminjaman)
                        <option value="{{ $peminjaman->id }}" {{ old('peminjaman_id') == $peminjaman->id ? 'selected' : '' }}>
                            #{{ $peminjaman->id }} - {{ $peminjaman->user->name ?? 'User Dihapus' }}
                            ({{ $peminjaman->tgl_pinjam?->format('d/m/Y') ?? '-' }})
                        </option>
                    @empty
                        <option value="" disabled>Tidak ada peminjaman aktif</option>
                    @endforelse
                </select>
            </div>

            <div>
                <label for="kondisi_kembali" class="block text-sm font-semibold text-gray-700 mb-1">Kondisi Barang</label>
                <input id="kondisi_kembali" type="text" name="kondisi_kembali" value="{{ old('kondisi_kembali') }}"
                    placeholder="Contoh: Lengkap dan berfungsi baik" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="denda" class="block text-sm font-semibold text-gray-700 mb-1">Denda</label>
                <input id="denda" type="number" name="denda" value="{{ old('denda', 0) }}" min="0"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.pengembalian.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Simpan</button>
            </div>
        </form>
    </div>
@endsection