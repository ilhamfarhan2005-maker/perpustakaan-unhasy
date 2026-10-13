<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'category_id', 'title', 'isbn', 'author', 'publisher', 'year',
        'rak_location', 'stock', 'cover_image', 'qr_code', 'format',
        'digital_url', 'preview_url', 'kode_inventaris',
        'file_path', 'preview_path', 'preview_pages', 'file_size',
    ];

    public const FORMATS = ['fisik', 'digital', 'fisik_digital'];

    protected static function booted(): void
    {
        static::created(function (Book $book) {
            if (blank($book->kode_inventaris)) {
                $book->forceFill([
                    'kode_inventaris' => 'UNHASY-BK-'.str_pad((string) $book->id, 6, '0', STR_PAD_LEFT),
                ])->saveQuietly();
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function isAvailable(): bool
    {
        return $this->hasPhysicalCopy() && $this->stock > 0;
    }

    public function hasPhysicalCopy(): bool
    {
        return in_array($this->format ?? 'fisik', ['fisik', 'fisik_digital'], true);
    }
}
