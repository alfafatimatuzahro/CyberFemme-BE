<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->string('nama');
            $table->string('sku')->unique();
            $table->string('kategori');
            $table->decimal('harga', 15, 2);
            $table->integer('stok')->default(0);
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
