<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillItemController extends Controller
{
    public function store(Request $request, Group $group, Bill $bill)
    {
        $this->authorizeGroupAccess($group);
        abort_unless($bill->group_id === $group->id, 404);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'quantity' => 'required|integer|min:1',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $item = $bill->items()->create([
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
        ]);

        $item->users()->attach($request->user_ids);

        return back()->with('success', 'Pozycja z paragonu dodana.');
    }

    private function authorizeGroupAccess(Group $group): void
    {
        if (!auth()->user()->isAdmin() && !$group->users->contains(Auth::id())) {
            abort(403, 'Brak dostepu.');
        }
    }
}
