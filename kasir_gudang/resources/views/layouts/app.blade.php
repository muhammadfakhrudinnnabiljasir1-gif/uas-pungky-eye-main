<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Kasir & Gudang</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <!-- Navbar -->
    <nav class="bg-blue-600 shadow-md">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="#" class="text-white text-xl font-bold tracking-wider">📦 Kasir & Gudang</a>
            <div class="space-x-4 text-white font-medium">
                <a href="{{ route('produk.index') }}" class="hover:text-blue-200 transition">Gudang</a>
                <a href="{{ route('kasir.index') }}" class="hover:text-blue-200 transition">Kasir (POS)</a>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <main class="container mx-auto px-4 py-8">
        <!-- Notifikasi Sukses/Gagal -->
        @if (session('sukses'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 shadow-sm">
                {{ session('sukses') }}
            </div>
        @endif
        @if (session('gagal'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 shadow-sm">
                {{ session('gagal') }}
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>
