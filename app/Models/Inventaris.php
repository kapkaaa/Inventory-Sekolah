<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    use HasFactory;

    protected $table = 'inventaris';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'deskripsi',
        'jumlah_total',
        'jumlah_tersedia',
        'kondisi',
        'lokasi',
        'qr_code',
        'foto',
    ];

    protected $casts = [
        'jumlah_total' => 'integer',
        'jumlah_tersedia' => 'integer',
    ];

    public function peminjamanItems()
    {
        return $this->hasMany(PeminjamanItem::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('jumlah_tersedia', '>', 0);
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code ? asset('storage/' . $this->qr_code) : null;
    }

    public function isAvailable($jumlah = 1)
    {
        return $this->jumlah_tersedia >= $jumlah;
    }
}