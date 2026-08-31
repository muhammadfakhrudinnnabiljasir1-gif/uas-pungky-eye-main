<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model ini.
     *
     * @var string
     */
    protected $table = 'transaksi';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'id_pengguna',
        'nomor_nota',
        'total_harga',
        'uang_bayar',
        'uang_kembali',
        'tanggal_dibuat',
    ];

    /**
     * Menonaktifkan timestamps default jika diperlukan (tapi kita pakai timestamps di migrasi, jadi biarkan saja).
     * Kita menggunakan kolom 'tanggal_dibuat' sebagai penanda spesifik.
     */

    /**
     * Relasi ke tabel Pengguna.
     * Sebuah Transaksi diproses oleh satu Pengguna (Kasir).
     */
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    /**
     * Relasi ke tabel Detail Transaksi.
     * Sebuah Transaksi memiliki banyak Detail Transaksi (barang-barang yang dibeli).
     */
    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi');
    }
}
