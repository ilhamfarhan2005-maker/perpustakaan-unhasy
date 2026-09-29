<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'book_id', 'handled_by', 'borrow_date', 'due_date',
        'return_date', 'status', 'extension_count',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
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

    public function fine(): HasOne
    {
        return $this->hasOne(Fine::class);
    }

    public function sisaHari(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->due_date, false);
    }
}
