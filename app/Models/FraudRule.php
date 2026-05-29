<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FraudRule extends Model
{
    protected $fillable = [
        'admin_id',
        'batas_nominal_max', 'batas_nominal_aktif',
        'batas_qty_max', 'batas_qty_aktif',
        'rentang_duplikasi_menit', 'anti_spam_aktif',
        'jam_buka', 'jam_tutup', 'jam_operasional_aktif', 'auto_logout_aktif',
    ];

    protected $casts = [
        'batas_nominal_max' => 'decimal:2',
        'batas_nominal_aktif' => 'boolean',
        'batas_qty_aktif' => 'boolean',
        'anti_spam_aktif' => 'boolean',
        'jam_operasional_aktif' => 'boolean',
        'auto_logout_aktif' => 'boolean',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Cek apakah sekarang dalam jam operasional
    public function isWithinOperationalHours(): bool
    {
        if (!$this->jam_operasional_aktif) return true;

        $now = now()->format('H:i:s');
        return $now >= $this->jam_buka && $now <= $this->jam_tutup;
    }
}
