<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Auth::user()->groups;
        return view('groups.index', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $group = Group::create([
            'name' => $request->name,
            'owner_id' => Auth::id(),
        ]);

        $group->users()->attach(Auth::id());

        return redirect()->route('groups.index')->with('success', 'Grupa utworzona!');
    }

    public function show(Group $group)
    {
        if (!$group->users->contains(Auth::id())) {
            abort(403, 'Brak dostępu.');
        }

        $group->load(['bills.payer', 'users']);
        return view('groups.show', compact('group'));
    }

    public function addUser(Request $request, Group $group)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $userToAdd = User::where('email', $request->email)->first();

        if ($group->users->contains($userToAdd->id)) {
            return back()->with('error', 'Ten użytkownik już jest w grupie!');
        }

        $group->users()->attach($userToAdd->id);
        return back()->with('success', 'Dodano użytkownika: ' . $userToAdd->name);
    }

    public function destroy(Group $group)
    {
        if ($group->owner_id !== Auth::id()) {
            abort(403);
        }
        $group->delete();
        return redirect()->route('groups.index')->with('success', 'Grupa usunięta.');
    }
}
