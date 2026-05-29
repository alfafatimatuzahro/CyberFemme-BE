<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 'kasir_id', 'admin_id', 'nama_pelanggan', 'loyalty_id',
        'metode_bayar', 'subtotal', 'ppn', 'diskon', 'total',
        'nominal_bayar', 'kembalian', 'status', 'fraud_reason', 'fraud_id',
        'admin_reviewed', 'reviewed_by', 'reviewed_at', 'terminal_id', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'admin_reviewed' => 'boolean',
        'reviewed_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'ppn' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total' => 'decimal:2',
        'nominal_bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
    ];

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class, 'transaction_id');
    }

    // Generate invoice ID unik
    public static function generateInvoiceId(): string
    {
        $year = now()->format('Y');
        $month = strtoupper(now()->format('M'));
        $count = self::whereYear('created_at', now()->year)
                     ->whereMonth('created_at', now()->month)
                     ->count() + 1;
        return "INV/{$year}/{$month}/" . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public function isFraud(): bool
    {
        return in_array($this->status, ['mencurigakan', 'tertahan']);
    }
}
