<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FinePayment extends Model
{
    public const STATUSES = ['menunggu', 'terverifikasi', 'ditolak'];

    protected $fillable = [
        'user_id', 'method', 'amount', 'proof_path', 'status',
        'verified_by', 'verified_at', 'rejection_note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function fines(): BelongsToMany
    {
        return $this->belongsToMany(Fine::class, 'fine_payment_fine');
    }
}
