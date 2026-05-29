<?php
// TransactionItem Model
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id', 'product_id', 'nama_produk', 'sku',
        'harga_satuan', 'qty', 'diskon_persen', 'subtotal',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'diskon_persen' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
