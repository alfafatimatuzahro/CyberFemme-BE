<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('product_id');
            $table->string('nama_produk'); // snapshot nama saat transaksi
            $table->string('sku');
            $table->decimal('harga_satuan', 15, 2);
            $table->integer('qty');
            $table->decimal('diskon_persen', 5, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
