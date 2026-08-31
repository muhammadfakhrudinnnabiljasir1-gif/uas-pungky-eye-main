<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Menampilkan daftar produk beserta kategorinya.
     */
    public function index()
    {
        // Memuat produk berserta relasi kategori agar efisien (Eager Loading)
        $produk = Produk::with('kategori')->latest()->get();
        return view('produk.index', compact('produk'));
    }

    /**
     * Menampilkan formulir tambah produk.
     */
    public function create()
    {
        $kategori = Kategori::all();
        return view('produk.create', compact('kategori'));
    }

    /**
     * Menyimpan data produk baru ke Gudang.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori,id',
            'nama_produk' => 'required|string|max:255',
            'kode_barang' => 'required|string|unique:produk,kode_barang',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
        ], [
            'kode_barang.unique' => 'Kode barang sudah digunakan!',
        ]);

        Produk::create($request->all());

        return redirect()->route('produk.index')->with('sukses', 'Produk berhasil ditambahkan ke Gudang.');
    }

    /**
     * Menampilkan formulir ubah produk.
     */
    public function edit(Produk $produk)
    {
        $kategori = Kategori::all();
        return view('produk.edit', compact('produk', 'kategori'));
    }

    /**
     * Memperbarui data produk dan stok di Gudang.
     */
    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori,id',
            'nama_produk' => 'required|string|max:255',
            'kode_barang' => 'required|string|unique:produk,kode_barang,' . $produk->id,
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
        ]);

        $produk->update($request->all());

        return redirect()->route('produk.index')->with('sukses', 'Data produk berhasil diperbarui.');
    }

    /**
     * Menghapus produk dari Gudang.
     */
    public function destroy(Produk $produk)
    {
        $produk->delete();
        return redirect()->route('produk.index')->with('sukses', 'Produk berhasil dihapus.');
    }
}
