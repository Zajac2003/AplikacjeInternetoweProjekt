<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    protected $fillable = ['bill_id', 'name', 'price', 'quantity'];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'bill_item_user');
    }
}
