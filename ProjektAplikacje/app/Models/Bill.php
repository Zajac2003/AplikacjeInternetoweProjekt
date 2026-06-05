<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bill extends Model
{
    use HasFactory;

    // TO JEST KLUCZOWA LISTA:
    protected $fillable = [
        'group_id',
        'payer_id',
        'description', // Tego brakowało!
        'amount',
        'date'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function items()
    {
        return $this->hasMany(BillItem::class);
    }

    public function splits()
    {
        return $this->hasMany(BillSplit::class);
    }
}
