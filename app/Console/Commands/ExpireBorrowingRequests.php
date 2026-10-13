<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireBorrowingRequests extends Command
{
    protected $signature = 'peminjaman:expire-requests';

    protected $description = 'Kedaluwarsakan pengajuan peminjaman yang belum diproses selama satu hari';

    public function handle(): int
    {
        $expirationTime = now()->subDay();
        $expiredCount = 0;

        DB::transaction(function () use ($expirationTime, &$expiredCount) {
            Peminjaman::query()
                ->where('status', 'pending')
                ->where('created_at', '<=', $expirationTime)
                ->orderBy('id')
                ->chunkById(100, function ($requests) use ($expirationTime, &$expiredCount) {
                    foreach ($requests as $request) {
                        $lockedRequest = Peminjaman::whereKey($request->id)
                            ->lockForUpdate()
                            ->first();

                        if (! $lockedRequest
                            || $lockedRequest->status !== 'pending'
                            || $lockedRequest->created_at->gt($expirationTime)) {
                            continue;
                        }

                        $lockedRequest->update([
                            'status' => 'ditolak',
                            'workflow_status' => 'Kedaluwarsa',
                        ]);
                        $expiredCount++;
                    }
                });
        });

        $this->info("{$expiredCount} pengajuan kedaluwarsa.");

        return self::SUCCESS;
    }
}
