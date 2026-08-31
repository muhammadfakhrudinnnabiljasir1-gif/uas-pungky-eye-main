<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    /**
     * Menampilkan antarmuka POS (Point of Sale) Kasir.
     */
    public function index()
    {
        $produk = Produk::where('stok', '>', 0)->get();
        return view('kasir.index', compact('produk'));
    }

    /**
     * Memproses pembayaran dan mengurangi stok (Transaksi).
     */
    public function store(Request $request)
    {
        $request->validate([
            'uang_bayar' => 'required|numeric|min:0',
            'keranjang'  => 'required|array|min:1',
            'keranjang.*.id_produk' => 'required|exists:produk,id',
            'keranjang.*.jumlah'    => 'required|integer|min:1',
        ]);

        try {
            // Memulai transaksi database agar aman jika terjadi kesalahan di tengah proses
            DB::transaction(function () use ($request) {
                
                $totalHarga = 0;
                $keranjang = $request->keranjang;
                $uangBayar = $request->uang_bayar;

                // 1. Validasi Stok dan Hitung Total Harga
                foreach ($keranjang as $item) {
                    // LockForUpdate mencegah terjadinya race condition (bentrokan saat ada pembeli bersamaan)
                    $produk = Produk::lockForUpdate()->find($item['id_produk']);
                    
                    if ($produk->stok < $item['jumlah']) {
                        throw new \Exception("Stok untuk produk {$produk->nama_produk} tidak mencukupi!");
                    }

                    $totalHarga += $produk->harga * $item['jumlah'];
                }

                if ($uangBayar < $totalHarga) {
                    throw new \Exception("Uang pembayaran kurang dari total belanja!");
                }

                $uangKembali = $uangBayar - $totalHarga;
                $nomorNota = 'NOTA-' . time() . '-' . rand(100, 999);

                // 2. Simpan Data Transaksi Utama
                $transaksi = Transaksi::create([
                    'id_pengguna'    => Auth::id() ?? 1, // Menggunakan ID Kasir yang login (default 1 sementara)
                    'nomor_nota'     => $nomorNota,
                    'total_harga'    => $totalHarga,
                    'uang_bayar'     => $uangBayar,
                    'uang_kembali'   => $uangKembali,
                    'tanggal_dibuat' => now(),
                ]);

                // 3. Simpan Detail Transaksi dan Kurangi Stok Gudang
                foreach ($keranjang as $item) {
                    $produk = Produk::find($item['id_produk']);
                    $subtotal = $produk->harga * $item['jumlah'];

                    // Buat detail riwayat belanja
                    DetailTransaksi::create([
                        'id_transaksi' => $transaksi->id,
                        'id_produk'    => $produk->id,
                        'jumlah'       => $item['jumlah'],
                        'subtotal'     => $subtotal,
                    ]);

                    // Mengurangi stok produk di database
                    $produk->decrement('stok', $item['jumlah']);
                }
            });

            return redirect()->route('kasir.index')->with('sukses', 'Transaksi berhasil diproses!');

        } catch (\Exception $e) {
            // Jika ada exception/error (misal stok kurang), maka semua perubahan di DB otomatis dibatalkan (Rollback)
            return redirect()->back()->with('gagal', $e->getMessage());
        }
    }
}
