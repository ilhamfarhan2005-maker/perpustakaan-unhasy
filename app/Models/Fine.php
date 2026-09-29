<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    protected $fillable = ['transaction_id', 'amount', 'days_late', 'paid_at', 'paid_amount', 'status'];
    protected $casts = ['paid_at' => 'datetime'];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function isLunas(): bool
    {
        return $this->status === 'lunas';
    }
}
