<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityNotification extends Model
{
    protected $fillable = [
        'admin_id', 'type', 'judul', 'pesan',
        'ref_type', 'ref_id', 'is_read', 'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Buat notifikasi fraud transaksi
    public static function createFraudAlert(int $adminId, Transaction $transaction): self
    {
        return self::create([
            'admin_id' => $adminId,
            'type' => 'urgent',
            'judul' => 'Transaksi Mencurigakan Terdeteksi',
            'pesan' => "Transaksi {$transaction->invoice_id} dari kasir {$transaction->kasir->username} ditandai karena \"{$transaction->fraud_reason}\". Id Fraud: {$transaction->fraud_id}",
            'ref_type' => 'transaction',
            'ref_id' => $transaction->id,
        ]);
    }

    // Buat notifikasi login mencurigakan
    public static function createLoginAlert(int $adminId, LoginLog $log): self
    {
        return self::create([
            'admin_id' => $adminId,
            'type' => 'urgent',
            'judul' => 'Login Mencurigakan Terdeteksi',
            'pesan' => "Login baru dari lokasi tidak biasa ({$log->lokasi}). Id Login: {$log->id}",
            'ref_type' => 'login_log',
            'ref_id' => $log->id,
        ]);
    }

    // Buat notifikasi info
    public static function createInfo(int $adminId, string $judul, string $pesan): self
    {
        return self::create([
            'admin_id' => $adminId,
            'type' => 'info',
            'judul' => $judul,
            'pesan' => $pesan,
        ]);
    }
}
