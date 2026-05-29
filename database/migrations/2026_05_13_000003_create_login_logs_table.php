<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('ip_address')->nullable();
            $table->string('lokasi')->nullable(); // hasil geolookup IP
            $table->string('user_agent')->nullable();
            $table->enum('status', ['sukses', 'gagal', 'mencurigakan'])->default('sukses');
            $table->string('keterangan')->nullable();
            $table->boolean('force_logout')->default(false);
            $table->timestamp('logout_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
