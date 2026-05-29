<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username', 'email', 'password', 'role',
        'nama_umkm', 'alamat_umkm', 'admin_id',
        'profile_photo', 'security_question', 'security_answer',
        'is_active', 'temp_password',
    ];

    protected $hidden = [
        'password', 'remember_token', 'security_answer', 'temp_password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relasi: kasir milik admin
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Relasi: admin punya banyak kasir
    public function kasirs()
    {
        return $this->hasMany(User::class, 'admin_id')->where('role', 'kasir');
    }

    // Relasi: admin punya fraud rules
    public function fraudRule()
    {
        return $this->hasOne(FraudRule::class, 'admin_id');
    }

    // Relasi: transaksi yang dilakukan kasir
    public function transactionsAsKasir()
    {
        return $this->hasMany(Transaction::class, 'kasir_id');
    }

    // Relasi: semua transaksi di bawah admin
    public function transactionsAsAdmin()
    {
        return $this->hasMany(Transaction::class, 'admin_id');
    }

    // Relasi: notifikasi admin
    public function securityNotifications()
    {
        return $this->hasMany(SecurityNotification::class, 'admin_id');
    }

    // Relasi: login logs
    public function loginLogs()
    {
        return $this->hasMany(LoginLog::class, 'user_id');
    }

    // Helper
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    // Dapatkan admin_id yang sesuai (jika kasir → admin_id, jika admin → id)
    public function getEffectiveAdminId(): int
    {
        return $this->isAdmin() ? $this->id : $this->admin_id;
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo
            ? asset('storage/' . $this->profile_photo)
            : null;
    }
}
