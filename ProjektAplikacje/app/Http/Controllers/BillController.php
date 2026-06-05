<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    public function store(Request $request, Group $group)
    {
        $this->authorizeGroupAccess($group);

        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'payer_id' => 'required|exists:users,id',
        ]);

        abort_unless($group->users->contains($request->payer_id), 422, 'Platnik musi byc czlonkiem grupy.');

        $bill = $group->bills()->create([
            'description' => $request->description,
            'amount' => $request->amount,
            'payer_id' => $request->payer_id,
            'date' => now(),
        ]);

        $this->createEqualSplits($bill, $group, (int) $request->payer_id);

        if (DB::getDriverName() !== 'mysql') {
            $group->increment('total_amount', $request->amount);
        }

        return back()->with('success', 'Wydatek dodany!');
    }

    public function destroy(Group $group, Bill $bill)
    {
        $this->authorizeGroupAccess($group);
        abort_unless($bill->group_id === $group->id, 404);

        $amount = $bill->amount;
        $bill->delete();

        if (DB::getDriverName() !== 'mysql') {
            $group->decrement('total_amount', $amount);
        }

        return back()->with('success', 'Rachunek usuniety.');
    }

    private function createEqualSplits(Bill $bill, Group $group, int $payerId): void
    {
        $members = $group->users;
        if ($members->isEmpty()) {
            return;
        }

        $share = round($bill->amount / $members->count(), 2);

        foreach ($members as $member) {
            $bill->splits()->create([
                'user_id' => $member->id,
                'amount' => $share,
                'is_paid' => $member->id === $payerId,
            ]);
        }
    }

    private function authorizeGroupAccess(Group $group): void
    {
        if (!auth()->user()->isAdmin() && !$group->users->contains(Auth::id())) {
            abort(403, 'Brak dostepu.');
        }
    }
}
