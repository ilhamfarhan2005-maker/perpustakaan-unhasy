<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->string('workflow_status', 40)->default('Diajukan');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->timestamp('return_requested_at')->nullable();
        });

        DB::table('peminjamans')->where('status', 'dipinjam')->update(['workflow_status' => 'Dipinjam']);
        DB::table('peminjamans')->where('status', 'dikembalikan')->update(['workflow_status' => 'Selesai']);
        DB::table('peminjamans')->where('status', 'ditolak')->update(['workflow_status' => 'Ditolak']);

        foreach (DB::table('peminjamans')
            ->whereNotNull('transaction_id')
            ->whereNotNull('requested_return_at')
            ->get(['transaction_id', 'requested_return_at']) as $request) {
            DB::table('transactions')
                ->where('id', $request->transaction_id)
                ->update([
                    'return_requested_at' => $request->requested_return_at,
                    'workflow_status' => 'Menunggu Konfirmasi Kembali',
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropColumn('workflow_status');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('return_requested_at');
        });
    }
};
