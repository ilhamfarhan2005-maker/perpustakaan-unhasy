<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;

    public const WORKFLOW_STATUSES = [
        'Diajukan',
        'Dipinjam',
        'Menunggu Konfirmasi Kembali',
        'Selesai',
        'Ditolak',
        'Kedaluwarsa',
    ];

    protected $fillable = [
        'user_id', 'book_id', 'handled_by', 'borrow_date', 'due_date',
        'return_date', 'status', 'extension_count', 'workflow_status',
        'requested_at', 'return_requested_at', 'approved_by', 'returned_confirmed_by',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'requested_at' => 'datetime',
        'return_requested_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function returnConfirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_confirmed_by');
    }

    public function fine(): HasOne
    {
        return $this->hasOne(Fine::class);
    }

    public function studentRequest(): HasOne
    {
        return $this->hasOne(Peminjaman::class);
    }

    public function sisaHari(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->due_date, false);
    }
}
