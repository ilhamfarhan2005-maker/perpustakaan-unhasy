<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('handled_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('borrow_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['dipinjam', 'selesai', 'terlambat'])->default('dipinjam');
            $table->unsignedTinyInteger('extension_count')->default(0);
            $table->timestamps();
            $table->index(['status', 'due_date']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
