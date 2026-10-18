<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // ==========================================
    // PERSETUJUAN PEMINJAMAN
    // ==========================================

    // Menampilkan daftar pengajuan peminjaman
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with([
            'user',
            'detailPinjams.alat'
        ])
        ->where('status', 'diajukan')
        ->when($search, function ($query, $search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        })
        ->latest()
        ->get();

        return view(
            'petugas.peminjaman.index',
            compact('peminjamans', 'search')
        );
    }

    // Menyetujui peminjaman
    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::with('detailPinjams')
                ->findOrFail($id);

            $peminjaman->update([
                'status' => 'dipinjam'
            ]);

            // Kurangi stok alat
            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);

                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            DB::commit();

            return redirect()->back()->with(
                'success',
                'Peminjaman disetujui dan stok alat dikurangi.'
            );

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with(
                'error',
                'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }

    // Menolak peminjaman
    public function tolakPeminjaman($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            if ($peminjaman->status == 'diajukan') {
                $peminjaman->delete();

                return redirect()->back()->with(
                    'success',
                    'Pengajuan peminjaman berhasil ditolak.'
                );
            }

            return redirect()->back()->with(
                'error',
                'Status peminjaman sudah berubah.'
            );

        } catch (\Exception $e) {
            return redirect()->back()->with(
                'error',
                'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }


    // ==========================================
    // PEMANTAUAN PENGEMBALIAN
    // ==========================================

    // Menampilkan peminjaman yang sedang dipinjam atau telat
    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with([
            'user',
            'detailPinjams.alat'
        ])
        ->whereIn('status', ['dipinjam', 'telat'])
        ->when($search, function ($query, $search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        })
        ->latest()
        ->get();

        return view(
            'petugas.peminjaman.pengembalian.index',
            compact('peminjamans', 'search')
        );
    }

    // Memproses pengembalian
    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|integer',
        ]);

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::with('detailPinjams')
                ->findOrFail($peminjamanId);

            // Simpan data pengembalian
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $request->denda ?? 0,
                'petugas_id' => auth()->id(),
            ]);

            // Ubah status menjadi selesai
            $peminjaman->update([
                'status' => 'selesai'
            ]);

            // Kembalikan stok alat
            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);

                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            DB::commit();

            return redirect()->back()->with(
                'success',
                'Pengembalian berhasil dicatat dan stok dipulihkan.'
            );

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with(
                'error',
                'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }


    // ==========================================
    // CETAK LAPORAN
    // ==========================================

    // Menampilkan halaman laporan
    public function laporan(Request $request)
{
    $status = $request->input('status');
    $dari_tanggal = $request->input('dari_tanggal');
    $sampai_tanggal = $request->input('sampai_tanggal');

    $laporans = Peminjaman::with(['user', 'detailPinjams.alat', 'pengembalian'])
        ->when($status, function ($query, $status) {
            return $query->where('status', $status);
        })
        ->when($dari_tanggal && $sampai_tanggal, function ($query) use ($dari_tanggal, $sampai_tanggal) {
            return $query->whereBetween('tgl_pinjam', [$dari_tanggal, $sampai_tanggal]);
        })
        ->latest()
        ->get();

    return view('petugas.peminjaman.laporan.index', compact('laporans', 'status', 'dari_tanggal', 'sampai_tanggal'));
}

// Menampilkan halaman khusus cetak (print preview)
public function cetakLaporan(Request $request)
{
    $status = $request->input('status');
    $dari_tanggal = $request->input('dari_tanggal');
    $sampai_tanggal = $request->input('sampai_tanggal');

    $laporans = Peminjaman::with(['user', 'detailPinjams.alat', 'pengembalian'])
        ->when($status, function ($query, $status) {
            return $query->where('status', $status);
        })
        ->when($dari_tanggal && $sampai_tanggal, function ($query) use ($dari_tanggal, $sampai_tanggal) {
            return $query->whereBetween('tgl_pinjam', [$dari_tanggal, $sampai_tanggal]);
        })
        ->latest()
        ->get();

    return view('petugas.peminjaman.laporan.cetak', compact('laporans', 'status', 'dari_tanggal', 'sampai_tanggal'));
}
    }
