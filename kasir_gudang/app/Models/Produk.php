<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model ini.
     *
     * @var string
     */
    protected $table = 'produk';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'id_kategori',
        'nama_produk',
        'kode_barang',
        'harga',
        'stok',
    ];

    /**
     * Relasi ke tabel Kategori.
     * Sebuah Produk merupakan bagian dari satu Kategori.
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    /**
     * Relasi ke tabel Detail Transaksi.
     * Sebuah Produk dapat muncul di banyak Detail Transaksi.
     */
    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_produk');
    }
}
