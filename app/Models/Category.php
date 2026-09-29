<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::creating(function ($category) {
            $category->slug ??= Str::slug($category->name);
        });
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
