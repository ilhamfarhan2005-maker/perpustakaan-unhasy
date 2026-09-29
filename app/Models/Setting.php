<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public const KEYS = [
        'fine_per_day' => 'Tarif denda per hari (Rp)',
        'max_borrow_per_user' => 'Batas maksimal peminjaman per anggota',
        'borrow_duration_days' => 'Durasi standar peminjaman (hari)',
        'max_extension' => 'Batas maksimal perpanjangan',
    ];

    public static function get(string $key, $default = null): mixed
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.{$key}");
    }
}
