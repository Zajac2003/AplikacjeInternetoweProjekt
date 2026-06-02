<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'owner_id', 'total_amount'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * ZŁOŻONA FUNKCJA SKŁADOWA: Obliczanie salda każdego członka.
     * To jest logika, która "rozlicza" wycieczkę.
     */
    public function getBalances()
    {
        $members = $this->users;
        $totalSpent = $this->bills->sum('amount'); // Suma wszystkich rachunków
        $memberCount = $members->count();

        if ($memberCount === 0) return [];

        $fairShare = $totalSpent / $memberCount; // Tyle sprawiedliwie przypada na osobę
        $balances = [];

        foreach ($members as $user) {
            // Ile dany użytkownik faktycznie wyłożył z portfela
            $userPaid = $this->bills->where('payer_id', $user->id)->sum('amount');

            $balances[] = [
                'user' => $user,
                'paid' => $userPaid,
                'balance' => $userPaid - $fairShare // Wynik dodatni = ktoś ma mu oddać, ujemny = on musi oddać
            ];
        }

        return $balances;
    }
}
