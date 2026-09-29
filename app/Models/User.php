<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','email_verified_at','password','role','nim_nip','no_hp','status'];
    protected $hidden = ['password','remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isPustakawan(): bool { return $this->role === 'pustakawan'; }
    public function isMahasiswa(): bool { return $this->role === 'mahasiswa'; }
    public function isKepalaPerpustakaan(): bool { return $this->role === 'kepala_perpustakaan'; }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
