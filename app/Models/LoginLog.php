<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $fillable = [
        'user_id', 'ip_address', 'lokasi', 'user_agent',
        'status', 'keterangan', 'force_logout', 'logout_at',
    ];

    protected $casts = [
        'force_logout' => 'boolean',
        'logout_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isSuspicious(): bool
    {
        return $this->status === 'mencurigakan';
    }
}
