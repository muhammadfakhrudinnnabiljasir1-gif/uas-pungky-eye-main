@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Manajemen Gudang</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola data barang dan pembaruan stok di sini.</p>
        </div>
        <a href="{{ route('produk.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-lg shadow-md transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Barang
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-50">
                <tr class="text-gray-600 text-sm leading-normal text-left">
                    <th class="py-4 px-6 border-b font-semibold">Kode</th>
                    <th class="py-4 px-6 border-b font-semibold">Nama Produk</th>
                    <th class="py-4 px-6 border-b font-semibold">Kategori</th>
                    <th class="py-4 px-6 border-b font-semibold text-right">Harga</th>
                    <th class="py-4 px-6 border-b font-semibold text-center">Stok</th>
                    <th class="py-4 px-6 border-b font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @forelse($produk as $item)
                <tr class="border-b border-gray-200 hover:bg-blue-50 transition duration-150">
                    <td class="py-4 px-6 font-medium text-gray-800 whitespace-nowrap">{{ $item->kode_barang }}</td>
                    <td class="py-4 px-6">{{ $item->nama_produk }}</td>
                    <td class="py-4 px-6">
                        <span class="bg-gray-200 text-gray-700 py-1 px-3 rounded-full text-xs">
                            {{ $item->kategori->nama_kategori ?? 'Tanpa Kategori' }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right font-semibold">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="py-4 px-6 text-center">
                        <span class="{{ $item->stok > 0 ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300' }} py-1 px-3 rounded-full text-xs font-bold">
                            {{ $item->stok }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-center flex justify-center space-x-2">
                        <!-- Tombol Edit -->
                        <a href="{{ route('produk.edit', $item->id) }}" class="bg-yellow-400 hover:bg-yellow-500 text-yellow-900 py-1.5 px-3 rounded shadow-sm text-xs font-bold transition">Ubah</a>
                        
                        <!-- Tombol Hapus -->
                        <form action="{{ route('produk.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini secara permanen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white py-1.5 px-3 rounded shadow-sm text-xs font-bold transition">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-500 font-medium">
                        Belum ada data barang di gudang.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
