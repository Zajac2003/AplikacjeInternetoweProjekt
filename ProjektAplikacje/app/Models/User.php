<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolumny, które można masowo zapisywać.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Dodano 'role' na potrzeby projektu na 5.0
    ];

    /**
     * Ukryte atrybuty (np. dla API).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Konwersja typów danych.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * RELACJA: Użytkownik należy do wielu grup rozliczeniowych.
     * To dzięki temu działa linia Auth::user()->groups w kontrolerze.
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
