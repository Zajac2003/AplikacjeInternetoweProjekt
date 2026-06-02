<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    public function store(Request $request, Group $group)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $group->bills()->create([
            'description' => $request->description,
            'amount' => $request->amount,
            'payer_id' => Auth::id(),
            'date' => now(),
        ]);

        return back()->with('success', 'Wydatek dodany!');
    }
}
