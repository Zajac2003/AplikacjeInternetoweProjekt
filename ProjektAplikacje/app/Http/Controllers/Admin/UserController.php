<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,admin',
            'name' => 'required|string|max:255',
        ]);

        $user->update([
            'name' => $request->name,
            'role' => $request->role,
        ]);

        return back()->with('success', 'Profil uzytkownika zaktualizowany.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Nie mozesz usunac wlasnego konta.');
        }

        $user->delete();

        return back()->with('success', 'Uzytkownik usuniety.');
    }
}
