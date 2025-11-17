<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class PeminjamanItem extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_items';

    protected $fillable = [
        'peminjaman_id',
        'inventaris_id',
        'jumlah',
        'kondisi_sebelum',
        'kondisi_sesudah',
        'catatan_item',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class);
    }
}