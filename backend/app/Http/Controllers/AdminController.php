<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // Menampilkan Dashboard Admin & Log Aktivitas
    public function index()
    {
        $logs = LogAktivitas::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('logs'));
    }

    // CRUD Alat: Menampilkan daftar alat
    public function indexAlat(Request $request)
    {
        $search = $request->input('search');

        $alats = Alat::with('kategori')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_alat', 'like', "%{$search}%")
                        ->orWhere('status_kondisi', 'like', "%{$search}%")
                        ->orWhereHas('kategori', function ($kategoriQuery) use ($search) {
                            $kategoriQuery->where(
                                'nama_kategori',
                                'like',
                                "%{$search}%"
                            );
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.alat.index', compact('alats', 'search'));
    }

    // 2. Menampilkan form tambah alat
    public function createAlat()
    {
        $kategoris = Kategori::all();

        return view('admin.alat.create', compact('kategoris'));
    }

    // 3. Menyimpan alat baru
    public function storeAlat(Request $request)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('gambar');

        // Upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $folder = public_path('storage/alat');

            // Membuat folder jika belum ada
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $file->move($folder, $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        // Simpan alat
        $alat = Alat::create($data);

        // Catat log aktivitas
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menambahkan alat baru: ' . $alat->nama_alat,
        ]);

        return redirect()
            ->route('admin.alat.index')
            ->with('success', 'Data alat berhasil ditambahkan.');
    }

    // 4. Menampilkan form edit alat
    public function editAlat($id)
    {
        $alat = Alat::findOrFail($id);
        $kategoris = Kategori::all();

        return view('admin.alat.edit', compact('alat', 'kategoris'));
    }

    // 5. Memperbarui data alat
    public function updateAlat(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('gambar');

        // Upload gambar baru jika ada
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            if (
                $alat->gambar &&
                file_exists(public_path($alat->gambar))
            ) {
                unlink(public_path($alat->gambar));
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $folder = public_path('storage/alat');

            // Membuat folder jika belum ada
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $file->move($folder, $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        $alat->update($data);

        // Catat log aktivitas
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Memperbarui alat: ' . $alat->nama_alat,
        ]);

        return redirect()
            ->route('admin.alat.index')
            ->with('success', 'Data alat berhasil diperbarui.');
    }

    // 6. Menghapus data alat
    public function destroyAlat($id)
    {
        $alat = Alat::findOrFail($id);

        // Hapus gambar fisik jika ada
        if (
            $alat->gambar &&
            file_exists(public_path($alat->gambar))
        ) {
            unlink(public_path($alat->gambar));
        }

        // Simpan nama alat sebelum dihapus
        $namaAlat = $alat->nama_alat;

        $alat->delete();

        // Catat log aktivitas
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menghapus alat: ' . $namaAlat,
        ]);

        return redirect()
            ->route('admin.alat.index')
            ->with('success', 'Data alat berhasil dihapus.');
    }

    // CRUD User (Manajemen User Admin, Petugas, Peminjam)
    public function indexUser(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.user.index', compact('users', 'search'));
    }

    public function createUser()
    {
        return view('admin.user.create');
    }

    // Menyimpan user baru ke database
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,petugas,peminjam',
            'no_hp' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'no_hp' => $request->no_hp,
        ]);

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);

        return view('admin.user.edit', compact('user'));
    }

    // Memperbarui data user
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,petugas,peminjam',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'no_hp' => $request->no_hp,
        ];

        // Jika password diisi, update password
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    // Menghapus user
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'User berhasil dihapus.');
    }

    // 1. Menampilkan daftar kategori
    public function indexKategori(Request $request)
    {
        $search = $request->input('search');

        $kategoris = Kategori::when($search, function ($query, $search) {
            return $query->where(
                'nama_kategori',
                'like',
                "%{$search}%"
            );
        })
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view(
            'admin.kategori.index',
            compact('kategoris', 'search')
        );
    }

    // 2. Menampilkan form tambah kategori
    public function createKategori()
    {
        return view('admin.kategori.create');
    }

    // 3. Menyimpan kategori baru
    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    // 4. Menampilkan form edit kategori
    public function editKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        return view(
            'admin.kategori.edit',
            compact('kategori')
        );
    }

    // 5. Memperbarui kategori
    public function updateKategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,' . $id,
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    // 6. Menghapus kategori
    public function destroyKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        // Cek apakah kategori masih digunakan oleh alat
        if ($kategori->alats()->count() > 0) {
            return redirect()
                ->route('admin.kategori.index')
                ->with(
                    'error',
                    'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.'
                );
        }

        $kategori->delete();

        return redirect()
            ->route('admin.kategori.index')
            ->with(
                'success',
                'Kategori berhasil dihapus.'
            );
    }

    // 1. Menampilkan daftar peminjaman
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('status', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.peminjaman.index',
            compact('peminjamans', 'search')
        );
    }

    // 2. Menampilkan form tambah peminjaman
    public function createPeminjaman()
    {
        $users = User::where('role', 'peminjam')->get();
        $alats = Alat::where('stok', '>', 0)->get();

        return view(
            'admin.peminjaman.create',
            compact('users', 'alats')
        );
    }

    // 3. Menyimpan data peminjaman
    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tgl_pinjam' => 'required|date',
            'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id' => 'required|array',
            'alat_id.*' => 'exists:alat,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Buat transaksi utama peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => $request->user_id,
                'tgl_pinjam' => $request->tgl_pinjam,
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan',
            ]);

            // Simpan detail alat yang dipinjam
            foreach ($request->alat_id as $index => $alatId) {
                $jumlahPinjam = $request->jumlah[$index];
                $alat = Alat::findOrFail($alatId);

                // Validasi stok
                if ($alat->stok < $jumlahPinjam) {
                    throw new \Exception(
                        "Stok alat '{$alat->nama_alat}' tidak mencukupi."
                    );
                }

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => $jumlahPinjam,
                ]);

                // Kurangi stok alat jika status langsung disetujui/dipinjam (Opsional, atau dikurangi saat status berubah jadi 'dipinjam')
            }

            DB::commit();

            return redirect()
                ->route('admin.peminjaman.index')
                ->with(
                    'success',
                    'Data peminjaman berhasil diajukan.'
                );
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    // 4. Memperbarui status peminjaman (Misal: dari diajukan -> dipinjam / selesai)
    public function updateStatusPeminjaman(Request $request, $id)
    {
        $peminjaman = Peminjaman::with('detailPinjams.alat')
            ->findOrFail($id);

        $request->validate([
            'status' => 'required|in:diajukan,dipinjam,selesai,telat',
        ]);

        DB::beginTransaction();

        try {
            $statusLama = $peminjaman->status;
            $statusBaru = $request->status;

            // Logika pengelolaan stok otomatis
            if ($statusLama != 'dipinjam' && $statusBaru == 'dipinjam') {
                // Kurangi stok karena barang resmi dipinjam
                foreach ($peminjaman->detailPinjams as $detail) {
                    $alat = $detail->alat;

                    if ($alat->stok < $detail->jumlah) {
                        throw new \Exception(
                            "Stok alat {$alat->nama_alat} tidak mencukupi untuk dipinjam."
                        );
                    }

                    $alat->decrement('stok', $detail->jumlah);
                }
            } elseif (
                $statusLama == 'dipinjam' &&
                $statusBaru == 'selesai'
            ) {
                // Kembalikan stok karena barang sudah dikembalikan (selesai)
                foreach ($peminjaman->detailPinjams as $detail) {
                    $detail->alat->increment('stok', $detail->jumlah);
                }
            }

            $peminjaman->update([
                'status' => $statusBaru,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.peminjaman.index')
                ->with(
                    'success',
                    'Status peminjaman berhasil diperbarui.'
                );
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    // 5. Menghapus data peminjaman
    public function destroyPeminjaman($id)
    {
        $peminjaman = Peminjaman::with('detailPinjams')
            ->findOrFail($id);

        // Jika statusnya sedang dipinjam, kembalikan stok terlebih dahulu sebelum dihapus
        if ($peminjaman->status == 'dipinjam') {
            foreach ($peminjaman->detailPinjams as $detail) {
                $detail->alat->increment(
                    'stok',
                    $detail->jumlah
                );
            }
        }

        $peminjaman->delete();

        return redirect()
            ->route('admin.peminjaman.index')
            ->with(
                'success',
                'Data peminjaman berhasil dihapus.'
            );
    }
    public function indexPengembalian(Request $request)
{
    $search = $request->input('search');

    $pengembalians = Pengembalian::with([
        'peminjaman.user',
        'petugas'
    ])
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('kondisi_kembali', 'like', "%{$search}%")
                    ->orWhereHas('peminjaman.user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('petugas', function ($petugasQuery) use ($search) {
                        $petugasQuery->where('name', 'like', "%{$search}%");
                    });
            });
        })
        ->latest('tgl_kembali')
        ->paginate(10)
        ->withQueryString();

    return view(
        'admin.pengembalian.index',
        compact('pengembalians', 'search')
    );
}

public function createPengembalian()
{
    $peminjamans = Peminjaman::with('user')
        ->where('status', 'dipinjam')
        ->whereDoesntHave('pengembalian')
        ->latest()
        ->get();

    return view(
        'admin.pengembalian.create',
        compact('peminjamans')
    );
}

public function storePengembalian(Request $request)
{
    $data = $request->validate([
        'peminjaman_id' => 'required|exists:peminjaman,id',
        'kondisi_kembali' => 'required|string|max:255',
        'denda' => 'nullable|integer|min:0',
    ]);

    DB::beginTransaction();

    try {
        $peminjaman = Peminjaman::with('detailPinjams.alat')
            ->findOrFail($data['peminjaman_id']);

        if (
            $peminjaman->status !== 'dipinjam' ||
            $peminjaman->pengembalian()->exists()
        ) {
            throw new \Exception(
                'Peminjaman ini tidak dapat diproses sebagai pengembalian.'
            );
        }

        Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tgl_kembali' => now(),
            'kondisi_kembali' => $data['kondisi_kembali'],
            'denda' => $data['denda'] ?? 0,
            'petugas_id' => auth()->id(),
        ]);

        foreach ($peminjaman->detailPinjams as $detail) {
            $detail->alat->increment('stok', $detail->jumlah);
        }

        $peminjaman->update([
            'status' => 'selesai'
        ]);

        DB::commit();

        return redirect()
            ->route('admin.pengembalian.index')
            ->with(
                'success',
                'Data pengembalian berhasil ditambahkan.'
            );

    } catch (\Exception $e) {
        DB::rollBack();

        return back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}

public function editPengembalian($id)
{
    $pengembalian = Pengembalian::with([
        'peminjaman.user'
    ])->findOrFail($id);

    return view(
        'admin.pengembalian.edit',
        compact('pengembalian')
    );
}

public function updatePengembalian(Request $request, $id)
{
    $data = $request->validate([
        'kondisi_kembali' => 'required|string|max:255',
        'denda' => 'nullable|integer|min:0',
    ]);

    $pengembalian = Pengembalian::findOrFail($id);

    $pengembalian->update([
        'kondisi_kembali' => $data['kondisi_kembali'],
        'denda' => $data['denda'] ?? 0,
    ]);

    return redirect()
        ->route('admin.pengembalian.index')
        ->with(
            'success',
            'Data pengembalian berhasil diperbarui.'
        );
}

public function destroyPengembalian($id)
{
    DB::beginTransaction();

    try {
        $pengembalian = Pengembalian::with(
            'peminjaman.detailPinjams.alat'
        )->findOrFail($id);

        $peminjaman = $pengembalian->peminjaman;

        foreach ($peminjaman->detailPinjams as $detail) {
            $detail->alat->decrement(
                'stok',
                $detail->jumlah
            );
        }

        $peminjaman->update([
            'status' => 'dipinjam'
        ]);

        $pengembalian->delete();

        DB::commit();

        return redirect()
            ->route('admin.pengembalian.index')
            ->with(
                'success',
                'Data pengembalian berhasil dihapus.'
            );

    } catch (\Exception $e) {
        DB::rollBack();

        return back()
            ->with('error', $e->getMessage());
    }
}
}