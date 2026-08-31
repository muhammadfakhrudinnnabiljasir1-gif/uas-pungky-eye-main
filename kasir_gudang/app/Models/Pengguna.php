<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use Notifiable;

    /**
     * Nama tabel yang terkait dengan model ini.
     *
     * @var string
     */
    protected $table = 'pengguna';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'nama',
        'email',
        'kata_sandi',
        'peran',
    ];

    /**
     * Atribut yang harus disembunyikan untuk serialisasi array.
     *
     * @var array
     */
    protected $hidden = [
        'kata_sandi',
    ];

    /**
     * Mengubah kata sandi secara otomatis (Opsional namun disarankan, Laravel 11/12 biasanya menggunakan metode casts).
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'kata_sandi' => 'hashed',
        ];
    }

    /**
     * Relasi ke tabel Transaksi
     * Seorang Pengguna (Kasir) dapat melakukan banyak Transaksi.
     */
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_pengguna');
    }
}
