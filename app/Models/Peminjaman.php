<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    protected $fillable = [
        'no_transaksi',
        'user_id',
        'status',
        'tgl_pinjam',
        'tgl_estimasi_kembali',
        'tgl_kembali',
        'total_denda',
        'catatan',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tgl_pinjam' => 'datetime',
        'tgl_estimasi_kembali' => 'datetime',
        'tgl_kembali' => 'datetime',
        'approved_at' => 'datetime',
        'total_denda' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(PeminjamanItem::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function calculateDenda()
    {
        if (!$this->tgl_kembali || !$this->tgl_estimasi_kembali) {
            return 0;
        }

        $kembali = Carbon::parse($this->tgl_kembali);
        $estimasi = Carbon::parse($this->tgl_estimasi_kembali);

        if ($kembali->lte($estimasi)) {
            return 0;
        }

        $hariTerlambat = $kembali->diffInDays($estimasi);
        $dendaPerHari = 5000; // Rp 5.000 per hari

        return $hariTerlambat * $dendaPerHari;
    }

    public function isOverdue()
    {
        return $this->status !== 'returned' && 
               Carbon::now()->gt($this->tgl_estimasi_kembali);
    }
}

