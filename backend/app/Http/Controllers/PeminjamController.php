<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamController extends Controller
{
    // Melihat daftar/katalog alat yang tersedia
    public function katalogAlat()
    {
        $alats = Alat::with('kategori')->where('stok', '>', 0)->get();
        return view('peminjam.katalog', compact('alats'));
    }

    // Dashboard peminjam
public function dashboard()
{
    $userId = auth()->id();

    // Total seluruh peminjaman milik user yang login
    $totalPeminjaman = Peminjaman::where('user_id', $userId)->count();

    // Peminjaman yang masih menunggu persetujuan petugas
    $menunggu = Peminjaman::where('user_id', $userId)
        ->where('status', 'diajukan')
        ->count();

    // Peminjaman yang sedang dibawa/dipinjam
    $sedangDipinjam = Peminjaman::where('user_id', $userId)
        ->whereIn('status', ['dipinjam', 'telat'])
        ->count();

    // Peminjaman yang sudah selesai
    $selesai = Peminjaman::where('user_id', $userId)
        ->whereIn('status', ['dikembalikan', 'selesai'])
        ->count();

    // 5 peminjaman terakhir
    $peminjamansTerbaru = Peminjaman::with([
        'detailPinjams.alat'
    ])
        ->where('user_id', $userId)
        ->latest()
        ->take(5)
        ->get();

    // Alat yang masih memiliki stok
    $alatsTersedia = Alat::with('kategori')
        ->where('stok', '>', 0)
        ->latest()
        ->take(4)
        ->get();

    return view('peminjam.dashboard', compact(
        'totalPeminjaman',
        'menunggu',
        'sedangDipinjam',
        'selesai',
        'peminjamansTerbaru',
        'alatsTersedia'
    ));
}

    public function ajukanPeminjaman(Request $request)
{
    $request->validate([
        'tgl_kembali_plan' => 'required|date|after:today',
        'alat_id' => 'required|array|min:1',
        'alat_id.*' => 'exists:alat,id',
        'jumlah' => 'required|array',
    ]);

    DB::beginTransaction();

    try {

        // Buat data peminjaman
        $peminjaman = Peminjaman::create([
            'user_id' => auth()->id(),
            'tgl_pinjam' => now(),
            'tgl_kembali_plan' => $request->tgl_kembali_plan,
            'status' => 'diajukan',
        ]);


        // Simpan setiap alat yang dipilih
        foreach ($request->alat_id as $alatId) {

            // Ambil jumlah berdasarkan ID alat
            $jumlah = (int) ($request->jumlah[$alatId] ?? 1);

            // Ambil data alat
            $alat = Alat::findOrFail($alatId);

            // Pastikan jumlah tidak melebihi stok
            if ($jumlah > $alat->stok) {
                throw new \Exception(
                    "Jumlah {$alat->nama_alat} melebihi stok yang tersedia."
                );
            }

            // Simpan detail peminjaman
            DetailPinjam::create([
                'peminjaman_id' => $peminjaman->id,
                'alat_id' => $alatId,
                'jumlah' => $jumlah,
            ]);
        }


        DB::commit();

        return redirect()
            ->route('peminjam.riwayat')
            ->with(
                'success',
                'Pengajuan peminjaman berhasil dikirim.'
            );

    } catch (\Exception $e) {

        DB::rollback();

        return redirect()
            ->back()
            ->withInput()
            ->with(
                'error',
                'Gagal mengajukan peminjaman: ' . $e->getMessage()
            );
    }
}
    // Melihat riwayat peminjaman user yang sedang login
    public function riwayatPeminjaman()
    {
        $peminjamans = Peminjaman::with('detailPinjams.alat')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('peminjam.riwayat', compact('peminjamans'));
    }
}