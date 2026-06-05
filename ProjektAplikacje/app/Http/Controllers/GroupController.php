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
        $groups = auth()->user()->isAdmin()
            ? Group::with('owner')->get()
            : Auth::user()->groups;

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
        $this->authorizeGroupAccess($group);

        $group->load(['bills.payer', 'bills.items.users', 'bills.splits.user', 'users']);

        return view('groups.show', compact('group'));
    }

    public function edit(Group $group)
    {
        $this->authorizeGroupOwnerOrAdmin($group);

        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $this->authorizeGroupOwnerOrAdmin($group);

        $request->validate(['name' => 'required|string|max:255']);
        $group->update(['name' => $request->name]);

        return redirect()->route('groups.show', $group)->with('success', 'Grupa zaktualizowana.');
    }

    public function addUser(Request $request, Group $group)
    {
        $this->authorizeGroupAccess($group);

        $request->validate(['email' => 'required|email|exists:users,email']);

        $userToAdd = User::where('email', $request->email)->first();

        if ($group->users->contains($userToAdd->id)) {
            return back()->with('error', 'Ten uzytkownik juz jest w grupie!');
        }

        $group->users()->attach($userToAdd->id);

        return back()->with('success', 'Dodano uzytkownika: ' . $userToAdd->name);
    }

    public function destroy(Group $group)
    {
        $this->authorizeGroupOwnerOrAdmin($group);
        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Grupa usunieta.');
    }

    private function authorizeGroupAccess(Group $group): void
    {
        if (!auth()->user()->isAdmin() && !$group->users->contains(Auth::id())) {
            abort(403, 'Brak dostepu.');
        }
    }

    private function authorizeGroupOwnerOrAdmin(Group $group): void
    {
        if (!auth()->user()->isAdmin() && $group->owner_id !== Auth::id()) {
            abort(403);
        }
    }
}
