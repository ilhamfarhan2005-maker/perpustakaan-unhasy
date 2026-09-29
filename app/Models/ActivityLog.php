<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'description', 'ip_address'];

    public static function record(string $action, ?string $desc = null): void
    {
        static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $desc,
            'ip_address' => request()->ip(),
        ]);
    }
}
