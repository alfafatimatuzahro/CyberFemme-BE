<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id', 'nama', 'sku', 'kategori', 'harga', 'stok', 'foto', 'is_active',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    // Generate SKU unik
    public static function generateSku(string $kategori): string
    {
        $prefix = strtoupper(substr($kategori, 0, 3));
        $count = self::count() + 1;
        return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
