<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Algorytm bilansowania: zaplacone - naleznosci (bill_splits).
     * Na MySQL korzysta z funkcji skladowej get_user_net_balance().
     */
    public function getBalances()
    {
        $members = $this->users;
        $balances = [];

        foreach ($members as $user) {
            $paid = $this->bills->where('payer_id', $user->id)->sum('amount');
            $owed = BillSplit::whereIn('bill_id', $this->bills->pluck('id'))
                ->where('user_id', $user->id)
                ->sum('amount');

            if (DB::getDriverName() === 'mysql') {
                $balance = (float) DB::selectOne(
                    'SELECT get_user_net_balance(?, ?) AS balance',
                    [$user->id, $this->id]
                )->balance;
            } else {
                $balance = $paid - $owed;
            }

            $balances[] = [
                'user' => $user,
                'paid' => $paid,
                'owed' => $owed,
                'balance' => $balance,
            ];
        }

        return $balances;
    }
}
