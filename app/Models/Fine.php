<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Fine extends Model
{
    use HasFactory;

    public const PAYMENT_STATUSES = ['belum_dibayar', 'menunggu_verifikasi', 'lunas', 'ditolak'];

    protected $fillable = [
        'transaction_id', 'user_id', 'amount', 'days_late', 'paid_at',
        'paid_amount', 'status', 'payment_status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(FinePayment::class, 'fine_payment_fine');
    }

    public function isLunas(): bool
    {
        return $this->payment_status === 'lunas' || $this->status === 'lunas';
    }
}
