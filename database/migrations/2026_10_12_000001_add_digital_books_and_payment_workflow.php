<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->enum('format', ['fisik', 'digital', 'fisik_digital'])->default('fisik');
            $table->string('digital_url')->nullable();
            $table->string('preview_url')->nullable();
            $table->string('kode_inventaris')->nullable()->unique();
        });

        foreach (DB::table('books')->select('id')->whereNull('kode_inventaris')->orderBy('id')->get() as $book) {
            DB::table('books')->where('id', $book->id)->update([
                'kode_inventaris' => 'UNHASY-BK-'.str_pad((string) $book->id, 6, '0', STR_PAD_LEFT),
            ]);
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('workflow_status', 40)->default('Dipinjam');
            $table->timestamp('requested_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('returned_confirmed_by')->nullable()->constrained('users')->nullOnDelete();
        });

        foreach (DB::table('transactions')->select('id', 'status')->orderBy('id')->get() as $transaction) {
            DB::table('transactions')->where('id', $transaction->id)->update([
                'workflow_status' => $transaction->status === 'selesai' ? 'Selesai' : 'Dipinjam',
            ]);
        }

        Schema::table('fines', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('payment_status', 30)->default('belum_dibayar');
        });

        foreach (DB::table('fines')->select('id', 'transaction_id', 'status')->orderBy('id')->get() as $fine) {
            $userId = DB::table('transactions')->where('id', $fine->transaction_id)->value('user_id');
            DB::table('fines')->where('id', $fine->id)->update([
                'user_id' => $userId,
                'payment_status' => $fine->status === 'lunas' ? 'lunas' : 'belum_dibayar',
            ]);
        }

        Schema::create('fine_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('method', ['qris', 'tunai']);
            $table->decimal('amount', 12, 2);
            $table->string('proof_path')->nullable();
            $table->enum('status', ['menunggu', 'terverifikasi', 'ditolak'])->default('menunggu');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_note')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });

        Schema::create('fine_payment_fine', function (Blueprint $table) {
            $table->foreignId('fine_payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fine_id')->constrained()->cascadeOnDelete();
            $table->primary(['fine_payment_id', 'fine_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fine_payment_fine');
        Schema::dropIfExists('fine_payments');

        Schema::table('fines', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'payment_status']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['returned_confirmed_by']);
            $table->dropColumn(['workflow_status', 'requested_at', 'approved_by', 'returned_confirmed_by']);
        });

        Schema::table('books', function (Blueprint $table) {
            $table->dropUnique(['kode_inventaris']);
            $table->dropColumn(['format', 'digital_url', 'preview_url', 'kode_inventaris']);
        });
    }
};
