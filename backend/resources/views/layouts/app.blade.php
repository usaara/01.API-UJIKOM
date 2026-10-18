<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Dashboard')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>


<body class="bg-gray-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">


        <!-- ========================================== -->
        <!-- SIDEBAR -->
        <!-- ========================================== -->

        <aside class="w-64 bg-gray-900 text-white flex flex-col hidden md:flex">


            <!-- JUDUL PANEL -->
            <div class="h-16 flex items-center justify-center
                        border-b border-gray-800">

                <span class="text-xl font-bold tracking-wider">

                    @if(auth()->user()->role == 'admin')

                        PANEL ADMIN

                    @elseif(auth()->user()->role == 'petugas')

                        PANEL PETUGAS

                    @elseif(auth()->user()->role == 'peminjam')

                        PANEL PEMINJAM

                    @endif

                </span>

            </div>


            <!-- MENU SIDEBAR -->
            <nav class="flex-1 p-4 space-y-2">


                <!-- ================================== -->
                <!-- MENU ADMIN -->
                <!-- ================================== -->

                @if(auth()->user()->role == 'admin')


                    <a href="{{ route('admin.dashboard') }}"
                       class="block px-4 py-2 rounded-lg transition
                       {{ request()->routeIs('admin.dashboard')
                            ? 'bg-gray-800 text-white font-medium shadow'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        Dashboard

                    </a>


                    <a href="{{ route('admin.alat.index') }}"
                       class="block px-4 py-2 rounded-lg transition
                       {{ request()->routeIs('admin.alat.*')
                            ? 'bg-gray-800 text-white font-medium shadow'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        Kelola Alat

                    </a>


                    <a href="{{ route('admin.user.index') }}"
                       class="block px-4 py-2 rounded-lg transition
                       {{ request()->routeIs('admin.user.*')
                            ? 'bg-gray-800 text-white font-medium shadow'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        Kelola User

                    </a>


                    <a href="{{ route('admin.kategori.index') }}"
                       class="block px-4 py-2 rounded-lg transition
                       {{ request()->routeIs('admin.kategori.*')
                            ? 'bg-gray-800 text-white font-medium shadow'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        Kelola Kategori

                    </a>


                    <a href="{{ route('admin.peminjaman.index') }}"
                       class="block px-4 py-2 rounded-lg transition
                       {{ request()->routeIs('admin.peminjaman.*')
                            ? 'bg-gray-800 text-white font-medium shadow'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        Kelola Peminjaman

                    </a>


                    <a href="{{ route('admin.pengembalian.index') }}"
                       class="block px-4 py-2 rounded-lg transition
                       {{ request()->routeIs('admin.pengembalian.*')
                            ? 'bg-gray-800 text-white font-medium shadow'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        Kelola Pengembalian

                    </a>


                @endif



                <!-- ================================== -->
                <!-- MENU PETUGAS -->
                <!-- ================================== -->

                @if(auth()->user()->role == 'petugas')


                    <a href="{{ route('petugas.peminjaman.index') }}"
                       class="block px-4 py-2 rounded-lg transition
                       {{ request()->routeIs('petugas.peminjaman.*')
                            ? 'bg-gray-800 text-white font-medium shadow'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        Persetujuan Peminjaman

                    </a>


                    <a href="{{ route('petugas.pengembalian.index') }}"
                       class="block px-4 py-2 rounded-lg transition
                       {{ request()->routeIs('petugas.pengembalian.*')
                            ? 'bg-gray-800 text-white font-medium shadow'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        Pemantauan Pengembalian

                    </a>


                    <a href="{{ route('petugas.laporan.index') }}"
                       class="block px-4 py-2 rounded-lg transition
                       {{ request()->routeIs('petugas.laporan.*')
                            ? 'bg-gray-800 text-white font-medium shadow'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        Cetak Laporan

                    </a>


                @endif



                <!-- ================================== -->
                <!-- MENU PEMINJAM -->
                <!-- ================================== -->

                @if(auth()->user()->role == 'peminjam')


                    <a href="{{ route('peminjam.dashboard') }}"
                       class="block px-4 py-2 rounded-lg transition
                       {{ request()->routeIs('peminjam.dashboard')
                            ? 'bg-gray-800 text-white font-medium shadow'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        Dashboard

                    </a>


                    <a href="{{ route('peminjam.katalog') }}"
                       class="block px-4 py-2 rounded-lg transition
                       {{ request()->routeIs('peminjam.katalog')
                            ? 'bg-gray-800 text-white font-medium shadow'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        Katalog Alat

                    </a>


                    <a href="{{ route('peminjam.riwayat') }}"
                       class="block px-4 py-2 rounded-lg transition
                       {{ request()->routeIs('peminjam.riwayat')
                            ? 'bg-gray-800 text-white font-medium shadow'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        Riwayat Peminjaman

                    </a>


                @endif

            </nav>



            <!-- ================================== -->
            <!-- USER LOGIN -->
            <!-- ================================== -->

            <div class="p-4 border-t border-gray-800 text-sm text-gray-400">

                <span class="block font-semibold text-white">
                    {{ Auth::user()->name }}
                </span>

                <span class="block text-xs mt-1 uppercase">
                    {{ Auth::user()->role }}
                </span>

            </div>


        </aside>



        <!-- ========================================== -->
        <!-- MAIN CONTENT -->
        <!-- ========================================== -->

        <div class="flex-1 flex flex-col overflow-y-auto">


            <!-- ================================== -->
            <!-- NAVBAR ATAS -->
            <!-- ================================== -->

            <header class="bg-white shadow-sm h-16
                           flex items-center justify-between
                           px-6 z-10">


                <!-- JUDUL HALAMAN -->
                <div class="text-lg font-semibold text-gray-800">

                    @yield('header-title', 'Dashboard')

                </div>


                <!-- LOGOUT -->
                <div>

                    <form action="{{ route('logout') }}"
                          method="POST">

                        @csrf

                        <button type="submit"
                                class="bg-red-500 hover:bg-red-600
                                       text-white text-sm font-semibold
                                       px-4 py-2 rounded-lg
                                       transition">

                            Logout

                        </button>

                    </form>

                </div>


            </header>



            <!-- ================================== -->
            <!-- KONTEN HALAMAN -->
            <!-- ================================== -->

            <main class="flex-1 p-6">

                @yield('content')

            </main>


        </div>

    </div>

</body>

</html>