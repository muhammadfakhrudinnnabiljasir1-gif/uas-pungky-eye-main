<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model ini.
     *
     * @var string
     */
    protected $table = 'kategori';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'nama_kategori',
    ];

    /**
     * Relasi ke tabel Produk.
     * Satu Kategori dapat memiliki banyak Produk.
     */
    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_kategori');
    }
}
