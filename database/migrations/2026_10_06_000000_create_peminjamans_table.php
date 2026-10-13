<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('buku_id')->constrained('books')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali')->nullable();
            $table->enum('status', ['pending', 'dipinjam', 'dikembalikan', 'ditolak'])->default('pending');
            $table->decimal('denda', 10, 2)->default(0);
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index(['buku_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};